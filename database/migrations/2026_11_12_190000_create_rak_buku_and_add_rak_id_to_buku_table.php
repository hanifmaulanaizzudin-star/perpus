<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Buat Tabel rak_buku jika belum ada
        if (!Schema::hasTable('rak_buku')) {
            Schema::create('rak_buku', function (Blueprint $table) {
                $table->id();
                $table->string('kode_rak', 30)->unique();
                $table->string('nama_rak', 100);
                $table->string('lokasi_gedung', 100)->nullable(); // Misal: Lantai 1 - Sayap Barat
                $table->text('keterangan')->nullable();
                $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
                $table->timestamp('dibuat_pada')->nullable();
                $table->timestamp('diperbarui_pada')->nullable();
            });

            // Insert beberapa rak standar awal
            DB::table('rak_buku')->insert([
                [
                    'kode_rak'      => 'RAK-A1',
                    'nama_rak'      => 'Rak Sastra, Fiksi & Novel',
                    'lokasi_gedung' => 'Lantai 1 - Sayap Timur',
                    'keterangan'    => 'Koleksi novel, sastra Indonesia & terjemahan',
                    'status'        => 'aktif',
                    'dibuat_pada'   => now(),
                    'diperbarui_pada'=> now(),
                ],
                [
                    'kode_rak'      => 'RAK-B1',
                    'nama_rak'      => 'Rak Sains & Teknologi Komputer',
                    'lokasi_gedung' => 'Lantai 1 - Sayap Barat',
                    'keterangan'    => 'Buku pemrograman, matematika, fisika, dan informatika',
                    'status'        => 'aktif',
                    'dibuat_pada'   => now(),
                    'diperbarui_pada'=> now(),
                ],
                [
                    'kode_rak'      => 'RAK-C1',
                    'nama_rak'      => 'Rak Sejarah & Sosial Humaniora',
                    'lokasi_gedung' => 'Lantai 1 - Tengah',
                    'keterangan'    => 'Buku sejarah nasional, sosiologi, dan geografi',
                    'status'        => 'aktif',
                    'dibuat_pada'   => now(),
                    'diperbarui_pada'=> now(),
                ],
                [
                    'kode_rak'      => 'RAK-D1',
                    'nama_rak'      => 'Rak Referensi & Kamus Ensiklopedia',
                    'lokasi_gedung' => 'Lantai 2 - Ruang Baca Khusus',
                    'keterangan'    => 'Kamus bahasa, ensiklopedia umum, dan jurnal ilmiah',
                    'status'        => 'aktif',
                    'dibuat_pada'   => now(),
                    'diperbarui_pada'=> now(),
                ],
            ]);
        }

        // 2. Tambahkan kolom rak_id pada tabel buku
        Schema::table('buku', function (Blueprint $table) {
            if (!Schema::hasColumn('buku', 'rak_id')) {
                $table->unsignedBigInteger('rak_id')->nullable()->after('kategori_id');
                $table->foreign('rak_id')
                    ->references('id')
                    ->on('rak_buku')
                    ->nullOnDelete()
                    ->cascadeOnUpdate();
            }
        });
    }

    public function down(): void
    {
        Schema::table('buku', function (Blueprint $table) {
            if (Schema::hasColumn('buku', 'rak_id')) {
                $table->dropForeign(['rak_id']);
                $table->dropColumn('rak_id');
            }
        });

        Schema::dropIfExists('rak_buku');
    }
};

