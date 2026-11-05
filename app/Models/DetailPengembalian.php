<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPengembalian extends Model
{
    protected $table = 'detail_pengembalian';

    protected $primaryKey = 'id';

    public const CREATED_AT = 'dibuat_pada';
    public const UPDATED_AT = null;

    protected $fillable = [
        'pengembalian_id',
        'detail_peminjaman_id',
        'jumlah_dikembalikan',
        'kondisi_buku',
        'biaya_ganti_rugi',
        'catatan',
    ];

    protected $attributes = [
        'biaya_ganti_rugi' => 0,
    ];

    protected function casts(): array
    {
        return [
            'biaya_ganti_rugi' => 'decimal:2',
        ];
    }

    public function pengembalian(): BelongsTo
    {
        return $this->belongsTo(Pengembalian::class, 'pengembalian_id');
    }

    public function detailPeminjaman(): BelongsTo
    {
        return $this->belongsTo(DetailPeminjaman::class, 'detail_peminjaman_id');
    }
}
