<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\PetugasAuthController;
use App\Http\Controllers\Auth\AnggotaAuthController;
use App\Http\Controllers\Petugas\DashboardController as PetugasDashboardController;
use App\Http\Controllers\Anggota\DashboardController as AnggotaDashboardController;
use App\Http\Controllers\Petugas\AnggotaController;
use App\Http\Controllers\Petugas\KategoriBukuController;
use App\Http\Controllers\Petugas\RakBukuController;
use App\Http\Controllers\Petugas\BukuController;
use App\Http\Controllers\Petugas\PeminjamanController;
use App\Http\Controllers\Petugas\PengembalianController;
use App\Http\Controllers\Petugas\ReservasiController;
use App\Http\Controllers\Petugas\StafController;
use App\Http\Controllers\Petugas\LaporanController;
use App\Http\Controllers\Petugas\PengaturanController;

// Rute Beranda: Mengarahkan otomatis sesuai status login
Route::get('/', function () {
    if (auth('petugas')->check()) {
        return redirect()->route('petugas.dashboard');
    }
    if (auth('anggota')->check()) {
        return redirect()->route('anggota.dashboard');
    }
    return redirect()->route('petugas.login');
});

// Portal Petugas & Admin Perpustakaan
Route::prefix('petugas')->name('petugas.')->group(function () {
    Route::middleware('guest:petugas')->group(function () {
        Route::get('/login', [PetugasAuthController::class, 'showLogin'])
            ->name('login');

        Route::post('/login', [PetugasAuthController::class, 'login'])
            ->name('login.process');
    });

    Route::middleware(['auth:petugas', 'petugas.aktif'])->group(function () {
        Route::get('/dashboard', [PetugasDashboardController::class, 'index'])
            ->name('dashboard');

        // Master Data Katalog
        Route::resource('kategori', KategoriBukuController::class);
        Route::resource('rak', RakBukuController::class);
        
        // Export & Import Massal Data Buku
        Route::get('/buku/export-csv', [BukuController::class, 'exportCsv'])
            ->name('buku.export-csv');
        Route::get('/buku/download-template', [BukuController::class, 'downloadTemplateCsv'])
            ->name('buku.download-template');
        Route::post('/buku/import-csv', [BukuController::class, 'importCsv'])
            ->name('buku.import-csv');
        Route::get('/buku/{buku}/cetak-barcode', [BukuController::class, 'cetakBarcode'])
            ->name('buku.cetak-barcode');
        Route::resource('buku', BukuController::class);

        // Data Anggota & Cetak Kartu
        Route::get('/anggota/export-csv', [AnggotaController::class, 'exportCsv'])
            ->name('anggota.export-csv');
        Route::get('/anggota/download-template', [AnggotaController::class, 'downloadTemplateCsv'])
            ->name('anggota.download-template');
        Route::post('/anggota/import-csv', [AnggotaController::class, 'importCsv'])
            ->name('anggota.import-csv');
        Route::get('/anggota/{anggota}/cetak-kartu', [AnggotaController::class, 'cetakKartu'])
            ->name('anggota.cetak-kartu');
        Route::resource('anggota', AnggotaController::class);

        // Sirkulasi Peminjaman
        Route::get('/peminjaman/cari-buku', [PeminjamanController::class, 'cariBuku'])
            ->name('peminjaman.cari-buku');
        Route::get('/peminjaman/cari-anggota', [PeminjamanController::class, 'cariAnggota'])
            ->name('peminjaman.cari-anggota');
        Route::post('/peminjaman/blast-wa', [PeminjamanController::class, 'blastWaOtomatis'])
            ->name('peminjaman.blast-wa');
        Route::post('/peminjaman/{peminjaman}/perpanjang', [PeminjamanController::class, 'perpanjang'])
            ->name('peminjaman.perpanjang');
        Route::resource('peminjaman', PeminjamanController::class);
        
        // Sirkulasi Pengembalian (Immutable Ledger - Anti-Manipulasi)
        Route::get('/pengembalian/cari-pinjaman', [PengembalianController::class, 'cariPinjaman'])
            ->name('pengembalian.cari-pinjaman');
        Route::resource('pengembalian', PengembalianController::class)->only(['index', 'create', 'store', 'show']);

        // Sirkulasi Reservasi & Antrean Buku
        Route::get('/reservasi', [ReservasiController::class, 'index'])
            ->name('reservasi.index');
        Route::post('/reservasi/{reservasi}/ambil', [ReservasiController::class, 'prosesAmbil'])
            ->name('reservasi.ambil');
        Route::post('/reservasi/{reservasi}/batal', [ReservasiController::class, 'batalkan'])
            ->name('reservasi.batal');
        Route::post('/reservasi/cek-kedaluwarsa', [ReservasiController::class, 'cekKedaluwarsa'])
            ->name('reservasi.cek-kedaluwarsa');

        // Laporan Sirkulasi Operasional
        Route::get('/laporan', [LaporanController::class, 'index'])
            ->name('laporan.index');
        Route::get('/laporan/cetak', [LaporanController::class, 'cetak'])
            ->name('laporan.cetak');
        Route::get('/laporan/cetak-sirkulasi', [LaporanController::class, 'cetakSirkulasi'])
            ->name('laporan.cetak-sirkulasi');

        // ==========================================
        // KHUSUS HAK AKSES ADMIN / KEPALA PERPUS
        // ==========================================
        Route::middleware('petugas.admin')->group(function () {
            // Manajemen Petugas & Hak Akses
            Route::resource('staf', StafController::class);

            // Pengaturan Perpustakaan (Denda, Kuota, Durasi Pinjam)
            Route::get('/pengaturan', [PengaturanController::class, 'index'])
                ->name('pengaturan.index');
            Route::put('/pengaturan', [PengaturanController::class, 'update'])
                ->name('pengaturan.update');

            // Laporan Keuangan Eksekutif (LPJ Kas & Excel)
            Route::get('/laporan/cetak-keuangan', [LaporanController::class, 'cetakKeuangan'])
                ->name('laporan.cetak-keuangan');
            Route::get('/laporan/export-keuangan', [LaporanController::class, 'exportKeuanganExcel'])
                ->name('laporan.export-keuangan');
        });

        Route::post('/logout', [PetugasAuthController::class, 'logout'])
            ->name('logout');
    });
});

// Portal Anggota (Siswa)
Route::prefix('anggota')->name('anggota.')->group(function () {
    Route::middleware('guest:anggota')->group(function () {
        Route::get('/login', [AnggotaAuthController::class, 'showLogin'])
            ->name('login');

        Route::post('/login', [AnggotaAuthController::class, 'login'])
            ->name('login.process');
    });

    Route::middleware(['auth:anggota', 'anggota.aktif'])->group(function () {
        Route::get('/dashboard', [AnggotaDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/katalog', [AnggotaDashboardController::class, 'katalog'])
            ->name('katalog');

        Route::get('/katalog/{buku}', [AnggotaDashboardController::class, 'detailBuku'])
            ->name('katalog.detail');

        // Reservasi & Antrean Buku Mandiri
        Route::post('/reservasi/{buku}', [AnggotaDashboardController::class, 'ajukanReservasi'])
            ->name('reservasi.store');
        Route::post('/reservasi/{reservasi}/batal', [AnggotaDashboardController::class, 'batalkanReservasi'])
            ->name('reservasi.batal');
        Route::get('/reservasi-saya', [AnggotaDashboardController::class, 'reservasiSaya'])
            ->name('reservasi-saya');

        Route::get('/pinjaman-saya', [AnggotaDashboardController::class, 'pinjamanSaya'])
            ->name('pinjaman-saya');

        Route::get('/kartu-saya', [AnggotaDashboardController::class, 'kartuSaya'])
            ->name('kartu-saya');

        Route::post('/logout', [AnggotaAuthController::class, 'logout'])
            ->name('logout');
    });
});
