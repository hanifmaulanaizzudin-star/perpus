<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_peminjaman', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('peminjaman_id');
            $table->unsignedBigInteger('buku_id');

            $table->unsignedInteger('jumlah_pinjam')->default(1);
            $table->unsignedInteger('jumlah_dikembalikan')->default(0);

            $table->timestamp('dibuat_pada')->nullable();
            $table->timestamp('diperbarui_pada')->nullable();

            $table->unique(
                ['peminjaman_id', 'buku_id'],
                'detail_peminjaman_transaksi_buku_unique'
            );

            $table->index('buku_id');

            $table->foreign('peminjaman_id')
                ->references('id')
                ->on('peminjaman')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreign('buku_id')
                ->references('id')
                ->on('buku')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_peminjaman');
    }
};