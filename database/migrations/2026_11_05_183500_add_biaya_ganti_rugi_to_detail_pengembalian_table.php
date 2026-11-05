<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('detail_pengembalian') && !Schema::hasColumn('detail_pengembalian', 'biaya_ganti_rugi')) {
            Schema::table('detail_pengembalian', function (Blueprint $table) {
                $table->decimal('biaya_ganti_rugi', 12, 2)->default(0)->after('kondisi_buku');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('detail_pengembalian') && Schema::hasColumn('detail_pengembalian', 'biaya_ganti_rugi')) {
            Schema::table('detail_pengembalian', function (Blueprint $table) {
                $table->dropColumn('biaya_ganti_rugi');
            });
        }
    }
};

