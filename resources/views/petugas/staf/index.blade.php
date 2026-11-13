@extends('layouts.app')

@section('title', 'Manajemen Petugas & Hak Akses')

@section('content')
<div style="display: flex; flex-direction: column; gap: 22px;">

    {{-- Alert Messages --}}
    @if (session('success'))
        <div class="alert alert-success">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    {{-- Metric Stat Cards --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
        
        <div class="card-box" style="margin-bottom: 0; padding: 18px 20px;">
            <span style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Total Staf</span>
            <div style="font-size: 26px; font-weight: 800; color: #0f172a; margin: 4px 0 2px;">{{ $counts['total'] }}</div>
            <span style="font-size: 12px; color: #64748b;">Pengelola perpustakaan</span>
        </div>

        <div class="card-box" style="margin-bottom: 0; padding: 18px 20px; border-left: 4px solid #4f46e5;">
            <span style="font-size: 12px; color: #4f46e5; font-weight: 600; text-transform: uppercase;">Kepala Perpus / Admin</span>
            <div style="font-size: 26px; font-weight: 800; color: #4f46e5; margin: 4px 0 2px;">{{ $counts['admin'] }}</div>
            <span style="font-size: 12px; color: #4338ca;">Akses sistem penuh</span>
        </div>

        <div class="card-box" style="margin-bottom: 0; padding: 18px 20px; border-left: 4px solid #0284c7;">
            <span style="font-size: 12px; color: #0284c7; font-weight: 600; text-transform: uppercase;">Petugas Sirkulasi</span>
            <div style="font-size: 26px; font-weight: 800; color: #0284c7; margin: 4px 0 2px;">{{ $counts['petugas'] }}</div>
            <span style="font-size: 12px; color: #0369a1;">Operasional meja sirkulasi</span>
        </div>

        <div class="card-box" style="margin-bottom: 0; padding: 18px 20px; border-left: 4px solid #10b981;">
            <span style="font-size: 12px; color: #059669; font-weight: 600; text-transform: uppercase;">Staf Aktif</span>
            <div style="font-size: 26px; font-weight: 800; color: #059669; margin: 4px 0 2px;">{{ $counts['aktif'] }}</div>
            <span style="font-size: 12px; color: #047857;">Akun dapat login</span>
        </div>

    </div>

    {{-- Main Container Card --}}
    <div class="card-box">
        
        {{-- Toolbar --}}
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid #f1f5f9;">
            
            {{-- Filter Tabs --}}
            <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
                <a href="{{ route('petugas.staf.index') }}" style="padding: 6px 14px; border-radius: 9999px; font-size: 12.5px; font-weight: 600; text-decoration: none; {{ empty($roleFilter) ? 'background: #4f46e5; color: #ffffff;' : 'background: #f1f5f9; color: #64748b;' }}">
                    Semua Role ({{ $counts['total'] }})
                </a>
                <a href="{{ route('petugas.staf.index', ['role' => 'admin']) }}" style="padding: 6px 14px; border-radius: 9999px; font-size: 12.5px; font-weight: 600; text-decoration: none; {{ $roleFilter === 'admin' ? 'background: #4f46e5; color: #ffffff;' : 'background: #eef2ff; color: #4338ca;' }}">
                    Admin ({{ $counts['admin'] }})
                </a>
                <a href="{{ route('petugas.staf.index', ['role' => 'petugas']) }}" style="padding: 6px 14px; border-radius: 9999px; font-size: 12.5px; font-weight: 600; text-decoration: none; {{ $roleFilter === 'petugas' ? 'background: #0284c7; color: #ffffff;' : 'background: #f0f9ff; color: #0369a1;' }}">
                    Petugas ({{ $counts['petugas'] }})
                </a>
            </div>

            {{-- Action & Search --}}
            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                <form action="{{ route('petugas.staf.index') }}" method="GET" style="margin: 0; display: flex; gap: 6px;">
                    @if (!empty($roleFilter))
                        <input type="hidden" name="role" value="{{ $roleFilter }}">
                    @endif
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, user, NIP..." style="height: 38px; padding: 6px 14px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; min-width: 210px;">
                    <button type="submit" class="btn btn-primary" style="padding: 0 14px; height: 38px; border-radius: 10px; font-size: 13px;">Cari</button>
                </form>

                <a href="{{ route('petugas.staf.create') }}" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 6px; height: 38px; padding: 0 18px; border-radius: 10px; font-size: 13px; text-decoration: none; font-weight: 700;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Tambah Staf
                </a>
            </div>

        </div>

        {{-- Table --}}
        @if ($staf->count() > 0)
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 45px;">No</th>
                            <th>Nama Petugas & Pegawai</th>
                            <th>Username & Email</th>
                            <th style="width: 170px;">Peran / Hak Akses</th>
                            <th style="width: 110px;">Status</th>
                            <th style="width: 140px;">Terakhir Login</th>
                            <th style="width: 130px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($staf as $s)
                            <tr>
                                <td style="color: #94a3b8; font-weight: 600;">
                                    {{ $loop->iteration + ($staf->currentPage() - 1) * $staf->perPage() }}
                                </td>
                                <td>
                                    <div style="display: flex; gap: 10px; align-items: center;">
                                        <div style="width: 36px; height: 36px; border-radius: 10px; background: {{ $s->role === 'admin' ? 'linear-gradient(135deg, #4f46e5, #7c3aed)' : 'linear-gradient(135deg, #0284c7, #38bdf8)' }}; color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 13px; flex-shrink: 0;">
                                            {{ strtoupper(substr($s->nama_lengkap, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div style="font-weight: 700; color: #0f172a; font-size: 13.5px;">
                                                {{ $s->nama_lengkap }}
                                                @if ($s->id === auth('petugas')->id())
                                                    <span style="background: #eef2ff; color: #4f46e5; font-size: 11px; padding: 2px 7px; border-radius: 9999px; font-weight: 700; margin-left: 4px;">Anda</span>
                                                @endif
                                            </div>
                                            <div style="font-size: 12px; color: #64748b; margin-top: 2px;">
                                                NIP: <strong>{{ $s->nomor_pegawai }}</strong> &bull; Telp: {{ $s->no_telepon ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: #1e293b; font-size: 13px;">
                                        <code>{{ $s->nama_pengguna }}</code>
                                    </div>
                                    <div style="font-size: 12px; color: #64748b; margin-top: 2px;">
                                        {{ $s->email }}
                                    </div>
                                </td>
                                <td>
                                    @if ($s->role === 'admin')
                                        <div style="display: inline-flex; align-items: center; gap: 6px; background: #eef2ff; color: #4338ca; border: 1px solid #c7d2fe; padding: 4px 10px; border-radius: 9999px; font-size: 12px; font-weight: 700;">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                            Kepala Perpus (Admin)
                                        </div>
                                    @else
                                        <div style="display: inline-flex; align-items: center; gap: 6px; background: #f0f9ff; color: #0369a1; border: 1px solid #bae6fd; padding: 4px 10px; border-radius: 9999px; font-size: 12px; font-weight: 700;">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/></svg>
                                            Petugas Sirkulasi
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if ($s->status === 'aktif')
                                        <span class="badge badge-aktif">Aktif</span>
                                    @else
                                        <span class="badge badge-danger">Nonaktif</span>
                                    @endif
                                </td>
                                <td style="color: #64748b; font-size: 12.5px;">
                                    @if ($s->terakhir_login)
                                        {{ \Carbon\Carbon::parse($s->terakhir_login)->translatedFormat('d M Y') }}
                                        <div style="font-size: 11px; color: #94a3b8;">{{ \Carbon\Carbon::parse($s->terakhir_login)->format('H:i') }} WIB</div>
                                    @else
                                        <span style="color: #94a3b8;">Belum pernah</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    <div style="display: inline-flex; gap: 6px; align-items: center;">
                                        <a href="{{ route('petugas.staf.edit', $s->id) }}" class="btn" style="background: #f8fafc; border: 1px solid #cbd5e1; color: #334155; padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; text-decoration: none;" title="Edit Akun & Peran">
                                            Edit
                                        </a>

                                        @if ($s->id !== auth('petugas')->id())
                                            <form action="{{ route('petugas.staf.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus staf {{ $s->nama_lengkap }}? Tindakan ini tidak dapat dibatalkan.');" style="margin: 0;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" style="background: #fff1f2; color: #e11d48; border: 1px solid #fecdd3; padding: 6px 10px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer;" title="Hapus Akun">
                                                    Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pagination">
                {{ $staf->links() }}
            </div>
        @else
            <div style="text-align: center; padding: 48px 20px;">
                <h3 style="font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 4px;">Tidak Ada Data Staf</h3>
                <p style="color: #64748b; font-size: 13.5px; margin: 0;">Belum ada akun petugas dengan filter pencarian tersebut.</p>
            </div>
        @endif

    </div>

</div>
@endsection

