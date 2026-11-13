@extends('layouts.app')

@section('title', 'Edit Data Staf & Hak Akses')

@section('content')
<div style="max-width: 800px; margin: 0 auto; display: flex; flex-direction: column; gap: 20px;">

    {{-- Error Banner --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <div>
                <strong>Terdapat kesalahan input:</strong>
                <ul style="margin: 6px 0 0 16px; padding: 0;">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="card-box">
        
        {{-- Header Form --}}
        <div style="margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid #edf2f7; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 style="font-size: 18px; font-weight: 800; color: #0f172a; margin-bottom: 4px;">
                    Edit Informasi Staf: {{ $staf->nama_lengkap }}
                </h2>
                <p style="color: #64748b; font-size: 13px; margin: 0;">
                    Perbarui profil, hak akses peran, atau atur ulang kata sandi petugas.
                </p>
            </div>
            <a href="{{ route('petugas.staf.index') }}" class="btn" style="background: #f1f5f9; color: #475569; text-decoration: none; font-size: 13px; padding: 8px 16px; border-radius: 10px; font-weight: 600;">
                &larr; Kembali
            </a>
        </div>

        <form action="{{ route('petugas.staf.update', $staf->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 18px;">
                
                {{-- NIP / Nomor Pegawai --}}
                <div class="form-group">
                    <label class="form-label" style="font-weight: 600; color: #334155; margin-bottom: 6px; display: block;">
                        Nomor Pegawai / NIP <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text" name="nomor_pegawai" class="form-control" value="{{ old('nomor_pegawai', $staf->nomor_pegawai) }}" required style="width: 100%; height: 42px; border: 1px solid #cbd5e1; border-radius: 10px; padding: 8px 14px; font-size: 13.5px;">
                </div>

                {{-- Nama Lengkap --}}
                <div class="form-group">
                    <label class="form-label" style="font-weight: 600; color: #334155; margin-bottom: 6px; display: block;">
                        Nama Lengkap Beserta Gelar <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text" name="nama_lengkap" class="form-control" value="{{ old('nama_lengkap', $staf->nama_lengkap) }}" required style="width: 100%; height: 42px; border: 1px solid #cbd5e1; border-radius: 10px; padding: 8px 14px; font-size: 13.5px;">
                </div>

            </div>

            {{-- Pemilihan Peran / Hak Akses --}}
            <div style="margin: 20px 0; padding: 18px; background: #f8fafc; border-radius: 14px; border: 1px solid #e2e8f0;">
                <label style="font-weight: 700; color: #0f172a; margin-bottom: 10px; display: block; font-size: 13.5px;">
                    Peran & Tingkat Hak Akses <span style="color: #ef4444;">*</span>
                </label>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    
                    {{-- Role Petugas --}}
                    <label style="display: block; border: 2px solid {{ old('role', $staf->role) === 'petugas' ? '#0284c7' : '#cbd5e1' }}; border-radius: 12px; padding: 16px; background: #ffffff; cursor: pointer; transition: all 0.15s ease;">
                        <div style="display: flex; align-items: flex-start; gap: 10px;">
                            <input type="radio" name="role" value="petugas" {{ old('role', $staf->role) === 'petugas' ? 'checked' : '' }} style="margin-top: 3px;" onchange="this.closest('form').querySelectorAll('label').forEach(l => l.style.borderColor='#cbd5e1'); this.closest('label').style.borderColor='#0284c7';">
                            <div>
                                <div style="font-weight: 700; color: #0369a1; font-size: 14px; margin-bottom: 4px;">
                                    Petugas Sirkulasi
                                </div>
                                <p style="font-size: 12px; color: #64748b; margin: 0; line-height: 1.5;">
                                    Dapat melayani peminjaman, pengembalian buku, antrean reservasi, data buku, rak, dan anggota. Tidak dapat mengakses pengaturan denda atau staf lain.
                                </p>
                            </div>
                        </div>
                    </label>

                    {{-- Role Admin --}}
                    <label style="display: block; border: 2px solid {{ old('role', $staf->role) === 'admin' ? '#4f46e5' : '#cbd5e1' }}; border-radius: 12px; padding: 16px; background: #ffffff; cursor: pointer; transition: all 0.15s ease;">
                        <div style="display: flex; align-items: flex-start; gap: 10px;">
                            <input type="radio" name="role" value="admin" {{ old('role', $staf->role) === 'admin' ? 'checked' : '' }} style="margin-top: 3px;" onchange="this.closest('form').querySelectorAll('label').forEach(l => l.style.borderColor='#cbd5e1'); this.closest('label').style.borderColor='#4f46e5';">
                            <div>
                                <div style="font-weight: 700; color: #4338ca; font-size: 14px; margin-bottom: 4px;">
                                    Kepala Perpustakaan (Admin)
                                </div>
                                <p style="font-size: 12px; color: #64748b; margin: 0; line-height: 1.5;">
                                    Akses sistem penuh: Kelola akun staf, pengaturan denda & pinjaman, serta unduh rekapan keuangan LPJ kas.
                                </p>
                            </div>
                        </div>
                    </label>

                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 18px;">
                
                {{-- Username --}}
                <div class="form-group">
                    <label class="form-label" style="font-weight: 600; color: #334155; margin-bottom: 6px; display: block;">
                        Username Login <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text" name="nama_pengguna" class="form-control" value="{{ old('nama_pengguna', $staf->nama_pengguna) }}" required style="width: 100%; height: 42px; border: 1px solid #cbd5e1; border-radius: 10px; padding: 8px 14px; font-size: 13.5px;">
                </div>

                {{-- Email --}}
                <div class="form-group">
                    <label class="form-label" style="font-weight: 600; color: #334155; margin-bottom: 6px; display: block;">
                        Alamat Email <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $staf->email) }}" required style="width: 100%; height: 42px; border: 1px solid #cbd5e1; border-radius: 10px; padding: 8px 14px; font-size: 13.5px;">
                </div>

            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-top: 16px;">
                
                {{-- Password --}}
                <div class="form-group">
                    <label class="form-label" style="font-weight: 600; color: #334155; margin-bottom: 6px; display: block;">
                        Kata Sandi Baru <small style="color: #94a3b8; font-weight: normal;">(Kosongkan bila tidak diubah)</small>
                    </label>
                    <input type="password" name="kata_sandi" class="form-control" placeholder="Biarkan kosong jika tidak diubah" style="width: 100%; height: 42px; border: 1px solid #cbd5e1; border-radius: 10px; padding: 8px 14px; font-size: 13.5px;">
                </div>

                {{-- Status Akun --}}
                <div class="form-group">
                    <label class="form-label" style="font-weight: 600; color: #334155; margin-bottom: 6px; display: block;">
                        Status Akun <span style="color: #ef4444;">*</span>
                    </label>
                    <select name="status" class="form-control" style="width: 100%; height: 42px; border: 1px solid #cbd5e1; border-radius: 10px; padding: 8px 14px; font-size: 13.5px; background: #fff;">
                        <option value="aktif" {{ old('status', $staf->status) === 'aktif' ? 'selected' : '' }}>Aktif (Dapat Login)</option>
                        <option value="nonaktif" {{ old('status', $staf->status) === 'nonaktif' ? 'selected' : '' }}>Nonaktif (Dibekukan)</option>
                    </select>
                </div>

            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-top: 16px;">
                
                {{-- No Telepon --}}
                <div class="form-group">
                    <label class="form-label" style="font-weight: 600; color: #334155; margin-bottom: 6px; display: block;">
                        Nomor HP / WhatsApp
                    </label>
                    <input type="text" name="no_telepon" class="form-control" value="{{ old('no_telepon', $staf->no_telepon) }}" placeholder="08xxxxxxxxxx" style="width: 100%; height: 42px; border: 1px solid #cbd5e1; border-radius: 10px; padding: 8px 14px; font-size: 13.5px;">
                </div>

                {{-- Alamat --}}
                <div class="form-group">
                    <label class="form-label" style="font-weight: 600; color: #334155; margin-bottom: 6px; display: block;">
                        Alamat Domisili
                    </label>
                    <input type="text" name="alamat" class="form-control" value="{{ old('alamat', $staf->alamat) }}" placeholder="Alamat singkat..." style="width: 100%; height: 42px; border: 1px solid #cbd5e1; border-radius: 10px; padding: 8px 14px; font-size: 13.5px;">
                </div>

            </div>

            <div style="margin-top: 28px; padding-top: 20px; border-top: 1px solid #edf2f7; display: flex; justify-content: flex-end; gap: 10px;">
                <a href="{{ route('petugas.staf.index') }}" class="btn" style="background: #f1f5f9; color: #475569; padding: 10px 20px; border-radius: 10px; text-decoration: none; font-size: 13.5px; font-weight: 600;">
                    Batal
                </a>
                <button type="submit" class="btn btn-primary" style="padding: 10px 24px; border-radius: 10px; font-size: 13.5px; font-weight: 700;">
                    Perbarui Akun Staf
                </button>
            </div>

        </form>

    </div>

</div>
@endsection

