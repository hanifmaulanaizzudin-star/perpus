<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DetailPeminjaman extends Model
{
    protected $table = 'detail_peminjaman';

    protected $primaryKey = 'id';

    public const CREATED_AT = 'dibuat_pada';
    public const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'peminjaman_id',
        'buku_id',
        'jumlah_pinjam',
        'jumlah_dikembalikan',
    ];

    public function peminjaman(): BelongsTo
    {
        return $this->belongsTo(Peminjaman::class, 'peminjaman_id');
    }

    public function buku(): BelongsTo
    {
        return $this->belongsTo(Buku::class, 'buku_id');
    }

    public function detailPengembalian(): HasMany
    {
        return $this->hasMany(DetailPengembalian::class, 'detail_peminjaman_id');
    }
}