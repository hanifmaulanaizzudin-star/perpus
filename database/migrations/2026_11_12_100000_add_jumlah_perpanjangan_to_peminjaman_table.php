<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            if (!Schema::hasColumn('peminjaman', 'jumlah_perpanjangan')) {
                $table->unsignedTinyInteger('jumlah_perpanjangan')->default(0)->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            if (Schema::hasColumn('peminjaman', 'jumlah_perpanjangan')) {
                $table->dropColumn('jumlah_perpanjangan');
            }
        });
    }
};

