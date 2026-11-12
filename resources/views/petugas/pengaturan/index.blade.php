@extends('layouts.app')

@section('title', 'Pengaturan Operasional Perpustakaan')

@section('content')
<div style="max-width: 860px; margin: 0 auto; display: flex; flex-direction: column; gap: 24px;">

    {{-- Header --}}
    <div>
        <h1 style="font-size: 22px; font-weight: 800; color: #0f172a; margin-bottom: 4px; letter-spacing: -0.02em;">
            Pengaturan Perpustakaan
        </h1>
        <p style="color: #64748b; font-size: 13.5px; margin: 0;">
            Sesuaikan identitas perpustakaan, durasi peminjaman, batas kuota buku, dan kebijakan denda harian.
        </p>
    </div>

    {{-- Alerts --}}
    @if (session('success'))
        <div class="alert alert-success" style="font-weight: 600; font-size: 14px;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-right: 6px; flex-shrink: 0;">
                <polyline points="20 6 9 17 4 12"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('petugas.pengaturan.update') }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Card 1: Identitas Perpustakaan --}}
        <div class="card-box" style="margin-bottom: 20px;">
            <h2 style="font-size: 15px; font-weight: 700; color: #0f172a; margin-bottom: 6px; display: flex; align-items: center; gap: 8px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
                Identitas Lembaga
            </h2>
            <p style="font-size: 12.5px; color: #64748b; margin-bottom: 18px;">
                Nama dan alamat ini akan otomatis tercetak pada kop laporan resmi, kartu anggota, dan kuitansi pengembalian.
            </p>

            <div class="form-group">
                <label for="nama_perpustakaan">Nama Perpustakaan <span style="color: #dc2626;">*</span></label>
                <input 
                    type="text" 
                    id="nama_perpustakaan" 
                    name="nama_perpustakaan" 
                    class="form-control" 
                    value="{{ old('nama_perpustakaan', $pengaturan->nama_perpustakaan ?? '') }}"
                    placeholder="Contoh: Perpustakaan SMA Negeri 1 Harapan Bangsa"
                    required
                >
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label for="alamat">Alamat Lengkap & Kontak Perpustakaan</label>
                <textarea 
                    id="alamat" 
                    name="alamat" 
                    class="form-control" 
                    rows="3"
                    placeholder="Contoh: Jl. Ki Hajar Dewantara No. 10, Jakarta Pusat &bull; Telp: (021) 7890123"
                >{{ old('alamat', $pengaturan->alamat ?? '') }}</textarea>
            </div>
        </div>

        {{-- Card 2: Kebijakan Sirkulasi & Kuota --}}
        <div class="card-box" style="margin-bottom: 20px;">
            <h2 style="font-size: 15px; font-weight: 700; color: #0f172a; margin-bottom: 6px; display: flex; align-items: center; gap: 8px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
                Kebijakan Durasi & Batas Kuota Pinjam
            </h2>
            <p style="font-size: 12.5px; color: #64748b; margin-bottom: 18px;">
                Aturan masa pinjam otomatis digunakan saat membuat transaksi peminjaman baru di meja sirkulasi.
            </p>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="lama_pinjam_hari">Lama Pinjam Standar (Hari) <span style="color: #dc2626;">*</span></label>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <input 
                            type="number" 
                            id="lama_pinjam_hari" 
                            name="lama_pinjam_hari" 
                            class="form-control" 
                            value="{{ old('lama_pinjam_hari', $pengaturan->lama_pinjam_hari ?? 7) }}"
                            min="1"
                            max="90"
                            required
                        >
                        <span style="font-size: 13px; color: #64748b; font-weight: 600;">Hari</span>
                    </div>
                    <small style="color: #64748b; font-size: 11.5px; display: block; margin-top: 4px;">
                        Buku otomatis jatuh tempo setelah X hari dari tanggal peminjaman.
                    </small>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label for="maksimal_buku_per_anggota">Batas Maksimal Buku per Siswa <span style="color: #dc2626;">*</span></label>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <input 
                            type="number" 
                            id="maksimal_buku_per_anggota" 
                            name="maksimal_buku_per_anggota" 
                            class="form-control" 
                            value="{{ old('maksimal_buku_per_anggota', $pengaturan->maksimal_buku_per_anggota ?? 3) }}"
                            min="1"
                            max="30"
                            required
                        >
                        <span style="font-size: 13px; color: #64748b; font-weight: 600;">Buku</span>
                    </div>
                    <small style="color: #64748b; font-size: 11.5px; display: block; margin-top: 4px;">
                        Mencegah satu siswa memborong terlalu banyak buku sekaligus.
                    </small>
                </div>
            </div>
        </div>

        {{-- Card 3: Kebijakan Denda Keterlambatan --}}
        <div class="card-box" style="margin-bottom: 24px;">
            <h2 style="font-size: 15px; font-weight: 700; color: #0f172a; margin-bottom: 6px; display: flex; align-items: center; gap: 8px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <line x1="12" y1="1" x2="12" y2="23"/>
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                </svg>
                Kebijakan Denda Keterlambatan
            </h2>
            <p style="font-size: 12.5px; color: #64748b; margin-bottom: 18px;">
                Pengaturan tarif denda otomatis yang dikalikan dengan jumlah hari keterlambatan saat pengembalian buku.
            </p>

            <div style="margin-bottom: 16px; padding: 12px 16px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; display: flex; align-items: center; gap: 12px;">
                <input 
                    type="checkbox" 
                    id="gunakan_denda" 
                    name="gunakan_denda" 
                    value="1" 
                    style="width: 18px; height: 18px; cursor: pointer;"
                    {{ old('gunakan_denda', $pengaturan->gunakan_denda ?? true) ? 'checked' : '' }}
                >
                <label for="gunakan_denda" style="margin: 0; font-size: 13.5px; font-weight: 600; color: #0f172a; cursor: pointer;">
                    Aktifkan Sistem Denda Keterlambatan
                </label>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label for="denda_per_hari">Tarif Denda per Hari (Rp) <span style="color: #dc2626;">*</span></label>
                <div style="display: flex; align-items: center; gap: 8px; max-width: 300px;">
                    <span style="font-size: 13px; color: #64748b; font-weight: 700;">Rp</span>
                    <input 
                        type="number" 
                        id="denda_per_hari" 
                        name="denda_per_hari" 
                        class="form-control" 
                        value="{{ old('denda_per_hari', (int) ($pengaturan->denda_per_hari ?? 1000)) }}"
                        min="0"
                        step="500"
                        required
                    >
                    <span style="font-size: 12px; color: #64748b;">/ buku / hari</span>
                </div>
                <small style="color: #64748b; font-size: 11.5px; display: block; margin-top: 4px;">
                    Contoh: Jika telat 3 hari untuk 1 buku dengan tarif Rp 1.000, maka denda = Rp 3.000.
                </small>
            </div>
        </div>

        {{-- Actions --}}
        <div style="display: flex; gap: 12px; align-items: center;">
            <button type="submit" class="btn btn-primary" style="font-weight: 700; padding: 10px 24px; box-shadow: 0 4px 14px rgba(79, 70, 229, 0.3);">
                Simpan Perubahan Pengaturan
            </button>
            <a href="{{ route('petugas.dashboard') }}" class="btn btn-secondary">
                Kembali ke Dashboard
            </a>
        </div>

    </form>

</div>
@endsection

