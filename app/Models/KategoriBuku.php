<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriBuku extends Model
{
    protected $table = 'kategori_buku';

    protected $primaryKey = 'id';

    public const CREATED_AT = 'dibuat_pada';
    public const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'nama_kategori',
        'deskripsi',
        'status',
    ];

    public function buku(): HasMany
    {
        return $this->hasMany(Buku::class, 'kategori_id');
    }
}

