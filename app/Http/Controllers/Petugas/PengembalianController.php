<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\DetailPeminjaman;
use App\Models\DetailPengembalian;
use App\Models\Peminjaman;
use App\Models\PengaturanPerpustakaan;
use App\Models\Pengembalian;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengembalianController extends Controller
{
    // 1. Tampilkan Daftar Transaksi Pengembalian
    public function index(Request $request)
    {
        $pengembalian = Pengembalian::with([
            'peminjaman.anggota',
            'petugas',
            'detailPengembalian.detailPeminjaman.buku'
        ])
            ->when($request->search, function ($query, $search) {
                $query->where('kode_pengembalian', 'like', "%{$search}%")
                    ->orWhereHas('peminjaman', function ($q) use ($search) {
                        $q->where('kode_peminjaman', 'like', "%{$search}%")
                          ->orWhereHas('anggota', function ($qa) use ($search) {
                              $qa->where('nama_lengkap', 'like', "%{$search}%");
                          });
                    });
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('petugas.pengembalian.index', compact('pengembalian'));
    }

    // 1.1 API Cari Pinjaman via Barcode/QR (Struk PJ / Nomor Induk Siswa / Barcode Buku)
    public function cariPinjaman(Request $request)
    {
        $query = trim($request->get('kode', ''));
        if (empty($query)) {
            return response()->json(['success' => false, 'message' => 'Kode scan kosong.']);
        }

        $peminjaman = Peminjaman::with(['anggota', 'detailPeminjaman.buku'])
            ->whereIn('status', ['dipinjam', 'terlambat'])
            ->where(function ($q) use ($query) {
                $q->where('kode_peminjaman', $query)
                  ->orWhereHas('anggota', function ($qa) use ($query) {
                      $qa->where('nomor_induk', $query);
                  })
                  ->orWhereHas('detailPeminjaman.buku', function ($qb) use ($query) {
                      $qb->where('kode_buku', $query);
                  });
            })
            ->first();

        if ($peminjaman) {
            return response()->json([
                'success'         => true,
                'peminjaman_id'   => $peminjaman->id,
                'kode_peminjaman' => $peminjaman->kode_peminjaman,
                'nama_anggota'    => $peminjaman->anggota->nama_lengkap ?? 'Anggota',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => "Tidak ada peminjaman aktif yang cocok dengan barcode '{$query}'.",
        ]);
    }

    // 2. Form Proses Pengembalian Buku
    public function create(Request $request)
    {
        // Ambil daftar pinjaman yang statusnya masih 'dipinjam' atau 'terlambat'
        $peminjamanAktif = Peminjaman::with(['anggota', 'detailPeminjaman.buku'])
            ->whereIn('status', ['dipinjam', 'terlambat'])
            ->latest('id')
            ->get();

        $selectedPeminjaman = null;
        $hariKeterlambatan = 0;
        $totalDenda = 0;

        $pengaturan = PengaturanPerpustakaan::first();
        $tarifDendaPerHari = $pengaturan->denda_per_hari ?? 1000;

        if ($request->has('peminjaman_id')) {
            $selectedPeminjaman = Peminjaman::with(['anggota', 'detailPeminjaman.buku'])
                ->whereIn('status', ['dipinjam', 'terlambat'])
                ->find($request->peminjaman_id);

            if ($selectedPeminjaman) {
                $jatuhTempo = Carbon::parse($selectedPeminjaman->tanggal_jatuh_tempo)->startOfDay();
                $hariIni = Carbon::today()->startOfDay();

                if ($hariIni->greaterThan($jatuhTempo)) {
                    $hariKeterlambatan = $hariIni->diffInDays($jatuhTempo);
                    $totalBuku = $selectedPeminjaman->detailPeminjaman->sum('jumlah_pinjam');
                    $totalDenda = $hariKeterlambatan * $tarifDendaPerHari * $totalBuku;
                }
            }
        }

        return view('petugas.pengembalian.create', compact(
            'peminjamanAktif',
            'selectedPeminjaman',
            'hariKeterlambatan',
            'totalDenda',
            'tarifDendaPerHari'
        ));
    }

    // 3. Simpan Pengembalian Buku
    public function store(Request $request)
    {
        $request->validate([
            'peminjaman_id'     => 'required|exists:peminjaman,id',
            'kondisi_buku'      => 'required|array',
            'kondisi_buku.*'    => 'required|in:baik,rusak_ringan,rusak_berat,hilang',
            'biaya_ganti_rugi'  => 'nullable|array',
            'metode_ganti_rugi' => 'nullable|array',
            'catatan'           => 'nullable|string',
        ]);

        $peminjaman = Peminjaman::with('detailPeminjaman.buku')
            ->whereIn('status', ['dipinjam', 'terlambat'])
            ->findOrFail($request->peminjaman_id);

        DB::beginTransaction();

        try {
            // Generate Kode Pengembalian: KB-YYYYMMDD-0001
            $today = Carbon::today()->format('Ymd');
            $countToday = Pengembalian::whereDate('tanggal_pengembalian', Carbon::today())->count() + 1;
            $kodePengembalian = 'KB-' . $today . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);

            // 1. Hitung denda keterlambatan jika ada
            $jatuhTempo = Carbon::parse($peminjaman->tanggal_jatuh_tempo)->startOfDay();
            $hariIni = Carbon::today()->startOfDay();
            $totalDenda = 0;
            $hariKeterlambatan = 0;
            $dendaInfo = '';

            if ($hariIni->greaterThan($jatuhTempo)) {
                $hariKeterlambatan = $hariIni->diffInDays($jatuhTempo);
                $pengaturan = PengaturanPerpustakaan::first();
                $tarifDendaPerHari = $pengaturan->denda_per_hari ?? 1000;
                $totalBuku = $peminjaman->detailPeminjaman->sum('jumlah_pinjam');
                $totalDenda = $hariKeterlambatan * $tarifDendaPerHari * $totalBuku;
                $statusBayar = $request->input('status_pembayaran_denda', 'Lunas Dibayar Tunai');

                $dendaInfo = "Denda Keterlambatan: Terlambat {$hariKeterlambatan} hari x Rp " . number_format($tarifDendaPerHari, 0, ',', '.') . " = Rp " . number_format($totalDenda, 0, ',', '.') . " ({$statusBayar})";
            }

            // 2. Evaluasi Kondisi Buku, Buku Hilang, dan Ganti Rugi
            $jumlahHilang = 0;
            $jumlahRusakBerat = 0;
            $totalGantiRugi = 0;
            $gantiRugiLogs = [];

            foreach ($peminjaman->detailPeminjaman as $detail) {
                $kondisi = $request->kondisi_buku[$detail->id] ?? 'baik';
                $biaya = isset($request->biaya_ganti_rugi[$detail->id]) ? (float) $request->biaya_ganti_rugi[$detail->id] : 0;
                $metode = $request->metode_ganti_rugi[$detail->id] ?? 'Ganti Uang Tunai';

                if ($kondisi === 'hilang') {
                    $jumlahHilang++;
                    $totalGantiRugi += $biaya;
                    $bukuJudul = $detail->buku->judul ?? 'Buku';
                    $gantiRugiLogs[] = "Buku '{$bukuJudul}' [HILANG] (Biaya: Rp " . number_format($biaya, 0, ',', '.') . " | Metode: {$metode})";
                } elseif ($kondisi === 'rusak_berat') {
                    $jumlahRusakBerat++;
                    $totalGantiRugi += $biaya;
                    $bukuJudul = $detail->buku->judul ?? 'Buku';
                    $gantiRugiLogs[] = "Buku '{$bukuJudul}' [RUSAK BERAT] (Biaya: Rp " . number_format($biaya, 0, ',', '.') . " | Metode: {$metode})";
                }
            }

            // 3. Susun Catatan Header Rekapitulasi
            $rekapLines = [];
            if ($jumlahHilang > 0) {
                $rekapLines[] = "[PENYELESAIAN BUKU HILANG: {$jumlahHilang} Buku]";
            } elseif ($jumlahRusakBerat > 0) {
                $rekapLines[] = "[PENYELESAIAN BUKU RUSAK]";
            } else {
                $rekapLines[] = "[PENGEMBALIAN BUKU: Semua Kembali Baik]";
            }

            if (!empty($gantiRugiLogs)) {
                $rekapLines[] = implode("\n", $gantiRugiLogs);
                $rekapLines[] = "Total Ganti Rugi: Rp " . number_format($totalGantiRugi, 0, ',', '.');
            }

            if (!empty($dendaInfo)) {
                $rekapLines[] = $dendaInfo;
            }

            $totalSemuaTagihan = $totalDenda + $totalGantiRugi;
            if ($totalSemuaTagihan > 0) {
                $rekapLines[] = "TOTAL KEUANGAN YANG DIBAYAR: Rp " . number_format($totalSemuaTagihan, 0, ',', '.');
            }

            if (!empty($request->catatan)) {
                $rekapLines[] = "Keterangan Petugas: " . $request->catatan;
            }

            $catatanFinal = implode("\n", $rekapLines);

            // 4. Simpan Header Pengembalian
            $pengembalian = Pengembalian::create([
                'kode_pengembalian'    => $kodePengembalian,
                'peminjaman_id'        => $peminjaman->id,
                'petugas_id'           => auth('petugas')->id(),
                'tanggal_pengembalian' => now(),
                'catatan'              => !empty($catatanFinal) ? $catatanFinal : null,
            ]);

            // 5. Simpan Detail Pengembalian & Kembalikan Stok Buku
            foreach ($peminjaman->detailPeminjaman as $detail) {
                $kondisi = $request->kondisi_buku[$detail->id] ?? 'baik';
                $biaya = isset($request->biaya_ganti_rugi[$detail->id]) ? (float) $request->biaya_ganti_rugi[$detail->id] : 0;
                $metode = $request->metode_ganti_rugi[$detail->id] ?? null;
                $catatanManual = $request->catatan_buku[$detail->id] ?? '';

                $detailNoteArr = [];
                if ($kondisi === 'hilang') {
                    $detailNoteArr[] = "BUKU HILANG: Ganti Rugi Rp " . number_format($biaya, 0, ',', '.') . " ({$metode})";
                } elseif ($kondisi === 'rusak_berat') {
                    $detailNoteArr[] = "RUSAK BERAT: Ganti Rugi Rp " . number_format($biaya, 0, ',', '.') . " ({$metode})";
                }
                if (!empty($catatanManual)) {
                    $detailNoteArr[] = $catatanManual;
                }

                DetailPengembalian::create([
                    'pengembalian_id'      => $pengembalian->id,
                    'detail_peminjaman_id' => $detail->id,
                    'jumlah_dikembalikan'  => $detail->jumlah_pinjam,
                    'kondisi_buku'         => $kondisi,
                    'biaya_ganti_rugi'     => (float) $biaya,
                    'catatan'              => !empty($detailNoteArr) ? implode(' | ', $detailNoteArr) : null,
                ]);

                // Update jumlah dikembalikan di detail peminjaman
                $detail->update(['jumlah_dikembalikan' => $detail->jumlah_pinjam]);

                // Jika buku kondisi baik / rusak ringan, kembalikan stok buku ke rak
                if (in_array($kondisi, ['baik', 'rusak_ringan'])) {
                    Buku::where('id', $detail->buku_id)->increment('stok_tersedia', $detail->jumlah_pinjam);
                }
            }

            // 6. Update Status Peminjaman Menjadi 'dikembalikan'
            $peminjaman->update(['status' => 'dikembalikan']);

            DB::commit();

            // Flash message yang sesuai logika
            if ($jumlahHilang > 0) {
                $flashMsg = "Penyelesaian {$jumlahHilang} buku hilang berhasil diproses! Transaksi {$kodePengembalian} tercatat di sistem.";
            } elseif ($jumlahRusakBerat > 0) {
                $flashMsg = "Penyelesaian buku rusak berhasil diproses! Transaksi {$kodePengembalian} tercatat di sistem.";
            } else {
                $flashMsg = "Semua buku berhasil dikembalikan dalam kondisi baik! Transaksi pengembalian {$kodePengembalian} tercatat.";
            }

            return redirect()
                ->route('petugas.pengembalian.show', $pengembalian->id)
                ->with('success', $flashMsg);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memproses pengembalian: ' . $e->getMessage());
        }
    }

    // 4. Detail Transaksi Pengembalian
    public function show(Pengembalian $pengembalian)
    {
        $pengembalian->load([
            'peminjaman.anggota',
            'petugas',
            'detailPengembalian.detailPeminjaman.buku'
        ]);

        return view('petugas.pengembalian.show', compact('pengembalian'));
    }
}

