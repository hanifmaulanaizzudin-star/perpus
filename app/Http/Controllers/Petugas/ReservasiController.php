<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\DetailPeminjaman;
use App\Models\Peminjaman;
use App\Models\PengaturanPerpustakaan;
use App\Models\ReservasiBuku;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservasiController extends Controller
{
    // 1. Tampilkan Daftar Reservasi & Antrean Buku
    public function index(Request $request)
    {
        $statusFilter = $request->query('status');
        $search = trim($request->query('search', ''));

        // Query Utama
        $reservasiQuery = ReservasiBuku::with(['anggota', 'buku.kategori', 'buku.rak'])
            ->latest('id');

        if (!empty($statusFilter)) {
            $reservasiQuery->where('status', $statusFilter);
        }

        if (!empty($search)) {
            $reservasiQuery->where(function ($q) use ($search) {
                $q->where('kode_reservasi', 'like', "%{$search}%")
                  ->orWhereHas('anggota', function ($qa) use ($search) {
                      $qa->where('nama_lengkap', 'like', "%{$search}%")
                         ->orWhere('nomor_induk', 'like', "%{$search}%");
                  })
                  ->orWhereHas('buku', function ($qb) use ($search) {
                      $qb->where('judul', 'like', "%{$search}%")
                         ->orWhere('kode_buku', 'like', "%{$search}%");
                  });
            });
        }

        $reservasi = $reservasiQuery->paginate(15)->withQueryString();

        // Hitung Statistik
        $counts = [
            'total'       => ReservasiBuku::count(),
            'menunggu'    => ReservasiBuku::where('status', 'menunggu')->count(),
            'tersedia'    => ReservasiBuku::where('status', 'tersedia')->count(),
            'selesai'     => ReservasiBuku::where('status', 'selesai')->count(),
            'dibatalkan'  => ReservasiBuku::where('status', 'dibatalkan')->count(),
            'kedaluwarsa' => ReservasiBuku::where('status', 'kedaluwarsa')->count(),
        ];

        return view('petugas.reservasi.index', compact('reservasi', 'counts', 'statusFilter', 'search'));
    }

    // 2. Realisasi Pengambilan Buku (Convert Reservasi -> Peminjaman)
    public function prosesAmbil(ReservasiBuku $reservasi)
    {
        if (!in_array($reservasi->status, ['tersedia', 'menunggu'])) {
            return back()->with('error', "Reservasi {$reservasi->kode_reservasi} berstatus '{$reservasi->status}' dan tidak dapat diproses.");
        }

        $anggota = $reservasi->anggota;
        $buku = $reservasi->buku;

        if (!$anggota || $anggota->status !== 'aktif') {
            return back()->with('error', 'Anggota tidak aktif atau tidak ditemukan.');
        }

        // Cek denda / pinjaman terlambat
        $adaTerlambat = Peminjaman::where('anggota_id', $anggota->id)
            ->where('status', 'terlambat')
            ->exists();
        if ($adaTerlambat) {
            return back()->with('error', "Siswa {$anggota->nama_lengkap} masih memiliki pinjaman terlambat. Selesaikan terlebih dahulu.");
        }

        $pengaturan = PengaturanPerpustakaan::first();
        $durasiHari = $pengaturan->lama_peminjaman_hari ?? 7;
        $maxBuku = $pengaturan->maksimal_buku_pinjam ?? 3;

        // Cek kuota pinjam saat ini
        $totalBukuSedangDipinjam = DetailPeminjaman::whereHas('peminjaman', function ($q) use ($anggota) {
            $q->where('anggota_id', $anggota->id)
              ->whereIn('status', ['dipinjam', 'terlambat']);
        })->sum('jumlah_pinjam');

        if ($totalBukuSedangDipinjam >= $maxBuku) {
            return back()->with('error', "Siswa {$anggota->nama_lengkap} sudah mencapai batas maksimal pinjaman ({$maxBuku} buku).");
        }

        DB::beginTransaction();
        try {
            // Generate Kode Pinjam: PJ-YYYYMMDD-0001
            $today = Carbon::today()->format('Ymd');
            $countToday = Peminjaman::whereDate('tanggal_pinjam', Carbon::today())->count() + 1;
            $kodePinjam = 'PJ-' . $today . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);

            $tglPinjam = Carbon::today();
            $tglTempo = Carbon::today()->addDays($durasiHari);

            // Simpan Transaksi Peminjaman
            $peminjaman = Peminjaman::create([
                'kode_peminjaman'     => $kodePinjam,
                'anggota_id'          => $anggota->id,
                'petugas_id'          => auth('petugas')->id(),
                'tanggal_pinjam'      => $tglPinjam->format('Y-m-d'),
                'tanggal_jatuh_tempo' => $tglTempo->format('Y-m-d'),
                'status'              => 'dipinjam',
                'catatan'             => "Realisasi dari Reservasi: {$reservasi->kode_reservasi}",
            ]);

            DetailPeminjaman::create([
                'peminjaman_id'       => $peminjaman->id,
                'buku_id'             => $buku->id,
                'jumlah_pinjam'       => 1,
                'jumlah_dikembalikan' => 0,
            ]);

            // Jika status reservasi sebelumnya 'menunggu' (diambil paksa saat masih antre), kurangi stok buku rak
            if ($reservasi->status === 'menunggu') {
                if ($buku->stok_tersedia > 0) {
                    $buku->decrement('stok_tersedia', 1);
                }
            }

            // Update status reservasi jadi SELESAI
            $reservasi->update([
                'status'  => 'selesai',
                'catatan' => ($reservasi->catatan ? $reservasi->catatan . "\n" : "") . "[Buku diambil & dipinjamkan pada " . now()->format('d/m/Y H:i') . " via transaksi {$kodePinjam}]",
            ]);

            DB::commit();

            // Kirim Notifikasi WA Sukses Ambil
            if ($anggota && !empty($anggota->no_telepon)) {
                $pesanWa = "Halo {$anggota->nama_lengkap},\n\n"
                         . "✅ *Peminjaman Buku Reservasi Berhasil!*\n"
                         . "Kode Peminjaman: *{$kodePinjam}*\n"
                         . "Judul Buku: *{$buku->judul}*\n"
                         . "📅 Jatuh Tempo: *{$tglTempo->translatedFormat('l, d F Y')}*\n\n"
                         . "Buku yang kamu reservasi telah resmi kamu pinjam. Harap menjaga kondisi buku dan mengembalikan sebelum batas waktu ya!\n\n"
                         . "_Perpustakaan PerpusKu_";
                WhatsAppService::kirimPesan($anggota->no_telepon, $pesanWa);
            }

            return redirect()->route('petugas.peminjaman.show', $peminjaman->id)
                ->with('success', "Sukses! Reservasi {$reservasi->kode_reservasi} berhasil diproses menjadi Transaksi Peminjaman {$kodePinjam}.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses peminjaman dari reservasi: ' . $e->getMessage());
        }
    }

    // 3. Batalkan Reservasi oleh Petugas
    public function batalkan(ReservasiBuku $reservasi)
    {
        if (in_array($reservasi->status, ['selesai', 'dibatalkan', 'kedaluwarsa'])) {
            return back()->with('error', 'Reservasi ini sudah berstatus selesai atau dibatalkan.');
        }

        DB::beginTransaction();
        try {
            $statusLama = $reservasi->status;
            $bukuId = $reservasi->buku_id;
            $petugasNama = auth('petugas')->user()->nama_lengkap ?? 'Petugas';

            $reservasi->update([
                'status'  => 'dibatalkan',
                'catatan' => ($reservasi->catatan ? $reservasi->catatan . "\n" : "") . "[Dibatalkan oleh petugas {$petugasNama} pada " . now()->format('d/m/Y H:i') . "]",
            ]);

            // Jika buku sebelumnya berstatus 'tersedia' (disisihkan di meja sirkulasi):
            // Berikan ke antrean reservasi berikutnya jika ada, atau kembalikan ke stok rak!
            if ($statusLama === 'tersedia') {
                $nextReservasi = ReservasiBuku::with('anggota')
                    ->where('buku_id', $bukuId)
                    ->where('status', 'menunggu')
                    ->orderBy('id', 'asc')
                    ->first();

                if ($nextReservasi) {
                    $batasAmbil = now()->addDays(2)->endOfDay();
                    $nextReservasi->update([
                        'status'           => 'tersedia',
                        'tanggal_tersedia' => now(),
                        'batas_ambil'      => $batasAmbil,
                        'catatan'          => ($nextReservasi->catatan ? $nextReservasi->catatan . "\n" : '') . "[Dialihkan dari pembatalan reservasi sebelumnya pada " . now()->format('d/m/Y H:i') . "]",
                    ]);

                    if ($nextReservasi->anggota && !empty($nextReservasi->anggota->no_telepon)) {
                        $pesanWa = "Halo {$nextReservasi->anggota->nama_lengkap},\n\n"
                                 . "🎉 *Buku Antrean Kamu Sudah Tersedia!*\n"
                                 . "Kode Reservasi: *{$nextReservasi->kode_reservasi}*\n"
                                 . "Judul Buku: *{$reservasi->buku->judul}*\n"
                                 . "📅 Batas Ambil: *{$batasAmbil->translatedFormat('l, d F Y')}*\n\n"
                                 . "Buku yang kamu reservasi telah tersedia di meja sirkulasi perpustakaan. Silakan segera diambil!\n\n"
                                 . "_Perpustakaan PerpusKu_";
                        WhatsAppService::kirimPesan($nextReservasi->anggota->no_telepon, $pesanWa);
                    }
                } else {
                    Buku::where('id', $bukuId)->increment('stok_tersedia', 1);
                }
            }

            DB::commit();

            return back()->with('success', "Reservasi {$reservasi->kode_reservasi} berhasil dibatalkan dan alokasi buku telah disesuaikan.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membatalkan reservasi: ' . $e->getMessage());
        }
    }

    // 4. Batch Cek & Update Reservasi Kedaluwarsa (Expired)
    public function cekKedaluwarsa()
    {
        $expiredList = ReservasiBuku::where('status', 'tersedia')
            ->whereNotNull('batas_ambil')
            ->where('batas_ambil', '<', now())
            ->get();

        if ($expiredList->isEmpty()) {
            return back()->with('info', 'Tidak ada antrean reservasi yang kedaluwarsa saat ini.');
        }

        $processed = 0;
        DB::beginTransaction();
        try {
            foreach ($expiredList as $item) {
                $bukuId = $item->buku_id;

                $item->update([
                    'status'  => 'kedaluwarsa',
                    'catatan' => ($item->catatan ? $item->catatan . "\n" : "") . "[Kedaluwarsa otomatis: Melewati batas ambil " . Carbon::parse($item->batas_ambil)->format('d/m/Y') . "]",
                ]);

                // Alihkan ke antrean berikutnya atau kembalikan ke stok umum
                $nextReservasi = ReservasiBuku::with('anggota')
                    ->where('buku_id', $bukuId)
                    ->where('status', 'menunggu')
                    ->orderBy('id', 'asc')
                    ->first();

                if ($nextReservasi) {
                    $batasAmbil = now()->addDays(2)->endOfDay();
                    $nextReservasi->update([
                        'status'           => 'tersedia',
                        'tanggal_tersedia' => now(),
                        'batas_ambil'      => $batasAmbil,
                        'catatan'          => ($nextReservasi->catatan ? $nextReservasi->catatan . "\n" : '') . "[Dialihkan karena reservasi {$item->kode_reservasi} kedaluwarsa pada " . now()->format('d/m/Y H:i') . "]",
                    ]);

                    if ($nextReservasi->anggota && !empty($nextReservasi->anggota->no_telepon)) {
                        $pesanWa = "Halo {$nextReservasi->anggota->nama_lengkap},\n\n"
                                 . "🎉 *Buku Antrean Kamu Sudah Tersedia!*\n"
                                 . "Kode Reservasi: *{$nextReservasi->kode_reservasi}*\n"
                                 . "Judul Buku: *{$item->buku->judul}*\n"
                                 . "📅 Batas Ambil: *{$batasAmbil->translatedFormat('l, d F Y')}*\n\n"
                                 . "Buku yang kamu reservasi telah tersedia di meja sirkulasi perpustakaan. Silakan segera diambil!\n\n"
                                 . "_Perpustakaan PerpusKu_";
                        WhatsAppService::kirimPesan($nextReservasi->anggota->no_telepon, $pesanWa);
                    }
                } else {
                    Buku::where('id', $bukuId)->increment('stok_tersedia', 1);
                }

                $processed++;
            }

            DB::commit();

            return back()->with('success', "Pemeriksaan selesai: {$processed} antrean reservasi yang melewati batas waktu berhasil diperbarui statusnya & dialihkan.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses reservasi kedaluwarsa: ' . $e->getMessage());
        }
    }
}

