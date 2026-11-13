<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReservasiBuku extends Model
{
    protected $table = 'reservasi_buku';

    protected $primaryKey = 'id';

    public const CREATED_AT = 'dibuat_pada';
    public const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'kode_reservasi',
        'anggota_id',
        'buku_id',
        'tanggal_reservasi',
        'tanggal_tersedia',
        'batas_ambil',
        'status',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_reservasi' => 'datetime',
            'tanggal_tersedia'  => 'datetime',
            'batas_ambil'       => 'datetime',
        ];
    }

    public function anggota(): BelongsTo
    {
        return $this->belongsTo(Anggota::class, 'anggota_id');
    }

    public function buku(): BelongsTo
    {
        return $this->belongsTo(Buku::class, 'buku_id');
    }

    /**
     * Hitung urutan antrean buku jika status masih 'menunggu'
     */
    public function getPosisiAntreanAttribute(): int
    {
        if ($this->status !== 'menunggu') {
            return 0;
        }

        return self::where('buku_id', $this->buku_id)
            ->where('status', 'menunggu')
            ->where('id', '<=', $this->id)
            ->count();
    }
}

