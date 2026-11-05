@extends('layouts.app')

@section('title', 'Detail Kategori Buku')

@section('content')
<div class="card-box" style="max-width: 700px;">

    <div style="margin-bottom: 25px; border-bottom: 1px solid #e2e8f0; padding-bottom: 15px;">
        <h3 style="font-size: 20px; margin-bottom: 6px;">{{ $kategori->nama_kategori }}</h3>
        <p style="color: #64748b; font-size: 14px;">Detail informasi kategori buku perpustakaan.</p>
    </div>

    <div style="display: flex; flex-direction: column; gap: 15px; margin-bottom: 30px;">
        <div>
            <strong style="color: #64748b; font-size: 13px; display: block; margin-bottom: 4px;">Status:</strong>
            <span class="badge {{ $kategori->status == 'aktif' ? 'badge-aktif' : 'badge-nonaktif' }}">
                {{ ucfirst($kategori->status) }}
            </span>
        </div>

        <div>
            <strong style="color: #64748b; font-size: 13px; display: block; margin-bottom: 4px;">Deskripsi:</strong>
            <p style="font-size: 15px; line-height: 1.5;">{{ $kategori->deskripsi ?? 'Tidak ada deskripsi.' }}</p>
        </div>

        <div>
            <strong style="color: #64748b; font-size: 13px; display: block; margin-bottom: 4px;">Dibuat Pada:</strong>
            <p style="font-size: 14px; color: #475569;">{{ $kategori->dibuat_pada ? $kategori->dibuat_pada->format('d M Y, H:i') : '-' }}</p>
        </div>
    </div>

    <div class="form-actions">
        <a href="{{ route('petugas.kategori.edit', $kategori->id) }}" class="btn btn-primary">Edit Kategori</a>
        <a href="{{ route('petugas.kategori.index') }}" class="btn btn-secondary">← Kembali</a>
    </div>

</div>
@endsection