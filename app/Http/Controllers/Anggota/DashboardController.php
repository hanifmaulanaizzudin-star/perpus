<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\DetailPeminjaman;
use App\Models\KategoriBuku;
use App\Models\Peminjaman;
use App\Models\ReservasiBuku;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // 1. Dashboard Utama Anggota (Ringkasan Pinjaman & Rekomendasi Buku)
    public function index()
    {
        $anggotaId = auth('anggota')->id();

        // Hitung transaksi pinjaman aktif
        $pinjamanAktif = Peminjaman::where('anggota_id', $anggotaId)
            ->whereIn('status', ['dipinjam', 'terlambat'])
            ->count();

        // Hitung total buku yang sedang dibawa pulang
        $totalBukuDibawa = DetailPeminjaman::whereHas('peminjaman', function ($q) use ($anggotaId) {
            $q->where('anggota_id', $anggotaId)
              ->whereIn('status', ['dipinjam', 'terlambat']);
        })->sum('jumlah_pinjam');

        // Total transaksi pinjaman selesai
        $pinjamanSelesai = Peminjaman::where('anggota_id', $anggotaId)
            ->where('status', 'dikembalikan')
            ->count();

        // Hitung reservasi aktif (menunggu / siap diambil)
        $reservasiAktif = ReservasiBuku::where('anggota_id', $anggotaId)
            ->whereIn('status', ['menunggu', 'tersedia'])
            ->count();

        // Ambil daftar transaksi peminjaman yang sedang aktif
        $pinjamanBerjalan = Peminjaman::with(['detailPeminjaman.buku.kategori'])
            ->where('anggota_id', $anggotaId)
            ->whereIn('status', ['dipinjam', 'terlambat'])
            ->latest('id')
            ->get();

        // 4 Buku Koleksi Terbaru untuk Rekomendasi
        $bukuTerbaru = Buku::with('kategori')
            ->where('status', 'tersedia')
            ->latest('id')
            ->take(4)
            ->get();

        return view('anggota.dashboard', compact(
            'pinjamanAktif',
            'totalBukuDibawa',
            'pinjamanSelesai',
            'reservasiAktif',
            'pinjamanBerjalan',
            'bukuTerbaru'
        ));
    }

    // 2. Katalog Buku Perpustakaan untuk Anggota
    public function katalog(Request $request)
    {
        $kategoriList = KategoriBuku::where('status', 'aktif')
            ->orderBy('nama_kategori')
            ->get();

        $buku = Buku::with(['kategori', 'rak'])
            ->where('status', 'tersedia')
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('judul', 'like', "%{$search}%")
                      ->orWhere('penulis', 'like', "%{$search}%")
                      ->orWhere('kode_buku', 'like', "%{$search}%");
                });
            })
            ->when($request->kategori_id, function ($query, $kategoriId) {
                $query->where('kategori_id', $kategoriId);
            })
            ->latest('id')
            ->paginate(8)
            ->withQueryString();

        return view('anggota.katalog', compact('buku', 'kategoriList'));
    }

    // 3. Detail Informasi Buku untuk Anggota
    public function detailBuku(Buku $buku)
    {
        $buku->load(['kategori', 'rak']);
        $anggotaId = auth('anggota')->id();

        $reservasiAktif = ReservasiBuku::where('anggota_id', $anggotaId)
            ->where('buku_id', $buku->id)
            ->whereIn('status', ['menunggu', 'tersedia'])
            ->first();

        $totalAntreanBuku = ReservasiBuku::where('buku_id', $buku->id)
            ->where('status', 'menunggu')
            ->count();

        return view('anggota.detail_buku', compact('buku', 'reservasiAktif', 'totalAntreanBuku'));
    }

    // 4. Ajukan Reservasi Buku (Saat Stok 0)
    public function ajukanReservasi(Buku $buku)
    {
        $anggota = auth('anggota')->user();

        // Validasi keaktifan anggota
        if ($anggota->status_keanggotaan !== 'aktif') {
            return back()->with('error', 'Status keanggotaan Anda tidak aktif. Tidak dapat mengajukan reservasi buku.');
        }

        // Cek apakah anggota punya tanggungan pinjaman terlambat
        $adaTerlambat = Peminjaman::where('anggota_id', $anggota->id)
            ->where('status', 'terlambat')
            ->exists();
        if ($adaTerlambat) {
            return back()->with('error', 'Anda masih memiliki pinjaman buku yang terlambat. Harap selesaikan pengembalian dan denda di perpustakaan terlebih dahulu.');
        }

        // Cek apakah sudah pernah booking buku yang sama dan masih aktif
        $sudahBooking = ReservasiBuku::where('anggota_id', $anggota->id)
            ->where('buku_id', $buku->id)
            ->whereIn('status', ['menunggu', 'tersedia'])
            ->exists();
        if ($sudahBooking) {
            return back()->with('error', 'Anda sudah memiliki antrean reservasi aktif untuk buku ini.');
        }

        // Generate Kode Reservasi: RSV-YYYYMMDD-0001
        $today = Carbon::today()->format('Ymd');
        $countToday = ReservasiBuku::whereDate('tanggal_reservasi', Carbon::today())->count() + 1;
        $kodeReservasi = 'RSV-' . $today . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);

        $reservasi = ReservasiBuku::create([
            'kode_reservasi'    => $kodeReservasi,
            'anggota_id'        => $anggota->id,
            'buku_id'           => $buku->id,
            'tanggal_reservasi' => now(),
            'status'            => 'menunggu',
            'catatan'           => 'Diajukan mandiri oleh siswa via portal online',
        ]);

        $posisi = $reservasi->posisi_antrean;

        return back()->with('success', "Berhasil masuk antrean reservasi untuk buku '{$buku->judul}'! Kode: {$kodeReservasi}. Posisi antrean Anda saat ini: Antrean Ke-{$posisi}.");
    }

    // 5. Batalkan Reservasi
    public function batalkanReservasi(ReservasiBuku $reservasi)
    {
        if ($reservasi->anggota_id !== auth('anggota')->id()) {
            abort(403, 'Akses tidak sah.');
        }

        if (!in_array($reservasi->status, ['menunggu', 'tersedia'])) {
            return back()->with('error', 'Reservasi ini sudah tidak dapat dibatalkan.');
        }

        $reservasi->update([
            'status' => 'dibatalkan',
            'catatan' => $reservasi->catatan . "\n[Dibatalkan mandiri oleh siswa pada " . now()->format('d/m/Y H:i') . "]",
        ]);

        return back()->with('success', 'Reservasi buku berhasil dibatalkan.');
    }

    // 6. Riwayat Reservasi Saya
    public function reservasiSaya()
    {
        $anggotaId = auth('anggota')->id();

        $reservasi = ReservasiBuku::with(['buku.kategori', 'buku.rak'])
            ->where('anggota_id', $anggotaId)
            ->latest('id')
            ->get();

        return view('anggota.reservasi_saya', compact('reservasi'));
    }

    // 7. Riwayat Semua Transaksi Peminjaman Anggota
    public function pinjamanSaya()
    {
        $anggotaId = auth('anggota')->id();

        $riwayatPinjaman = Peminjaman::with([
            'detailPeminjaman.buku.kategori',
            'pengembalian'
        ])
            ->where('anggota_id', $anggotaId)
            ->latest('id')
            ->paginate(8);

        return view('anggota.pinjaman_saya', compact('riwayatPinjaman'));
    }

    // 5. Tampilan Kartu Anggota Digital
    public function kartuSaya()
    {
        $anggota = auth('anggota')->user();
        return view('anggota.kartu_saya', compact('anggota'));
    }
}
