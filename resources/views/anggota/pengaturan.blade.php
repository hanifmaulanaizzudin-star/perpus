@extends('layouts.anggota')

@section('title', 'Pengaturan Akun & Profil')

@section('content')
<div style="max-width: 900px; margin: 0 auto; display: flex; flex-direction: column; gap: 24px;">

    {{-- Header Banner --}}
    <div style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%); color: #ffffff; border-radius: 18px; padding: 26px 30px; box-shadow: 0 4px 16px rgba(15, 23, 42, 0.12); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div style="display: flex; align-items: center; gap: 18px;">
            <div style="width: 58px; height: 58px; border-radius: 50%; background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); color: #ffffff; font-weight: 800; font-size: 24px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4); flex-shrink: 0;">
                {{ strtoupper(substr($anggota->nama_lengkap ?? 'A', 0, 1)) }}
            </div>
            <div>
                <h1 style="font-size: 22px; font-weight: 800; margin-bottom: 4px; letter-spacing: -0.02em;">
                    {{ $anggota->nama_lengkap }}
                </h1>
                <div style="font-size: 13px; opacity: 0.85; display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <span>NISN: <strong>{{ $anggota->nomor_induk }}</strong></span>
                    <span>&bull;</span>
                    <span>Kelas: <strong>{{ $anggota->kelas ?? '-' }} {{ $anggota->jurusan ? '(' . $anggota->jurusan . ')' : '' }}</strong></span>
                    <span>&bull;</span>
                    <span class="badge badge-aktif" style="background: #059669; color: #fff; border: none; font-size: 11px; padding: 2px 8px;">
                        {{ ucfirst($anggota->status_keanggotaan ?? 'Aktif') }}
                    </span>
                </div>
            </div>
        </div>
        <div>
            <a href="{{ route('anggota.kartu-saya') }}" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 700; border-radius: 10px; padding: 8px 16px;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                Lihat Kartu Anggota
            </a>
        </div>
    </div>

    {{-- Grid Form & Informasi --}}
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">

        {{-- Kolom 1: Perbarui Kontak & WhatsApp --}}
        <div class="card-box" style="margin-bottom: 0;">
            <div style="margin-bottom: 20px; padding-bottom: 14px; border-bottom: 1px solid #edf2f7;">
                <h2 style="font-size: 16px; font-weight: 700; color: #0f172a; margin-bottom: 3px; display: flex; align-items: center; gap: 8px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2.2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    Kontak & Notifikasi
                </h2>
                <p style="font-size: 12.5px; color: #64748b; margin: 0;">
                    Nomor WhatsApp digunakan untuk menerima notifikasi otomatis batas pinjaman & ketersediaan antrean buku.
                </p>
            </div>

            @if (session('success_profil'))
                <div class="alert alert-success" style="background-color: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; padding: 10px 14px; border-radius: 10px; margin-bottom: 16px; font-size: 13px;">
                    {{ session('success_profil') }}
                </div>
            @endif

            @if ($errors->has('email') || $errors->has('no_telepon') || $errors->has('alamat'))
                <div class="alert alert-danger" style="background-color: #fef2f2; color: #991b1b; border: 1px solid #fecaca; padding: 10px 14px; border-radius: 10px; margin-bottom: 16px; font-size: 13px;">
                    {{ $errors->first('email') ?: ($errors->first('no_telepon') ?: $errors->first('alamat')) }}
                </div>
            @endif

            <form action="{{ route('anggota.pengaturan.profil') }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Nomor WhatsApp --}}
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">
                        Nomor HP / WhatsApp Aktif
                    </label>
                    <input type="text" name="no_telepon" value="{{ old('no_telepon', $anggota->no_telepon) }}" placeholder="Contoh: 08123456789" style="width: 100%; height: 42px; border: 1px solid #cbd5e1; border-radius: 10px; padding: 8px 14px; font-size: 13.5px; font-family: inherit;">
                    <small style="color: #64748b; font-size: 11.5px; margin-top: 4px; display: block;">Pastikan nomor terhubung dengan WhatsApp aktif.</small>
                </div>

                {{-- Email --}}
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">
                        Alamat Email Siswa
                    </label>
                    <input type="email" name="email" value="{{ old('email', $anggota->email) }}" placeholder="email@siswa.sch.id" style="width: 100%; height: 42px; border: 1px solid #cbd5e1; border-radius: 10px; padding: 8px 14px; font-size: 13.5px; font-family: inherit;">
                </div>

                {{-- Alamat Rumah --}}
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">
                        Alamat Domisili
                    </label>
                    <textarea name="alamat" rows="3" placeholder="Alamat tempat tinggal..." style="width: 100%; border: 1px solid #cbd5e1; border-radius: 10px; padding: 10px 14px; font-size: 13.5px; font-family: inherit; resize: vertical;">{{ old('alamat', $anggota->alamat) }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 10px; border-radius: 10px; font-size: 13.5px; font-weight: 700;">
                    Simpan Perubahan Kontak
                </button>
            </form>
        </div>

        {{-- Kolom 2: Ganti Password Siswa --}}
        <div class="card-box" style="margin-bottom: 0;">
            <div style="margin-bottom: 20px; padding-bottom: 14px; border-bottom: 1px solid #edf2f7;">
                <h2 style="font-size: 16px; font-weight: 700; color: #0f172a; margin-bottom: 3px; display: flex; align-items: center; gap: 8px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2.2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    Ubah Kata Sandi Akun
                </h2>
                <p style="font-size: 12.5px; color: #64748b; margin: 0;">
                    Ganti kata sandi secara berkala untuk menjaga keamanan akun perpustakaan kamu.
                </p>
            </div>

            @if (session('success_password'))
                <div class="alert alert-success" style="background-color: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; padding: 10px 14px; border-radius: 10px; margin-bottom: 16px; font-size: 13px;">
                    {{ session('success_password') }}
                </div>
            @endif

            @if ($errors->has('kata_sandi_lama') || $errors->has('kata_sandi'))
                <div class="alert alert-danger" style="background-color: #fef2f2; color: #991b1b; border: 1px solid #fecaca; padding: 10px 14px; border-radius: 10px; margin-bottom: 16px; font-size: 13px;">
                    {{ $errors->first('kata_sandi_lama') ?: $errors->first('kata_sandi') }}
                </div>
            @endif

            <form action="{{ route('anggota.pengaturan.password') }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Password Lama --}}
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">
                        Kata Sandi Saat Ini <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="password" name="kata_sandi_lama" required placeholder="Masukkan kata sandi saat ini" style="width: 100%; height: 42px; border: 1px solid #cbd5e1; border-radius: 10px; padding: 8px 14px; font-size: 13.5px; font-family: inherit;">
                </div>

                {{-- Password Baru --}}
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">
                        Kata Sandi Baru <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="password" name="kata_sandi" required placeholder="Minimal 6 karakter" style="width: 100%; height: 42px; border: 1px solid #cbd5e1; border-radius: 10px; padding: 8px 14px; font-size: 13.5px; font-family: inherit;">
                </div>

                {{-- Konfirmasi Password Baru --}}
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">
                        Ulangi Kata Sandi Baru <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="password" name="kata_sandi_confirmation" required placeholder="Ketik ulang kata sandi baru" style="width: 100%; height: 42px; border: 1px solid #cbd5e1; border-radius: 10px; padding: 8px 14px; font-size: 13.5px; font-family: inherit;">
                </div>

                <button type="submit" class="btn" style="width: 100%; padding: 10px; border-radius: 10px; font-size: 13.5px; font-weight: 700; background: #0f172a; color: #ffffff; border: none; cursor: pointer;">
                    Perbarui Kata Sandi
                </button>
            </form>
        </div>

    </div>

    {{-- Catatan Informasi Kebijakan --}}
    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-left: 4px solid #4f46e5; border-radius: 12px; padding: 16px 20px; display: flex; gap: 14px; align-items: flex-start;">
        <div style="background: #eef2ff; color: #4f46e5; width: 34px; height: 34px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 16px;">
            ℹ️
        </div>
        <div style="font-size: 13px; color: #475569; line-height: 1.5;">
            <div style="font-weight: 700; color: #0f172a; margin-bottom: 2px;">Informasi Perubahan Identitas Resmi</div>
            Data identitas pokok seperti <strong>Nama Lengkap</strong>, <strong>NISN</strong>, dan <strong>Kelas</strong> dikunci demi menjaga keaslian data perpustakaan sekolah. Jika terdapat kesalahan penulisan nama atau kelas, silakan temui petugas di Meja Sirkulasi Perpustakaan.
        </div>
    </div>

</div>
@endsection
