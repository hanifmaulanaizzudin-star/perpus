
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $tables = [
        'anggota',
        'buku',
        'cache',
        'cache_locks',
        'detail_peminjaman',
        'detail_pengembalian',
        'failed_jobs',
        'job_batches',
        'jobs',
        'kategori_buku',
        'migrations',
        'password_reset_tokens',
        'peminjaman',
        'pengaturan_perpustakaan',
        'pengembalian',
        'petugas',
        'sessions',
        'users',
    ];

    public function up(): void
    {
        $database = DB::connection()->getDatabaseName();

        if ($database !== 'my_perpus_testing') {
            throw new RuntimeException(
                'Migration ini hanya boleh dijalankan di my_perpus_testing.'
            );
        }

        foreach ($this->tables as $table) {
            DB::statement(
                "ALTER TABLE `{$table}` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci"
            );
        }
    }

    public function down(): void
    {
        $database = DB::connection()->getDatabaseName();

        if ($database !== 'my_perpus_testing') {
            throw new RuntimeException(
                'Rollback ini hanya boleh dijalankan di my_perpus_testing.'
            );
        }

        foreach ($this->tables as $table) {
            DB::statement(
                "ALTER TABLE `{$table}` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            );
        }
    }
};
