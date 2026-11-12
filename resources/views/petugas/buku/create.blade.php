@extends('layouts.app')

@section('title', 'Tambah Buku Baru')

@section('content')
<div class="card-box" style="max-width: 850px;">

    <div style="margin-bottom: 25px;">
        <h3 style="font-size: 18px; margin-bottom: 6px;">Form Tambah Buku</h3>
        <p style="color: #64748b; font-size: 14px;">Tambahkan koleksi buku baru ke katalog perpustakaan.</p>
    </div>

    {{-- PENTING: Wajib pakai enctype="multipart/form-data" untuk upload file --}}
    <form action="{{ route('petugas.buku.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Baris 1: Kode Buku & ISBN --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div class="form-group">
                <label for="kode_buku">Kode Buku <span style="color: #dc2626;">*</span></label>
                <input 
                    type="text" 
                    id="kode_buku" 
                    name="kode_buku" 
                    class="form-control" 
                    placeholder="Contoh: BK-001" 
                    value="{{ old('kode_buku') }}"
                    required
                >
                @error('kode_buku')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="isbn">Nomor ISBN (Opsional)</label>
                <input 
                    type="text" 
                    id="isbn" 
                    name="isbn" 
                    class="form-control" 
                    placeholder="Contoh: 978-602-8519-93-9" 
                    value="{{ old('isbn') }}"
                >
                @error('isbn')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        {{-- Baris 2: Judul Buku --}}
        <div class="form-group">
            <label for="judul">Judul Buku <span style="color: #dc2626;">*</span></label>
            <input 
                type="text" 
                id="judul" 
                name="judul" 
                class="form-control" 
                placeholder="Masukkan judul buku lengkap..." 
                value="{{ old('judul') }}"
                required
            >
            @error('judul')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        {{-- Baris 3: Kategori & Lokasi Rak --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div class="form-group">
                <label for="kategori_id">Kategori Buku <span style="color: #dc2626;">*</span></label>
                <select id="kategori_id" name="kategori_id" class="form-control" required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach ($kategori as $kat)
                        <option value="{{ $kat->id }}" {{ old('kategori_id') == $kat->id ? 'selected' : '' }}>
                            {{ $kat->nama_kategori }}
                        </option>
                    @endforeach
                </select>
                @error('kategori_id')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                    <label for="rak_id" style="margin: 0; font-weight: 600;">Lokasi Rak Buku</label>
                    <a href="{{ route('petugas.rak.create') }}" target="_blank" style="font-size: 11.5px; color: #4338ca; text-decoration: none; font-weight: 700;">
                        + Buat Rak Baru
                    </a>
                </div>
                <select id="rak_id" name="rak_id" class="form-control">
                    <option value="">-- Pilih Rak Penyimpanan Buku --</option>
                    @foreach ($rak as $r)
                        <option value="{{ $r->id }}" {{ old('rak_id') == $r->id ? 'selected' : '' }}>
                            {{ $r->label_rak }}
                        </option>
                    @endforeach
                </select>
                @error('rak_id')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        {{-- Baris 4: Penulis, Penerbit, Tahun Terbit --}}
        <div style="display: grid; grid-template-columns: 2fr 2fr 1fr; gap: 15px;">
            <div class="form-group">
                <label for="penulis">Penulis / Pengarang</label>
                <input 
                    type="text" 
                    id="penulis" 
                    name="penulis" 
                    class="form-control" 
                    placeholder="Nama penulis" 
                    value="{{ old('penulis') }}"
                >
                @error('penulis')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="penerbit">Penerbit</label>
                <input 
                    type="text" 
                    id="penerbit" 
                    name="penerbit" 
                    class="form-control" 
                    placeholder="Nama penerbit" 
                    value="{{ old('penerbit') }}"
                >
                @error('penerbit')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="tahun_terbit">Tahun</label>
                <input 
                    type="number" 
                    id="tahun_terbit" 
                    name="tahun_terbit" 
                    class="form-control" 
                    placeholder="2024" 
                    value="{{ old('tahun_terbit') }}"
                >
                @error('tahun_terbit')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        {{-- Baris 5: Jumlah Stok & Status --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div class="form-group">
                <label for="jumlah_stok">Jumlah Stok Buku <span style="color: #dc2626;">*</span></label>
                <input 
                    type="number" 
                    id="jumlah_stok" 
                    name="jumlah_stok" 
                    class="form-control" 
                    placeholder="0" 
                    min="0"
                    value="{{ old('jumlah_stok', 1) }}"
                    required
                >
                @error('jumlah_stok')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="status">Status Buku <span style="color: #dc2626;">*</span></label>
                <select id="status" name="status" class="form-control" required>
                    <option value="tersedia" {{ old('status', 'tersedia') == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                    <option value="nonaktif" {{ old('status', 'nonaktif') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                @error('status')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        {{-- Baris 6: Upload Foto Sampul Buku + Preview --}}
        <div class="form-group">
            <label for="gambar_sampul">Foto Sampul Buku (Opsional)</label>
            <input 
                type="file" 
                id="gambar_sampul" 
                name="gambar_sampul" 
                class="form-control" 
                accept="image/*"
                onchange="previewCover(this)"
            >
            <small style="color: #64748b; font-size: 12px; display: block; margin-top: 4px;">
                Format: JPG, PNG, WEBP. Maksimal 2 MB.
            </small>
            @error('gambar_sampul')
                <span class="text-danger">{{ $message }}</span>
            @enderror

            {{-- Kotak Preview Gambar --}}
            <div id="preview-box" style="display: none; margin-top: 12px;">
                <p style="font-size: 12px; color: #64748b; margin-bottom: 5px;">Pratinjau Sampul:</p>
                <img id="preview-img" src="#" alt="Preview" style="max-height: 160px; border-radius: 8px; border: 1px solid #cbd5e1; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
            </div>
        </div>

        {{-- Baris 7: Sinopsis / Deskripsi --}}
        <div class="form-group">
            <label for="deskripsi">Deskripsi / Sinopsis Buku</label>
            <textarea 
                id="deskripsi" 
                name="deskripsi" 
                class="form-control" 
                placeholder="Ringkasan isi buku..."
            >{{ old('deskripsi') }}</textarea>
            @error('deskripsi')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        {{-- Tombol Aksi --}}
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Simpan Buku</button>
            <a href="{{ route('petugas.buku.index') }}" class="btn btn-secondary">Batal</a>
        </div>

    </form>

</div>

{{-- Script Javascript Sederhana Buat Preview Gambar Langsung --}}
<script>
    function previewCover(input) {
        const previewBox = document.getElementById('preview-box');
        const previewImg = document.getElementById('preview-img');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewBox.style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        } else {
            previewBox.style.display = 'none';
        }
    }
</script>
@endsection