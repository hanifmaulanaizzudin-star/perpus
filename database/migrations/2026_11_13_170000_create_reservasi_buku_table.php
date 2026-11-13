<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('reservasi_buku')) {
            Schema::create('reservasi_buku', function (Blueprint $table) {
                $table->id();

                $table->string('kode_reservasi', 50)->unique();
                $table->unsignedBigInteger('anggota_id');
                $table->unsignedBigInteger('buku_id');

                $table->dateTime('tanggal_reservasi');
                $table->dateTime('tanggal_tersedia')->nullable();
                $table->dateTime('batas_ambil')->nullable();

                $table->enum('status', [
                    'menunggu',     // Sedang antre menunggu peminjam lama mengembalikan buku
                    'tersedia',     // Buku sudah kembali, disisihkan di perpus siap diambil siswa
                    'selesai',      // Siswa sudah mengambil buku (dikonversi jadi transaksi pinjam)
                    'dibatalkan',   // Dibatalkan oleh siswa atau petugas
                    'kedaluwarsa',  // Batas 2 hari pengambilan habis tanpa diambil
                ])->default('menunggu');

                $table->text('catatan')->nullable();

                $table->timestamp('dibuat_pada')->nullable();
                $table->timestamp('diperbarui_pada')->nullable();

                $table->index(['anggota_id', 'status']);
                $table->index(['buku_id', 'status']);

                $table->foreign('anggota_id')
                    ->references('id')
                    ->on('anggota')
                    ->restrictOnDelete()
                    ->cascadeOnUpdate();

                $table->foreign('buku_id')
                    ->references('id')
                    ->on('buku')
                    ->restrictOnDelete()
                    ->cascadeOnUpdate();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('reservasi_buku');
    }
};

