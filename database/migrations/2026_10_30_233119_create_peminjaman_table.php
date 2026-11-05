<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id();

            $table->string('kode_peminjaman', 50)->unique();

            $table->unsignedBigInteger('anggota_id');
            $table->unsignedBigInteger('petugas_id');

            $table->date('tanggal_pinjam');
            $table->date('tanggal_jatuh_tempo');

            $table->enum('status', [
                'dipinjam',
                'terlambat',
                'dikembalikan',
                'dibatalkan',
            ]);

            $table->text('catatan')->nullable();

            $table->timestamp('dibuat_pada')->nullable();
            $table->timestamp('diperbarui_pada')->nullable();

            $table->index(
            ['tanggal_pinjam', 'tanggal_jatuh_tempo'],
            'peminjaman_tanggal_index'
            );

            $table->index('status', 'peminjaman_status_index');

            $table->foreign('anggota_id')
                ->references('id')
                ->on('anggota')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreign('petugas_id')
                ->references('id')
                ->on('petugas')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};