<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('petugas', function (Blueprint $table) {
            if (!Schema::hasColumn('petugas', 'role')) {
                $table->enum('role', ['admin', 'petugas'])->default('petugas')->after('nama_lengkap');
            }
        });

        // Set semua akun petugas existing yang nama_pengguna 'admin' atau id 1 menjadi 'admin'
        DB::table('petugas')
            ->where('nama_pengguna', 'admin')
            ->orWhere('id', 1)
            ->update(['role' => 'admin']);
    }

    public function down(): void
    {
        Schema::table('petugas', function (Blueprint $table) {
            if (Schema::hasColumn('petugas', 'role')) {
                $table->dropColumn('role');
            }
        });
    }
};

