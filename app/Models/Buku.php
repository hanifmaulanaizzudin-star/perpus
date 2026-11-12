<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Buku extends Model
{
    protected $table = 'buku';

    protected $primaryKey = 'id';

    public const CREATED_AT = 'dibuat_pada';
    public const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'kategori_id',
        'rak_id',
        'kode_buku',
        'isbn',
        'judul',
        'penulis',
        'penerbit',
        'tahun_terbit',
        'deskripsi',
        'lokasi_rak',
        'jumlah_stok',
        'stok_tersedia',
        'gambar_sampul',
        'status',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriBuku::class, 'kategori_id');
    }

    public function rak(): BelongsTo
    {
        return $this->belongsTo(RakBuku::class, 'rak_id');
    }

    public function getNamaLokasiRakAttribute(): string
    {
        if ($this->rak) {
            return "[{$this->rak->kode_rak}] {$this->rak->nama_rak}";
        }
        return $this->lokasi_rak ?? '-';
    }

    public function detailPeminjaman(): HasMany
    {
        return $this->hasMany(DetailPeminjaman::class, 'buku_id');
    }
}