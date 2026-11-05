@extends('layouts.app')

@section('title', 'Tambah Kategori Buku')

@section('content')
<div class="card-box" style="max-width: 700px;">

    <div style="margin-bottom: 25px;">
        <h3 style="font-size: 18px; margin-bottom: 6px;">Form Tambah Kategori</h3>
        <p style="color: #64748b; font-size: 14px;">Masukkan informasi kategori buku baru ke perpustakaan.</p>
    </div>

    <form action="{{ route('petugas.kategori.store') }}" method="POST">
        @csrf

        {{-- Input Nama Kategori --}}
        <div class="form-group">
            <label for="nama_kategori">Nama Kategori <span style="color: #dc2626;">*</span></label>
            <input 
                type="text" 
                id="nama_kategori" 
                name="nama_kategori" 
                class="form-control" 
                placeholder="Contoh: Pemrograman, Fiksi, Sains..." 
                value="{{ old('nama_kategori') }}"
                required
            >
            @error('nama_kategori')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        {{-- Input Deskripsi --}}
        <div class="form-group">
            <label for="deskripsi">Deskripsi (Opsional)</label>
            <textarea 
                id="deskripsi" 
                name="deskripsi" 
                class="form-control" 
                placeholder="Penjelasan singkat mengenai kategori ini..."
            >{{ old('deskripsi') }}</textarea>
            @error('deskripsi')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        {{-- Pilihan Status --}}
        <div class="form-group">
            <label for="status">Status <span style="color: #dc2626;">*</span></label>
            <select id="status" name="status" class="form-control" required>
                <option value="aktif" {{ old('status', 'aktif') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="nonaktif" {{ old('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
            </select>
            @error('status')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        {{-- Tombol Aksi --}}
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Simpan Kategori</button>
            <a href="{{ route('petugas.kategori.index') }}" class="btn btn-secondary">Batal</a>
        </div>

    </form>

</div>
@endsection