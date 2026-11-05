<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buku', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('kategori_id')->nullable();

            $table->string('kode_buku', 50)->unique();
            $table->string('isbn', 30)->nullable()->unique();
            $table->string('judul', 200);
            $table->string('penulis', 150)->nullable();
            $table->string('penerbit', 150)->nullable();
            $table->year('tahun_terbit')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('lokasi_rak', 50)->nullable();

            $table->unsignedInteger('jumlah_stok')->default(0);
            $table->unsignedInteger('stok_tersedia')->default(0);

            $table->string('gambar_sampul')->nullable();

            $table->enum('status', [
                'tersedia',
                'nonaktif',
            ]);

            $table->timestamp('dibuat_pada')->nullable();
            $table->timestamp('diperbarui_pada')->nullable();

            $table->foreign('kategori_id')
                ->references('id')
                ->on('kategori_buku')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buku');
    }
};