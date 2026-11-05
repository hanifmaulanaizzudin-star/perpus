<?php

namespace Database\Seeders;

use App\Models\Petugas;
use Illuminate\Database\Seeder;

class PetugasSeeder extends Seeder
{
    public function run(): void
    {
        Petugas::updateOrCreate(
            ['nama_pengguna' => 'adminperpus'],
            [
                'nomor_pegawai' => 'PTG001',
                'nama_lengkap' => 'Administrator Perpustakaan',
                'email' => 'adminperpus@example.com',
                'kata_sandi' => 'password123',
                'status' => 'aktif',
            ]
        );
    }
}
