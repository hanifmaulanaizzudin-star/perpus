<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_pengembalian', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('pengembalian_id');
            $table->unsignedBigInteger('detail_peminjaman_id');

            $table->unsignedInteger('jumlah_dikembalikan')->default(1);

            $table->enum('kondisi_buku', [
                'baik',
                'rusak_ringan',
                'rusak_berat',
                'hilang',
            ]);

            $table->text('catatan')->nullable();

            $table->timestamp('dibuat_pada')->nullable();

            $table->index('pengembalian_id');
            $table->index('detail_peminjaman_id');

            $table->foreign('pengembalian_id')
                ->references('id')
                ->on('pengembalian')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreign('detail_peminjaman_id')
                ->references('id')
                ->on('detail_peminjaman')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_pengembalian');
    }
};
