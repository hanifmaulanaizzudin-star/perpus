<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengembalian', function (Blueprint $table) {
            $table->id();

            $table->string('kode_pengembalian', 50)->unique();

            $table->unsignedBigInteger('peminjaman_id');
            $table->unsignedBigInteger('petugas_id');

            $table->dateTime('tanggal_pengembalian');
            $table->text('catatan')->nullable();

            $table->timestamp('dibuat_pada')->nullable();

            $table->index('peminjaman_id');
            $table->index('petugas_id');
            $table->index('tanggal_pengembalian');

            $table->foreign('peminjaman_id')
                ->references('id')
                ->on('peminjaman')
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
        Schema::dropIfExists('pengembalian');
    }
};
