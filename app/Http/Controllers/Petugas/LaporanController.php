<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\DetailPeminjaman;
use App\Models\DetailPengembalian;
use App\Models\Peminjaman;
use App\Models\PengaturanPerpustakaan;
use App\Models\Pengembalian;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    /**
     * Tampilkan Halaman Rekapitulasi & Laporan Perpustakaan (Dashboard Analitik)
     */
    public function index(Request $request)
    {
        // 1. Tentukan Rentang Tanggal Filter (Default: Awal Bulan ini s/d Hari ini)
        $tanggalMulai = $request->query('tanggal_mulai', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $tanggalSelesai = $request->query('tanggal_selesai', Carbon::now()->format('Y-m-d'));

        // 2. Data Transaksi Peminjaman dalam Rentang Tanggal
        $peminjaman = Peminjaman::with(['anggota', 'petugas', 'detailPeminjaman.buku'])
            ->whereDate('tanggal_pinjam', '>=', $tanggalMulai)
            ->whereDate('tanggal_pinjam', '<=', $tanggalSelesai)
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest('id')
            ->get();

        // 3. Data Transaksi Pengembalian dalam Rentang Tanggal
        $pengembalian = Pengembalian::with([
            'peminjaman.anggota',
            'petugas',
            'detailPengembalian.detailPeminjaman.buku'
        ])
            ->whereDate('tanggal_pengembalian', '>=', $tanggalMulai)
            ->whereDate('tanggal_pengembalian', '<=', $tanggalSelesai)
            ->latest('id')
            ->get();

        // 4. Perhitungan Metrik Ringkasan Sirkulasi
        $totalPeminjamanPeriode = $peminjaman->count();
        $totalBukuDipinjam = $peminjaman->sum(function ($p) {
            return $p->detailPeminjaman->sum('jumlah_pinjam');
        });
        $peminjamanSelesai = $peminjaman->where('status', 'dikembalikan')->count();
        $peminjamanAktif = $peminjaman->where('status', 'dipinjam')->count();
        $peminjamanTerlambat = $peminjaman->where('status', 'terlambat')->count();

        // 5. Perhitungan Kas Masuk (Ganti Rugi Buku Hilang/Rusak & Denda)
        $totalGantiRugi = DetailPengembalian::whereHas('pengembalian', function ($q) use ($tanggalMulai, $tanggalSelesai) {
            $q->whereDate('tanggal_pengembalian', '>=', $tanggalMulai)
              ->whereDate('tanggal_pengembalian', '<=', $tanggalSelesai);
        })->sum('biaya_ganti_rugi');

        $totalDenda = $pengembalian->sum(function ($p) {
            return $p->nominal_denda;
        });

        $totalKasMasuk = $totalGantiRugi + $totalDenda;

        $totalBukuHilang = DetailPengembalian::whereHas('pengembalian', function ($q) use ($tanggalMulai, $tanggalSelesai) {
            $q->whereDate('tanggal_pengembalian', '>=', $tanggalMulai)
              ->whereDate('tanggal_pengembalian', '<=', $tanggalSelesai);
        })->where('kondisi_buku', 'hilang')->count();

        $totalBukuRusak = DetailPengembalian::whereHas('pengembalian', function ($q) use ($tanggalMulai, $tanggalSelesai) {
            $q->whereDate('tanggal_pengembalian', '>=', $tanggalMulai)
              ->whereDate('tanggal_pengembalian', '<=', $tanggalSelesai);
        })->whereIn('kondisi_buku', ['rusak_berat', 'rusak_ringan'])->count();

        // 6. Buku Terpopuler (Top 5 Paling Sering Dipinjam)
        $bukuPopuler = DetailPeminjaman::whereHas('peminjaman', function ($q) use ($tanggalMulai, $tanggalSelesai) {
            $q->whereDate('tanggal_pinjam', '>=', $tanggalMulai)
              ->whereDate('tanggal_pinjam', '<=', $tanggalSelesai);
        })
            ->select('buku_id', DB::raw('count(*) as total_dipinjam'))
            ->groupBy('buku_id')
            ->orderByDesc('total_dipinjam')
            ->with('buku.kategori')
            ->take(5)
            ->get();

        // 7. Anggota Teraktif (Top 5 Siswa Paling Sering Meminjam)
        $anggotaAktif = Peminjaman::whereDate('tanggal_pinjam', '>=', $tanggalMulai)
            ->whereDate('tanggal_pinjam', '<=', $tanggalSelesai)
            ->select('anggota_id', DB::raw('count(*) as total_transaksi'))
            ->groupBy('anggota_id')
            ->orderByDesc('total_transaksi')
            ->with('anggota')
            ->take(5)
            ->get();

        $pengaturan = PengaturanPerpustakaan::first();

        return view('petugas.laporan.index', compact(
            'peminjaman',
            'pengembalian',
            'tanggalMulai',
            'tanggalSelesai',
            'totalPeminjamanPeriode',
            'totalBukuDipinjam',
            'peminjamanSelesai',
            'peminjamanAktif',
            'peminjamanTerlambat',
            'totalGantiRugi',
            'totalDenda',
            'totalKasMasuk',
            'totalBukuHilang',
            'totalBukuRusak',
            'bukuPopuler',
            'anggotaAktif',
            'pengaturan'
        ));
    }

    /**
     * Halaman Cetak Dokumen Resmi Laporan Lengkap (Print / PDF)
     */
    public function cetak(Request $request)
    {
        $tanggalMulai = $request->query('tanggal_mulai', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $tanggalSelesai = $request->query('tanggal_selesai', Carbon::now()->format('Y-m-d'));

        $peminjaman = Peminjaman::with(['anggota', 'petugas', 'detailPeminjaman.buku'])
            ->whereDate('tanggal_pinjam', '>=', $tanggalMulai)
            ->whereDate('tanggal_pinjam', '<=', $tanggalSelesai)
            ->latest('id')
            ->get();

        $pengembalian = Pengembalian::with([
            'peminjaman.anggota',
            'petugas',
            'detailPengembalian.detailPeminjaman.buku'
        ])
            ->whereDate('tanggal_pengembalian', '>=', $tanggalMulai)
            ->whereDate('tanggal_pengembalian', '<=', $tanggalSelesai)
            ->latest('id')
            ->get();

        $totalGantiRugi = DetailPengembalian::whereHas('pengembalian', function ($q) use ($tanggalMulai, $tanggalSelesai) {
            $q->whereDate('tanggal_pengembalian', '>=', $tanggalMulai)
              ->whereDate('tanggal_pengembalian', '<=', $tanggalSelesai);
        })->sum('biaya_ganti_rugi');

        $totalDenda = $pengembalian->sum(function ($p) {
            return $p->nominal_denda;
        });

        $totalKasMasuk = $totalGantiRugi + $totalDenda;
        $pengaturan = PengaturanPerpustakaan::first();

        return view('petugas.laporan.cetak', compact(
            'peminjaman',
            'pengembalian',
            'tanggalMulai',
            'tanggalSelesai',
            'totalGantiRugi',
            'totalDenda',
            'totalKasMasuk',
            'pengaturan'
        ));
    }

    /**
     * Halaman Cetak Khusus: LAPORAN REKAPITULASI KAS & KEUANGAN (LPJ Keuangan untuk Atasan)
     * Tidak membeberkan identitas nama siswa berlembar-lembar, melainkan fokus pada mutasi kas,
     * bukti transaksi (KB-...), uraian ganti rugi/denda, akuntabilitas petugas penerima, dan tanda tangan pengesahan.
     */
    public function cetakKeuangan(Request $request)
    {
        $tanggalMulai = $request->query('tanggal_mulai', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $tanggalSelesai = $request->query('tanggal_selesai', Carbon::now()->format('Y-m-d'));

        $pengembalian = Pengembalian::with([
            'peminjaman.anggota',
            'petugas',
            'detailPengembalian.detailPeminjaman.buku'
        ])
            ->whereDate('tanggal_pengembalian', '>=', $tanggalMulai)
            ->whereDate('tanggal_pengembalian', '<=', $tanggalSelesai)
            ->latest('id')
            ->get();

        $totalGantiRugi = DetailPengembalian::whereHas('pengembalian', function ($q) use ($tanggalMulai, $tanggalSelesai) {
            $q->whereDate('tanggal_pengembalian', '>=', $tanggalMulai)
              ->whereDate('tanggal_pengembalian', '<=', $tanggalSelesai);
        })->sum('biaya_ganti_rugi');

        $totalDenda = $pengembalian->sum(function ($p) {
            return $p->nominal_denda;
        });

        $totalKasMasuk = $totalGantiRugi + $totalDenda;

        $totalBukuHilang = DetailPengembalian::whereHas('pengembalian', function ($q) use ($tanggalMulai, $tanggalSelesai) {
            $q->whereDate('tanggal_pengembalian', '>=', $tanggalMulai)
              ->whereDate('tanggal_pengembalian', '<=', $tanggalSelesai);
        })->where('kondisi_buku', 'hilang')->count();

        $totalBukuRusak = DetailPengembalian::whereHas('pengembalian', function ($q) use ($tanggalMulai, $tanggalSelesai) {
            $q->whereDate('tanggal_pengembalian', '>=', $tanggalMulai)
              ->whereDate('tanggal_pengembalian', '<=', $tanggalSelesai);
        })->whereIn('kondisi_buku', ['rusak_berat', 'rusak_ringan'])->count();

        $pengaturan = PengaturanPerpustakaan::first();

        return view('petugas.laporan.cetak_keuangan', compact(
            'pengembalian',
            'tanggalMulai',
            'tanggalSelesai',
            'totalGantiRugi',
            'totalDenda',
            'totalKasMasuk',
            'totalBukuHilang',
            'totalBukuRusak',
            'pengaturan'
        ));
    }

    /**
     * Halaman Cetak Khusus: LAPORAN SIRKULASI KOLEKSI & STATISTIK LITERASI (Untuk Atasan / Kepala Perpus)
     * Fokus pada peredaran aset buku, eksemplar keluar-masuk, buku terpopuler, dan kondisi buku
     * tanpa mencantumkan detail nama siswa jika hanya ingin melihat rekapan kinerja perpustakaan.
     */
    public function cetakSirkulasi(Request $request)
    {
        $tanggalMulai = $request->query('tanggal_mulai', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $tanggalSelesai = $request->query('tanggal_selesai', Carbon::now()->format('Y-m-d'));

        $peminjaman = Peminjaman::with(['anggota', 'petugas', 'detailPeminjaman.buku.kategori'])
            ->whereDate('tanggal_pinjam', '>=', $tanggalMulai)
            ->whereDate('tanggal_pinjam', '<=', $tanggalSelesai)
            ->latest('id')
            ->get();

        $totalPeminjaman = $peminjaman->count();
        $totalBukuDipinjam = $peminjaman->sum(function ($p) {
            return $p->detailPeminjaman->sum('jumlah_pinjam');
        });
        $peminjamanSelesai = $peminjaman->where('status', 'dikembalikan')->count();
        $peminjamanAktif = $peminjaman->where('status', 'dipinjam')->count();
        $peminjamanTerlambat = $peminjaman->where('status', 'terlambat')->count();

        // Rekap Buku Populer
        $bukuPopuler = DetailPeminjaman::whereHas('peminjaman', function ($q) use ($tanggalMulai, $tanggalSelesai) {
            $q->whereDate('tanggal_pinjam', '>=', $tanggalMulai)
              ->whereDate('tanggal_pinjam', '<=', $tanggalSelesai);
        })
            ->select('buku_id', DB::raw('count(*) as total_dipinjam'), DB::raw('sum(jumlah_pinjam) as total_eksemplar'))
            ->groupBy('buku_id')
            ->orderByDesc('total_dipinjam')
            ->with('buku.kategori')
            ->take(10)
            ->get();

        $totalBukuHilang = DetailPengembalian::whereHas('pengembalian', function ($q) use ($tanggalMulai, $tanggalSelesai) {
            $q->whereDate('tanggal_pengembalian', '>=', $tanggalMulai)
              ->whereDate('tanggal_pengembalian', '<=', $tanggalSelesai);
        })->where('kondisi_buku', 'hilang')->count();

        $totalBukuRusak = DetailPengembalian::whereHas('pengembalian', function ($q) use ($tanggalMulai, $tanggalSelesai) {
            $q->whereDate('tanggal_pengembalian', '>=', $tanggalMulai)
              ->whereDate('tanggal_pengembalian', '<=', $tanggalSelesai);
        })->whereIn('kondisi_buku', ['rusak_berat', 'rusak_ringan'])->count();

        $pengaturan = PengaturanPerpustakaan::first();

        return view('petugas.laporan.cetak_sirkulasi', compact(
            'peminjaman',
            'tanggalMulai',
            'tanggalSelesai',
            'totalPeminjaman',
            'totalBukuDipinjam',
            'peminjamanSelesai',
            'peminjamanAktif',
            'peminjamanTerlambat',
            'bukuPopuler',
            'totalBukuHilang',
            'totalBukuRusak',
            'pengaturan'
        ));
    }

    /**
     * Export Rekapan Keuangan & Kas Masuk ke Format Microsoft Excel (.xls)
     */
    public function exportKeuanganExcel(Request $request)
    {
        $tanggalMulai = $request->query('tanggal_mulai', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $tanggalSelesai = $request->query('tanggal_selesai', Carbon::now()->format('Y-m-d'));

        $pengembalian = Pengembalian::with([
            'peminjaman.anggota',
            'petugas',
            'detailPengembalian.detailPeminjaman.buku'
        ])
            ->whereDate('tanggal_pengembalian', '>=', $tanggalMulai)
            ->whereDate('tanggal_pengembalian', '<=', $tanggalSelesai)
            ->latest('id')
            ->get();

        $totalGantiRugi = DetailPengembalian::whereHas('pengembalian', function ($q) use ($tanggalMulai, $tanggalSelesai) {
            $q->whereDate('tanggal_pengembalian', '>=', $tanggalMulai)
              ->whereDate('tanggal_pengembalian', '<=', $tanggalSelesai);
        })->sum('biaya_ganti_rugi');

        $totalDenda = $pengembalian->sum(function ($p) {
            return $p->nominal_denda;
        });

        $totalKasMasuk = $totalGantiRugi + $totalDenda;

        $pengaturan = PengaturanPerpustakaan::first();

        $filename = 'Rekap_Keuangan_Kas_PerpusKu_' . Carbon::parse($tanggalMulai)->format('Ymd') . '_sd_' . Carbon::parse($tanggalSelesai)->format('Ymd') . '.xls';

        $htmlContent = view('petugas.laporan.excel_keuangan', compact(
            'pengembalian',
            'tanggalMulai',
            'tanggalSelesai',
            'totalGantiRugi',
            'totalDenda',
            'totalKasMasuk',
            'pengaturan'
        ))->render();

        return response($htmlContent, 200, [
            'Content-Type'        => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control'       => 'max-age=0, no-cache, no-store, must-revalidate',
            'Pragma'              => 'no-cache',
            'Expires'             => '0',
        ]);
    }
}
