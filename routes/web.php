<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\PetugasAuthController;
use App\Http\Controllers\Auth\AnggotaAuthController;
use App\Http\Controllers\Petugas\DashboardController as PetugasDashboardController;
use App\Http\Controllers\Anggota\DashboardController as AnggotaDashboardController;
use App\Http\Controllers\Petugas\AnggotaController;
use App\Http\Controllers\Petugas\KategoriBukuController;
use App\Http\Controllers\Petugas\BukuController;
use App\Http\Controllers\Petugas\PeminjamanController;
use App\Http\Controllers\Petugas\PengembalianController;

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
        
        Route::get('/buku/{buku}/cetak-barcode', [BukuController::class, 'cetakBarcode'])
            ->name('buku.cetak-barcode');
        Route::resource('buku', BukuController::class);

        Route::get('/peminjaman/cari-buku', [PeminjamanController::class, 'cariBuku'])
            ->name('peminjaman.cari-buku');
        Route::get('/peminjaman/cari-anggota', [PeminjamanController::class, 'cariAnggota'])
            ->name('peminjaman.cari-anggota');
        Route::post('/peminjaman/blast-wa', [PeminjamanController::class, 'blastWaOtomatis'])
            ->name('peminjaman.blast-wa');
        Route::resource('peminjaman', PeminjamanController::class);
        Route::resource('pengembalian', PengembalianController::class);
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
