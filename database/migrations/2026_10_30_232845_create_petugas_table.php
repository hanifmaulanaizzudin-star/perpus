<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('petugas', function (Blueprint $table) {
            $table->id();

            $table->string('nomor_pegawai', 30)->nullable()->unique();
            $table->string('nama_lengkap', 150);
            $table->string('nama_pengguna', 50)->unique();
            $table->string('email', 150)->nullable()->unique();
            $table->string('kata_sandi', 255);

            $table->string('no_telepon', 20)->nullable();
            $table->text('alamat')->nullable();

            $table->enum('status', [
                'aktif',
                'nonaktif',
            ]);

            $table->dateTime('terakhir_login')->nullable();

            $table->timestamp('dibuat_pada')->nullable();
            $table->timestamp('diperbarui_pada')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('petugas');
    }
};