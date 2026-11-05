<?php

namespace Database\Seeders;

use App\Models\Anggota;
use Illuminate\Database\Seeder;

class AnggotaSeeder extends Seeder
{
    public function run(): void
    {
        Anggota::updateOrCreate(
            ['nama_pengguna' => 'siswa001'],
            [
                'nomor_induk' => '2026001',
                'nama_lengkap' => 'Siswa Percobaan',
                'email' => 'siswa001@example.com',
                'kata_sandi' => 'password123',
                'kelas' => 'XI',
                'jurusan' => 'PPLG',
                'tanggal_daftar' => now()->toDateString(),
                'status_keanggotaan' => 'aktif',
            ]
        );
    }
}
