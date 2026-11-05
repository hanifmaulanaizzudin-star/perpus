<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengaturanPerpustakaan extends Model
{
    protected $table = 'pengaturan_perpustakaan';

    protected $primaryKey = 'id';

    public const CREATED_AT = null;
    public const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'nama_perpustakaan',
        'alamat',
        'maksimal_buku_per_anggota',
        'lama_pinjam_hari',
        'denda_per_hari',
        'gunakan_denda',
    ];

    protected function casts(): array
    {
        return [
            'maksimal_buku_per_anggota' => 'integer',
            'lama_pinjam_hari' => 'integer',
            'denda_per_hari' => 'decimal:2',
            'gunakan_denda' => 'boolean',
        ];
    }
}