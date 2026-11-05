@extends('layouts.app')

@section('title', 'Tambah Anggota Baru')

@section('content')
<div class="card-box" style="max-width: 820px;">

    <div style="margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid #e2e8f0;">
        <h1 style="font-size: 18px; font-weight: 700; color: #0f172a; margin-bottom: 4px;">
            Form Tambah Anggota
        </h1>
        <p style="color: #64748b; font-size: 13.5px; margin: 0;">
            Daftarkan siswa, guru, atau staf sebagai anggota aktif perpustakaan.
        </p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Terdapat kesalahan pada isian form:</strong>
            <ul style="margin: 6px 0 0 18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('petugas.anggota.store') }}" method="POST">
        @csrf

        {{-- Row 1: Nomor Induk & Nama Lengkap --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div class="form-group">
                <label for="nomor_induk">Nomor Induk / NISN <span style="color: #dc2626;">*</span></label>
                <input 
                    type="text" 
                    id="nomor_induk" 
                    name="nomor_induk" 
                    class="form-control" 
                    value="{{ old('nomor_induk') }}" 
                    placeholder="Contoh: 2026001" 
                    required
                >
            </div>

            <div class="form-group">
                <label for="nama_lengkap">Nama Lengkap <span style="color: #dc2626;">*</span></label>
                <input 
                    type="text" 
                    id="nama_lengkap" 
                    name="nama_lengkap" 
                    class="form-control" 
                    value="{{ old('nama_lengkap') }}" 
                    placeholder="Masukkan nama lengkap" 
                    required
                >
            </div>
        </div>

        {{-- Row 2: Username & Password --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div class="form-group">
                <label for="nama_pengguna">Username Akun <span style="color: #dc2626;">*</span></label>
                <input 
                    type="text" 
                    id="nama_pengguna" 
                    name="nama_pengguna" 
                    class="form-control" 
                    value="{{ old('nama_pengguna') }}" 
                    placeholder="Username untuk login" 
                    required
                >
            </div>

            <div class="form-group">
                <label for="kata_sandi">Password Akun <span style="color: #dc2626;">*</span></label>
                <input 
                    type="password" 
                    id="kata_sandi" 
                    name="kata_sandi" 
                    class="form-control" 
                    placeholder="Minimal 8 karakter" 
                    required
                >
            </div>
        </div>

        {{-- Row 3: Email & No Telepon --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div class="form-group">
                <label for="email">Alamat Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-control" 
                    value="{{ old('email') }}" 
                    placeholder="nama@email.com"
                >
            </div>

            <div class="form-group">
                <label for="no_telepon">Nomor Telepon / WhatsApp</label>
                <input 
                    type="text" 
                    id="no_telepon" 
                    name="no_telepon" 
                    class="form-control" 
                    value="{{ old('no_telepon') }}" 
                    placeholder="08xxxxxxxxxx"
                >
            </div>
        </div>

        {{-- Row 4: Kelas, Jurusan, Status --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px;">
            <div class="form-group">
                <label for="kelas">Kelas</label>
                <input 
                    type="text" 
                    id="kelas" 
                    name="kelas" 
                    class="form-control" 
                    value="{{ old('kelas') }}" 
                    placeholder="Contoh: XI PPLG 1"
                >
            </div>

            <div class="form-group">
                <label for="jurusan">Jurusan</label>
                <input 
                    type="text" 
                    id="jurusan" 
                    name="jurusan" 
                    class="form-control" 
                    value="{{ old('jurusan') }}" 
                    placeholder="Contoh: PPLG"
                >
            </div>

            <div class="form-group">
                <label for="status_keanggotaan">Status <span style="color: #dc2626;">*</span></label>
                <select id="status_keanggotaan" name="status_keanggotaan" class="form-control" required>
                    <option value="aktif" {{ old('status_keanggotaan', 'aktif') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ old('status_keanggotaan') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    <option value="ditangguhkan" {{ old('status_keanggotaan') == 'ditangguhkan' ? 'selected' : '' }}>Ditangguhkan</option>
                </select>
            </div>
        </div>

        {{-- Row 5: Tanggal Daftar --}}
        <div class="form-group">
            <label for="tanggal_daftar">Tanggal Pendaftaran <span style="color: #dc2626;">*</span></label>
            <input 
                type="date" 
                id="tanggal_daftar" 
                name="tanggal_daftar" 
                class="form-control" 
                value="{{ old('tanggal_daftar', date('Y-m-d')) }}" 
                required
            >
        </div>

        {{-- Row 6: Alamat --}}
        <div class="form-group">
            <label for="alamat">Alamat Domisili</label>
            <textarea 
                id="alamat" 
                name="alamat" 
                class="form-control" 
                placeholder="Masukkan alamat tinggal anggota..."
            >{{ old('alamat') }}</textarea>
        </div>

        {{-- Actions --}}
        <div class="form-actions" style="margin-top: 24px; padding-top: 16px; border-top: 1px solid #e2e8f0; display: flex; gap: 10px;">
            <button type="submit" class="btn btn-primary">Simpan Anggota</button>
            <a href="{{ route('petugas.anggota.index') }}" class="btn btn-secondary">Batal</a>
        </div>

    </form>

</div>
@endsection
