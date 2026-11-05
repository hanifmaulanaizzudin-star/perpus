<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\Pengembalian;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Data Statistik Utama
        $totalBuku = Buku::count();

        $totalAnggota = Anggota::where('status_keanggotaan', 'aktif')->count();

        $totalPeminjaman = Peminjaman::count();

        $peminjamanAktif = Peminjaman::whereIn('status', ['dipinjam', 'terlambat'])->count();

        $totalPengembalian = Pengembalian::count();

        $peminjamanTerlambat = Peminjaman::where('status', 'terlambat')->count();

        // 2. Data 5 Transaksi Peminjaman Terbaru
        $transaksiTerbaru = Peminjaman::with(['anggota', 'detailPeminjaman.buku'])
            ->latest('id')
            ->take(5)
            ->get();

        return view('petugas.dashboard', compact(
            'totalBuku',
            'totalAnggota',
            'totalPeminjaman',
            'peminjamanAktif',
            'totalPengembalian',
            'peminjamanTerlambat',
            'transaksiTerbaru'
        ));
    }
}
