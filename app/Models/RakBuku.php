<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RakBuku extends Model
{
    protected $table = 'rak_buku';

    protected $primaryKey = 'id';

    public const CREATED_AT = 'dibuat_pada';
    public const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'kode_rak',
        'nama_rak',
        'lokasi_gedung',
        'keterangan',
        'status',
    ];

    public function buku(): HasMany
    {
        return $this->hasMany(Buku::class, 'rak_id');
    }

    public function getLabelRakAttribute(): string
    {
        $lokasi = !empty($this->lokasi_gedung) ? " - {$this->lokasi_gedung}" : "";
        return "[{$this->kode_rak}] {$this->nama_rak}{$lokasi}";
    }
}

