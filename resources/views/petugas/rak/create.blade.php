@extends('layouts.app')

@section('title', 'Tambah Rak Buku Baru')

@section('content')
<div class="card-box" style="max-width: 650px; margin: 0 auto;">

    <div style="margin-bottom: 24px; border-bottom: 1px solid #e2e8f0; padding-bottom: 16px;">
        <h2 style="font-size: 18px; font-weight: 700; color: #0f172a; margin-bottom: 4px;">Tambah Rak Buku Baru</h2>
        <p style="color: #64748b; font-size: 13.5px; margin: 0;">Daftarkan lokasi penyimpanan fisik buku agar mudah dicari oleh siswa dan petugas.</p>
    </div>

    @if (isset($errors) && $errors->any())
        <div class="alert alert-danger">
            <ul style="margin-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('petugas.rak.store') }}" method="POST">
        @csrf

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 16px;">
            <div class="form-group">
                <label for="kode_rak">Kode Rak <span style="color: #dc2626;">*</span></label>
                <input
                    type="text"
                    id="kode_rak"
                    name="kode_rak"
                    class="form-control"
                    value="{{ old('kode_rak') }}"
                    placeholder="Contoh: RAK-A1"
                    required
                    autofocus
                >
            </div>

            <div class="form-group">
                <label for="nama_rak">Nama Rak Buku <span style="color: #dc2626;">*</span></label>
                <input
                    type="text"
                    id="nama_rak"
                    name="nama_rak"
                    class="form-control"
                    value="{{ old('nama_rak') }}"
                    placeholder="Contoh: Rak Sastra & Novel"
                    required
                >
            </div>
        </div>

        <div class="form-group">
            <label for="lokasi_gedung">Lokasi Gedung / Ruangan / Lantai</label>
            <input
                type="text"
                id="lokasi_gedung"
                name="lokasi_gedung"
                class="form-control"
                value="{{ old('lokasi_gedung') }}"
                placeholder="Contoh: Lantai 1 - Sayap Timur, Dekat Jendela"
            >
        </div>

        <div class="form-group">
            <label for="keterangan">Keterangan / Catatan Tambahan (Opsional)</label>
            <textarea
                id="keterangan"
                name="keterangan"
                class="form-control"
                rows="3"
                placeholder="Deskripsi jenis koleksi atau kapasitas rak..."
            >{{ old('keterangan') }}</textarea>
        </div>

        <div class="form-group">
            <label for="status">Status Operasional <span style="color: #dc2626;">*</span></label>
            <select id="status" name="status" class="form-control" required>
                <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif (Siap Digunakan)</option>
                <option value="nonaktif" {{ old('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif (Sedang Perbaikan / Penuh)</option>
            </select>
        </div>

        <div class="form-actions" style="margin-top: 24px; padding-top: 20px; border-top: 1px solid #e2e8f0;">
            <button type="submit" class="btn btn-primary">Simpan Rak Buku</button>
            <a href="{{ route('petugas.rak.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>

</div>
@endsection
