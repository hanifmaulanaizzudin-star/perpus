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

    /**
     * Hitung total biaya ganti rugi (buku hilang/rusak) dari detail transaksi
     */
    public function getTotalGantiRugiAttribute(): float
    {
        return (float) $this->detailPengembalian->sum('biaya_ganti_rugi');
    }

    /**
     * Ekstrak nominal denda keterlambatan jika tercatat di catatan pengembalian
     */
    public function getNominalDendaAttribute(): float
    {
        if (preg_match('/Denda Keterlambatan:.*?=\s*Rp\s*([\d\.]+)/i', $this->catatan ?? '', $matches)) {
            return (float) str_replace('.', '', $matches[1]);
        }
        return 0;
    }

    /**
     * Hitung total penerimaan uang kas masuk (Ganti Rugi + Denda)
     */
    public function getTotalKasMasukAttribute(): float
    {
        return $this->total_ganti_rugi + $this->nominal_denda;
    }

    /**
     * Ringkasan Uraian Kas Masuk untuk Keperluan LPJ / Akuntansi
     */
    public function getUraianKasAttribute(): string
    {
        $uraian = [];

        foreach ($this->detailPengembalian as $d) {
            $judul = $d->detailPeminjaman->buku->judul ?? 'Buku';
            if ($d->kondisi_buku === 'hilang') {
                $uraian[] = "Ganti Rugi Buku Hilang ({$judul})";
            } elseif ($d->kondisi_buku === 'rusak_berat') {
                $uraian[] = "Ganti Rugi Buku Rusak ({$judul})";
            } elseif ($d->biaya_ganti_rugi > 0) {
                $uraian[] = "Ganti Rugi ({$judul})";
            }
        }

        if ($this->nominal_denda > 0) {
            $uraian[] = "Denda Keterlambatan Pengembalian";
        }

        if (empty($uraian)) {
            return "Pengembalian Buku Fisik Baik (Nihil Kas)";
        }

        return implode(', ', $uraian);
    }
}