<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Petugas extends Authenticatable
{
    protected $table = 'petugas';

    protected $primaryKey = 'id';

    public const CREATED_AT = 'dibuat_pada';
    public const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'nomor_pegawai',
        'nama_lengkap',
        'role',
        'nama_pengguna',
        'email',
        'kata_sandi',
        'no_telepon',
        'alamat',
        'status',
        'terakhir_login',
    ];

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function getLabelRoleAttribute(): string
    {
        return $this->role === 'admin' ? 'Kepala Perpus / Admin' : 'Petugas Sirkulasi';
    }

    protected $hidden = [
        'kata_sandi',
    ];

    protected function casts(): array
    {
        return [
            'kata_sandi' => 'hashed',
            'terakhir_login' => 'datetime',
        ];
    }

    public function getAuthPasswordName(): string
    {
        return 'kata_sandi';
    }

    public function peminjaman(): HasMany
    {
        return $this->hasMany(Peminjaman::class, 'petugas_id');
    }

    public function pengembalian(): HasMany
    {
        return $this->hasMany(Pengembalian::class, 'petugas_id');
    }
}