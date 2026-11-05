<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaturan_perpustakaan', function (Blueprint $table) {
            $table->id();

            $table->string('nama_perpustakaan', 150);
            $table->text('alamat')->nullable();

            $table->unsignedInteger('maksimal_buku_per_anggota')->default(3);
            $table->unsignedInteger('lama_pinjam_hari')->default(7);

            $table->decimal('denda_per_hari', 12, 2)->default(0);
            $table->boolean('gunakan_denda')->default(false);

            $table->timestamp('diperbarui_pada')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturan_perpustakaan');
    }
};
