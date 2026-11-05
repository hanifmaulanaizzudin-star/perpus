<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Anggota extends Authenticatable
{
    protected $table = 'anggota';

    protected $primaryKey = 'id';

    public const CREATED_AT = 'dibuat_pada';
    public const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'nomor_induk',
        'nama_lengkap',
        'nama_pengguna',
        'email',
        'kata_sandi',
        'kelas',
        'jurusan',
        'no_telepon',
        'alamat',
        'tanggal_daftar',
        'status_keanggotaan',
        'terakhir_login',
    ];

    protected $hidden = [
        'kata_sandi',
    ];

    protected function casts(): array
    {
        return [
            'kata_sandi' => 'hashed',
            'tanggal_daftar' => 'date',
            'terakhir_login' => 'datetime',
        ];
    }

    public function getAuthPasswordName(): string
    {
        return 'kata_sandi';
    }

    public function getNomorAnggotaAttribute(): ?string
    {
        return $this->nomor_induk;
    }

    public function peminjaman(): HasMany
    {
        return $this->hasMany(Peminjaman::class, 'anggota_id');
    }
}