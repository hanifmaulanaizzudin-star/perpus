<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pengembalian extends Model
{
    protected $table = 'pengembalian';

    protected $primaryKey = 'id';

    public const CREATED_AT = 'dibuat_pada';
    public const UPDATED_AT = null;

    protected $fillable = [
        'kode_pengembalian',
        'peminjaman_id',
        'petugas_id',
        'tanggal_pengembalian',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pengembalian' => 'datetime',
        ];
    }

    public function peminjaman(): BelongsTo
    {
        return $this->belongsTo(Peminjaman::class, 'peminjaman_id');
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(Petugas::class, 'petugas_id');
    }

    public function detailPengembalian(): HasMany
    {
        return $this->hasMany(DetailPengembalian::class, 'pengembalian_id');
    }
}