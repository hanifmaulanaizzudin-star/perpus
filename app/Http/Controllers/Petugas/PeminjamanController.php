<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Buku;
use App\Models\DetailPeminjaman;
use App\Models\Peminjaman;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeminjamanController extends Controller
{
    // 1. Tampilkan Daftar Transaksi Peminjaman
    public function index(Request $request)
    {
        $peminjaman = Peminjaman::with(['anggota', 'petugas', 'detailPeminjaman.buku'])
            ->when($request->search, function ($query, $search) {
                $query->where('kode_peminjaman', 'like', "%{$search}%")
                    ->orWhereHas('anggota', function ($q) use ($search) {
                        $q->where('nama_lengkap', 'like', "%{$search}%")
                          ->orWhere('nomor_anggota', 'like', "%{$search}%");
                    });
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('petugas.peminjaman.index', compact('peminjaman'));
    }

    // 2. Form Transaksi Baru (Ada Scanner Barcode)
    public function create()
    {
        // Hanya anggota yang aktif
        $anggota = Anggota::where('status_keanggotaan', 'aktif')->orderBy('nama_lengkap')->get();

        // Daftar buku untuk pilihan manual (jika tidak discan)
        $buku = Buku::where('status', 'tersedia')
            ->where('stok_tersedia', '>', 0)
            ->orderBy('judul')
            ->get();

        // Default tanggal pinjam hari ini & tenggat kembali 7 hari ke depan
        $tanggalPinjam = Carbon::today()->format('Y-m-d');
        $tanggalJatuhTempo = Carbon::today()->addDays(7)->format('Y-m-d');

        return view('petugas.peminjaman.create', compact('anggota', 'buku', 'tanggalPinjam', 'tanggalJatuhTempo'));
    }

    // 3. API Pencarian Buku Berdasarkan Scan Barcode
    public function cariBuku(Request $request)
    {
        $kode = trim($request->query('kode', ''));

        if (empty($kode)) {
            return response()->json([
                'success' => false,
                'message' => 'Kode barcode tidak boleh kosong.',
            ], 400);
        }

        // Cari berdasarkan kode_buku atau isbn
        $buku = Buku::with('kategori')
            ->where('kode_buku', $kode)
            ->orWhere('isbn', $kode)
            ->first();

        if (!$buku) {
            return response()->json([
                'success' => false,
                'message' => "Buku dengan barcode/kode '{$kode}' tidak ditemukan di sistem.",
            ], 404);
        }

        if ($buku->status !== 'tersedia') {
            return response()->json([
                'success' => false,
                'message' => "Buku '{$buku->judul}' saat ini berstatus nonaktif.",
            ], 422);
        }

        if ($buku->stok_tersedia < 1) {
            return response()->json([
                'success' => false,
                'message' => "Stok buku '{$buku->judul}' di rak sedang habis (0).",
            ], 422);
        }

        return response()->json([
            'success' => true,
            'buku' => [
                'id'            => $buku->id,
                'kode_buku'     => $buku->kode_buku,
                'judul'         => $buku->judul,
                'penulis'       => $buku->penulis ?? '-',
                'kategori'      => $buku->kategori->nama_kategori ?? 'Umum',
                'lokasi_rak'    => $buku->lokasi_rak ?? '-',
                'stok_tersedia' => $buku->stok_tersedia,
                'gambar_sampul' => $buku->gambar_sampul ? asset('storage/' . $buku->gambar_sampul) : null,
            ],
        ]);
    }

    // 4. API Pencarian Anggota Berdasarkan Scan Barcode Kartu Siswa
    public function cariAnggota(Request $request)
    {
        $nomor = trim($request->query('nomor', ''));

        if (empty($nomor)) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor barcode anggota tidak boleh kosong.',
            ], 400);
        }

        $anggota = Anggota::where('nomor_induk', $nomor)
            ->orWhere('nama_pengguna', $nomor)
            ->first();

        if (!$anggota) {
            return response()->json([
                'success' => false,
                'message' => "Anggota dengan nomor barcode '{$nomor}' tidak ditemukan.",
            ], 404);
        }

        if ($anggota->status_keanggotaan !== 'aktif') {
            return response()->json([
                'success' => false,
                'message' => "Anggota '{$anggota->nama_lengkap}' saat ini berstatus {$anggota->status_keanggotaan} (tidak aktif).",
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Anggota berhasil ditemukan.',
            'anggota' => [
                'id'           => $anggota->id,
                'nomor_induk'  => $anggota->nomor_induk,
                'nama_lengkap' => $anggota->nama_lengkap,
                'kelas'        => $anggota->kelas ?? '-',
                'jurusan'      => $anggota->jurusan ?? '-',
            ],
        ]);
    }

    // 5. Simpan Transaksi Peminjaman
    public function store(Request $request)
    {
        $request->validate([
            'anggota_id'          => 'required|exists:anggota,id',
            'buku_id'             => 'required|array|min:1',
            'buku_id.*'           => 'required|exists:buku,id',
            'tanggal_pinjam'      => 'required|date',
            'tanggal_jatuh_tempo' => 'required|date|after_or_equal:tanggal_pinjam',
            'catatan'             => 'nullable|string',
        ], [
            'buku_id.required'    => 'Silakan scan atau pilih minimal 1 buku yang ingin dipinjam.',
            'tanggal_jatuh_tempo.after_or_equal' => 'Tanggal jatuh tempo tidak boleh sebelum tanggal pinjam.',
        ]);

        // Cek duplikasi buku
        if (count($request->buku_id) !== count(array_unique($request->buku_id))) {
            return back()->withInput()->with('error', 'Buku yang sama tidak boleh dipinjam lebih dari 1 kali dalam 1 transaksi.');
        }

        // Validasi status keaktifan anggota & tanggungan buku
        $anggota = Anggota::findOrFail($request->anggota_id);
        if ($anggota->status_keanggotaan !== 'aktif') {
            return back()->withInput()->with('error', "Peminjaman ditolak: Anggota '{$anggota->nama_lengkap}' berstatus '{$anggota->status_keanggotaan}'. Kartu anggota tidak dapat digunakan.");
        }

        $adaKeterlambatan = $anggota->peminjaman()
            ->where('status', 'terlambat')
            ->exists();
        if ($adaKeterlambatan) {
            return back()->withInput()->with('error', "Peminjaman ditolak: Anggota '{$anggota->nama_lengkap}' masih memiliki pinjaman buku yang berstatus terlambat. Selesaikan pengembalian terlebih dahulu.");
        }

        DB::beginTransaction();

        try {
            // Generate Kode Peminjaman: PJ-YYYYMMDD-0001
            $today = Carbon::today()->format('Ymd');
            $countToday = Peminjaman::whereDate('tanggal_pinjam', Carbon::today())->count() + 1;
            $kodePeminjaman = 'PJ-' . $today . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);

            // Simpan Data Transaksi
            $peminjaman = Peminjaman::create([
                'kode_peminjaman'     => $kodePeminjaman,
                'anggota_id'          => $request->anggota_id,
                'petugas_id'          => auth('petugas')->id(),
                'tanggal_pinjam'      => $request->tanggal_pinjam,
                'tanggal_jatuh_tempo' => $request->tanggal_jatuh_tempo,
                'status'              => 'dipinjam',
                'catatan'             => $request->catatan,
            ]);

            // Simpan Detail & Kurangi Stok
            foreach ($request->buku_id as $bukuId) {
                $buku = Buku::lockForUpdate()->find($bukuId);

                if ($buku->stok_tersedia < 1) {
                    throw new \Exception("Stok buku '{$buku->judul}' sedang tidak tersedia.");
                }

                DetailPeminjaman::create([
                    'peminjaman_id'       => $peminjaman->id,
                    'buku_id'             => $bukuId,
                    'jumlah_pinjam'       => 1,
                    'jumlah_dikembalikan' => 0,
                ]);

                // Kurangi stok tersedia buku
                $buku->decrement('stok_tersedia', 1);
            }

            DB::commit();

            return redirect()
                ->route('petugas.peminjaman.show', $peminjaman->id)
                ->with('success', "Transaksi {$kodePeminjaman} berhasil dibuat!");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memproses peminjaman: ' . $e->getMessage());
        }
    }

    // 5. Detail Transaksi Peminjaman
    public function show(Peminjaman $peminjaman)
    {
        $peminjaman->load(['anggota', 'petugas', 'detailPeminjaman.buku.kategori']);
        return view('petugas.peminjaman.show', compact('peminjaman'));
    }

    // 6. Blast Pesan WhatsApp Otomatis ke Semua yang Terlambat
    public function blastWaOtomatis()
    {
        $today = Carbon::today()->startOfDay();

        // Update status pinjaman yang lewat jatuh tempo
        Peminjaman::where('status', 'dipinjam')
            ->whereDate('tanggal_jatuh_tempo', '<', $today)
            ->update(['status' => 'terlambat']);

        $pinjamanTerlambat = Peminjaman::with(['anggota', 'detailPeminjaman.buku'])
            ->where('status', 'terlambat')
            ->get();

        if ($pinjamanTerlambat->isEmpty()) {
            return back()->with('success', 'Tidak ada transaksi peminjaman yang berstatus terlambat saat ini. Semua peminjam tertib!');
        }

        $terkirim = 0;
        $tanpaHp = 0;

        foreach ($pinjamanTerlambat as $item) {
            $anggota = $item->anggota;
            if (!$anggota || empty($anggota->no_telepon)) {
                $tanpaHp++;
                continue;
            }

            $bukuList = $item->detailPeminjaman->map(fn($d) => $d->buku->judul ?? 'Buku')->implode(', ');
            $jatuhTempo = Carbon::parse($item->tanggal_jatuh_tempo)->translatedFormat('d F Y');
            $hariTelat = $today->diffInDays(Carbon::parse($item->tanggal_jatuh_tempo)->startOfDay());

            $pesan = "Halo {$anggota->nama_lengkap} (NISN: {$anggota->nomor_induk}),\n\n"
                   . "Kami dari *Perpustakaan PerpusKu* ingin menginformasikan bahwa pinjaman buku:\n"
                   . "📖 *{$bukuList}*\n"
                   . "🔖 Kode Pinjam: *{$item->kode_peminjaman}*\n"
                   . "⚠️ *STATUS: TERLAMBAT ({$hariTelat} Hari)*\n"
                   . "📅 Jatuh Tempo: {$jatuhTempo}\n\n"
                   . "Mohon untuk segera mengembalikan buku tersebut ke meja sirkulasi perpustakaan untuk menghindari akumulasi denda harian.\n\n"
                   . "Terima kasih atas kerja samanya! 🙏\n"
                   . "_Pesan otomatis dari Sistem PerpusKu Digital_";

            $res = \App\Services\WhatsAppService::kirimPesan($anggota->no_telepon, $pesan);
            if ($res['success']) {
                $terkirim++;
            }
        }

        $infoTambahan = ($tanpaHp > 0) ? " ({$tanpaHp} murid dilewati karena belum memasukkan nomor HP)" : "";
        return back()->with('success', "⚡ Pengiriman WA Otomatis Berhasil! Pesan tagihan telah dikirimkan ke {$terkirim} murid yang terlambat{$infoTambahan}.");
    }
}

