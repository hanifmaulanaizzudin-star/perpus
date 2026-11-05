@extends('layouts.app')

@section('title', 'Dashboard Perpustakaan')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Header Section -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 style="font-size: 22px; font-weight: 800; color: #0f172a; margin-bottom: 4px; letter-spacing: -0.02em;">
                Halo, {{ auth('petugas')->user()->nama_lengkap }}!
            </h1>
            <p style="color: #64748b; font-size: 13.5px; margin: 0;">
                Berikut ringkasan operasional perpustakaan, sirkulasi peminjaman, dan status koleksi hari ini.
            </p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('petugas.peminjaman.create') }}" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Peminjaman Baru (Scan)
            </a>
            <a href="{{ route('petugas.pengembalian.create') }}" class="btn btn-secondary">
                Pengembalian Buku
            </a>
        </div>
    </div>

    <!-- Metric Cards Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 18px;">
        
        {{-- Total Buku --}}
        <div class="card-box" style="margin-bottom: 0; padding: 22px; display: flex; align-items: center; justify-content: space-between; transition: all 0.2s ease-in-out;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px -4px rgba(15,23,42,0.08)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 20px -2px rgba(15,23,42,0.04)'">
            <div>
                <span style="font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b;">Total Judul Buku</span>
                <div style="font-size: 32px; font-weight: 800; color: #0f172a; margin: 4px 0 2px;">{{ $totalBuku }}</div>
                <span style="font-size: 12px; color: #4f46e5; font-weight: 600;">Koleksi terdaftar</span>
            </div>
            <div style="width: 50px; height: 50px; border-radius: 14px; background: #eef2ff; color: #4f46e5; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(79, 70, 229, 0.15);">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                </svg>
            </div>
        </div>

        {{-- Anggota Aktif --}}
        <div class="card-box" style="margin-bottom: 0; padding: 22px; display: flex; align-items: center; justify-content: space-between; transition: all 0.2s ease-in-out;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px -4px rgba(15,23,42,0.08)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 20px -2px rgba(15,23,42,0.04)'">
            <div>
                <span style="font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b;">Anggota Aktif</span>
                <div style="font-size: 32px; font-weight: 800; color: #0f172a; margin: 4px 0 2px;">{{ $totalAnggota }}</div>
                <span style="font-size: 12px; color: #7c3aed; font-weight: 600;">Siswa & Guru</span>
            </div>
            <div style="width: 50px; height: 50px; border-radius: 14px; background: #f5f3ff; color: #7c3aed; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(124, 58, 237, 0.15);">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>
        </div>

        {{-- Buku Dipinjam --}}
        <div class="card-box" style="margin-bottom: 0; padding: 22px; display: flex; align-items: center; justify-content: space-between; transition: all 0.2s ease-in-out;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px -4px rgba(15,23,42,0.08)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 20px -2px rgba(15,23,42,0.04)'">
            <div>
                <span style="font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b;">Pinjaman Aktif</span>
                <div style="font-size: 32px; font-weight: 800; color: #2563eb; margin: 4px 0 2px;">{{ $peminjamanAktif }}</div>
                <span style="font-size: 12px; color: #2563eb; font-weight: 600;">Buku di luar rak</span>
            </div>
            <div style="width: 50px; height: 50px; border-radius: 14px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(37, 99, 235, 0.15);">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
            </div>
        </div>

        {{-- Terlambat --}}
        <div class="card-box" style="margin-bottom: 0; padding: 22px; display: flex; align-items: center; justify-content: space-between; transition: all 0.2s ease-in-out;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px -4px rgba(15,23,42,0.08)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 20px -2px rgba(15,23,42,0.04)'">
            <div>
                <span style="font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b;">Terlambat</span>
                <div style="font-size: 32px; font-weight: 800; color: {{ $peminjamanTerlambat > 0 ? '#dc2626' : '#0f172a' }}; margin: 4px 0 2px;">{{ $peminjamanTerlambat }}</div>
                <span style="font-size: 12px; color: {{ $peminjamanTerlambat > 0 ? '#dc2626' : '#64748b' }}; font-weight: 600;">
                    {{ $peminjamanTerlambat > 0 ? 'Perlu ditindaklanjuti' : 'Semua tepat waktu' }}
                </span>
            </div>
            <div style="width: 50px; height: 50px; border-radius: 14px; background: {{ $peminjamanTerlambat > 0 ? '#fef2f2' : '#f8fafc' }}; color: {{ $peminjamanTerlambat > 0 ? '#dc2626' : '#94a3b8' }}; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(220, 38, 38, 0.12);">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
            </div>
        </div>

    </div>

    <!-- Recent Transactions Table Card -->
    <div class="card-box" style="padding: 0; overflow: hidden;">
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 20px 26px; border-bottom: 1px solid #edf2f7;">
            <div>
                <h2 style="font-size: 16px; font-weight: 700; color: #0f172a; margin-bottom: 2px;">
                    Aktivitas Peminjaman Terkini
                </h2>
                <p style="font-size: 13px; color: #64748b; margin: 0;">Transaksi sirkulasi yang baru saja diproses sistem.</p>
            </div>
            <a href="{{ route('petugas.peminjaman.index') }}" class="btn btn-secondary" style="font-size: 12.5px; padding: 7px 14px; border-radius: 8px;">
                Lihat Seluruh Data &rarr;
            </a>
        </div>

        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th style="width: 160px;">Kode Pinjam</th>
                        <th>Nama Peminjam</th>
                        <th style="width: 140px;">Tgl Pinjam</th>
                        <th style="width: 140px;">Jatuh Tempo</th>
                        <th style="width: 130px;">Status</th>
                        <th style="width: 100px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transaksiTerbaru as $trx)
                        <tr>
                            <td>
                                <code style="font-size: 12.5px; background: #f1f5f9; padding: 3px 8px; border-radius: 6px; font-weight: 700; color: #4f46e5;">
                                    {{ $trx->kode_peminjaman }}
                                </code>
                            </td>
                            <td>
                                <div style="font-weight: 700; color: #0f172a; font-size: 13.5px;">{{ $trx->anggota->nama_lengkap ?? '-' }}</div>
                                <div style="font-size: 12px; color: #64748b;">No: {{ $trx->anggota->nomor_induk ?? '-' }}</div>
                            </td>
                            <td style="color: #475569; font-weight: 500;">{{ \Carbon\Carbon::parse($trx->tanggal_pinjam)->translatedFormat('d M Y') }}</td>
                            <td>
                                @php
                                    $isDue = \Carbon\Carbon::parse($trx->tanggal_jatuh_tempo)->isPast() && $trx->status !== 'dikembalikan';
                                @endphp
                                <span style="color: {{ $isDue ? '#dc2626' : '#1e293b' }}; font-weight: 700;">
                                    {{ \Carbon\Carbon::parse($trx->tanggal_jatuh_tempo)->translatedFormat('d M Y') }}
                                </span>
                            </td>
                            <td>
                                @if ($trx->status === 'dipinjam')
                                    <span class="badge" style="background-color: #eef2ff; color: #4338ca; border: 1px solid #c7d2fe;">Dipinjam</span>
                                @elseif ($trx->status === 'terlambat' || $isDue)
                                    <span class="badge badge-danger">Terlambat</span>
                                @elseif ($trx->status === 'dikembalikan')
                                    <span class="badge badge-aktif">Dikembalikan</span>
                                @else
                                    <span class="badge badge-neutral">{{ ucfirst($trx->status) }}</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <a href="{{ route('petugas.peminjaman.show', $trx->id) }}" class="act-detail" style="font-size: 12px; padding: 4px 10px;">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: #64748b; padding: 35px;">
                                Belum ada transaksi peminjaman hari ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
