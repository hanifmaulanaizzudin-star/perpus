<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\DetailPeminjaman;
use App\Models\KategoriBuku;
use App\Models\Peminjaman;
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

        $buku = Buku::with('kategori')
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
        $buku->load('kategori');
        return view('anggota.detail_buku', compact('buku'));
    }

    // 4. Riwayat Semua Transaksi Peminjaman Anggota
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
