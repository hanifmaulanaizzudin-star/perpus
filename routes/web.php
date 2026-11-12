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

// Login Petugas
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

        Route::get('/peminjaman/cari-buku', [PeminjamanController::class, 'cariBuku'])
            ->name('peminjaman.cari-buku');
        Route::get('/peminjaman/cari-anggota', [PeminjamanController::class, 'cariAnggota'])
            ->name('peminjaman.cari-anggota');
        Route::post('/peminjaman/blast-wa', [PeminjamanController::class, 'blastWaOtomatis'])
            ->name('peminjaman.blast-wa');
        Route::post('/peminjaman/{peminjaman}/perpanjang', [PeminjamanController::class, 'perpanjang'])
            ->name('peminjaman.perpanjang');
        Route::resource('peminjaman', PeminjamanController::class);
        
        // Transaksi Pengembalian: Dibatasi HANYA index, create, store, show.
        // TIDAK ADA edit, update, atau destroy untuk menjamin Integritas Data & Anti-Manipulasi (Immutable Ledger)
        Route::get('/pengembalian/cari-pinjaman', [PengembalianController::class, 'cariPinjaman'])
            ->name('pengembalian.cari-pinjaman');
        Route::resource('pengembalian', PengembalianController::class)->only(['index', 'create', 'store', 'show']);

        // Modul Laporan & Rekapitulasi Perpustakaan
        Route::get('/laporan', [LaporanController::class, 'index'])
            ->name('laporan.index');
        Route::get('/laporan/cetak', [LaporanController::class, 'cetak'])
            ->name('laporan.cetak');
        Route::get('/laporan/cetak-keuangan', [LaporanController::class, 'cetakKeuangan'])
            ->name('laporan.cetak-keuangan');
        Route::get('/laporan/cetak-sirkulasi', [LaporanController::class, 'cetakSirkulasi'])
            ->name('laporan.cetak-sirkulasi');
        Route::get('/laporan/export-keuangan', [LaporanController::class, 'exportKeuanganExcel'])
            ->name('laporan.export-keuangan');

        // Modul Pengaturan Perpustakaan
        Route::get('/pengaturan', [PengaturanController::class, 'index'])
            ->name('pengaturan.index');
        Route::put('/pengaturan', [PengaturanController::class, 'update'])
            ->name('pengaturan.update');

        Route::post('/logout', [PetugasAuthController::class, 'logout'])
            ->name('logout');
    });
});

// Login Anggota
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

        Route::get('/pinjaman-saya', [AnggotaDashboardController::class, 'pinjamanSaya'])
            ->name('pinjaman-saya');

        Route::get('/kartu-saya', [AnggotaDashboardController::class, 'kartuSaya'])
            ->name('kartu-saya');

        Route::post('/logout', [AnggotaAuthController::class, 'logout'])
            ->name('logout');
    });
});


Route::middleware(['auth:petugas', 'petugas.aktif'])
    ->prefix('petugas')
    ->name('petugas.')
    ->group(function () {
        Route::get('/anggota', [AnggotaController::class, 'index'])
            ->name('anggota.index');

        Route::get('/anggota/create', [AnggotaController::class, 'create'])
            ->name('anggota.create');

        Route::post('/anggota', [AnggotaController::class, 'store'])
            ->name('anggota.store');

        // Export & Import Massal Data Anggota
        Route::get('/anggota/export-csv', [AnggotaController::class, 'exportCsv'])
            ->name('anggota.export-csv');
        Route::get('/anggota/download-template', [AnggotaController::class, 'downloadTemplateCsv'])
            ->name('anggota.download-template');
        Route::post('/anggota/import-csv', [AnggotaController::class, 'importCsv'])
            ->name('anggota.import-csv');

        Route::get('/anggota/{anggota}', [AnggotaController::class, 'show'])
            ->name('anggota.show');

        Route::get('/anggota/{anggota}/cetak-kartu', [AnggotaController::class, 'cetakKartu'])
            ->name('anggota.cetak-kartu');

        Route::get('/anggota/{anggota}/edit', [AnggotaController::class, 'edit'])
            ->name('anggota.edit');

        Route::put('/anggota/{anggota}', [AnggotaController::class, 'update'])
            ->name('anggota.update');

        Route::delete('/anggota/{anggota}', [AnggotaController::class, 'destroy'])
            ->name('anggota.destroy');
    });
