@extends('layouts.anggota')

@section('title', 'Daftar Reservasi & Antrean Buku')

@section('content')
<div class="card-box">

    {{-- Alert Success / Error --}}
    @if (session('success'))
        <div class="alert alert-success" style="background-color: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; padding: 14px 18px; border-radius: 12px; margin-bottom: 22px; display: flex; align-items: center; gap: 10px;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger" style="background-color: #fef2f2; color: #991b1b; border: 1px solid #fecaca; padding: 14px 18px; border-radius: 12px; margin-bottom: 22px; display: flex; align-items: center; gap: 10px;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    {{-- Header --}}
    <div style="margin-bottom: 24px; padding-bottom: 18px; border-bottom: 1px solid #edf2f7; display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 14px;">
        <div>
            <h1 style="font-size: 22px; font-weight: 800; color: #0f172a; margin-bottom: 4px; letter-spacing: -0.02em;">
                Reservasi & Antrean Buku Saya
            </h1>
            <p style="color: #64748b; font-size: 13.5px; margin: 0;">
                Pantau status antrean buku yang sedang kamu booking secara online. Segera ambil buku di meja sirkulasi jika status telah siap diambil.
            </p>
        </div>
        <div style="display: flex; gap: 10px; align-items: center;">
            <a href="{{ route('anggota.katalog') }}" style="display: inline-flex; align-items: center; gap: 6px; background: #4f46e5; color: #ffffff; padding: 8px 16px; border-radius: 9999px; font-size: 13px; font-weight: 600; text-decoration: none;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                Cari Buku Lain
            </a>
            <div style="background: #eef2ff; color: #4338ca; font-weight: 700; font-size: 13px; padding: 7px 15px; border-radius: 9999px;">
                Total {{ $reservasi->count() }} Antrean
            </div>
        </div>
    </div>

    @if ($reservasi->count() > 0)
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th style="width: 160px;">Kode Reservasi</th>
                        <th>Buku yang Diantrekan</th>
                        <th style="width: 140px;">Tgl Reservasi</th>
                        <th style="width: 170px;">Status Antrean</th>
                        <th style="width: 140px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reservasi as $rsv)
                        <tr>
                            <td style="color: #94a3b8; font-weight: 600;">{{ $loop->iteration }}</td>
                            <td>
                                <code style="background: #f1f5f9; padding: 4px 8px; border-radius: 6px; font-size: 12.5px; font-weight: 700; color: #4f46e5;">{{ $rsv->kode_reservasi }}</code>
                            </td>
                            <td>
                                <div style="display: flex; gap: 12px; align-items: center;">
                                    @if ($rsv->buku && $rsv->buku->gambar_sampul)
                                        <img src="{{ asset('storage/' . $rsv->buku->gambar_sampul) }}" alt="Sampul" style="width: 40px; height: 54px; object-fit: cover; border-radius: 6px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); flex-shrink: 0;">
                                    @else
                                        <div style="width: 40px; height: 54px; background: #eef2ff; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #6366f1; border: 1px solid #e0e7ff; flex-shrink: 0;">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <div>
                                        <a href="{{ route('anggota.katalog.detail', $rsv->buku_id) }}" style="font-weight: 700; font-size: 14px; color: #0f172a; text-decoration: none; display: block; margin-bottom: 2px;">
                                            {{ $rsv->buku->judul ?? 'Buku Tidak Ditemukan' }}
                                        </a>
                                        <div style="font-size: 12px; color: #64748b;">
                                            Kategori: <span style="font-weight: 600; color: #334155;">{{ $rsv->buku->kategori->nama_kategori ?? '-' }}</span> &bull; Rak: <span style="font-weight: 600; color: #4f46e5;">{{ $rsv->buku->lokasi_rak ?? '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td style="color: #475569; font-weight: 500;">
                                {{ \Carbon\Carbon::parse($rsv->tanggal_reservasi)->translatedFormat('d M Y H:i') }}
                            </td>
                            <td>
                                @if ($rsv->status === 'menunggu')
                                    <div style="display: inline-flex; align-items: center; gap: 6px; background: #fffbeb; color: #b45309; border: 1px solid #fde68a; padding: 4px 10px; border-radius: 9999px; font-size: 12px; font-weight: 700;">
                                        <span style="width: 7px; height: 7px; background: #f59e0b; border-radius: 50%; display: inline-block;"></span>
                                        Dalam Antrean #{{ $rsv->posisi_antrean }}
                                    </div>
                                    <div style="font-size: 11px; color: #94a3b8; margin-top: 3px;">Menunggu buku dikembalikan</div>
                                @elseif ($rsv->status === 'tersedia')
                                    <div style="display: inline-flex; align-items: center; gap: 6px; background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; padding: 4px 10px; border-radius: 9999px; font-size: 12px; font-weight: 700;">
                                        <span style="width: 7px; height: 7px; background: #10b981; border-radius: 50%; display: inline-block;"></span>
                                        Buku Siap Diambil!
                                    </div>
                                    @if ($rsv->batas_ambil)
                                        <div style="font-size: 11px; color: #dc2626; font-weight: 600; margin-top: 3px;">
                                            Batas: {{ \Carbon\Carbon::parse($rsv->batas_ambil)->translatedFormat('d M Y') }}
                                        </div>
                                    @endif
                                @elseif ($rsv->status === 'selesai')
                                    <span class="badge badge-aktif">Telah Dipinjam</span>
                                @elseif ($rsv->status === 'kedaluwarsa')
                                    <span class="badge badge-danger">Kedaluwarsa</span>
                                    <div style="font-size: 11px; color: #94a3b8; margin-top: 3px;">Buku tidak diambil</div>
                                @elseif ($rsv->status === 'dibatalkan')
                                    <span style="background: #f1f5f9; color: #64748b; font-size: 12px; padding: 3px 8px; border-radius: 6px; font-weight: 600;">Dibatalkan</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                @if (in_array($rsv->status, ['menunggu', 'tersedia']))
                                    <form action="{{ route('anggota.reservasi.batal', $rsv->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan booking antrean buku ini?');" style="margin: 0; display: inline;">
                                        @csrf
                                        <button type="submit" style="background: #fff1f2; color: #e11d48; border: 1px solid #fecdd3; padding: 5px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; transition: all 0.15s ease;" onmouseover="this.style.background='#ffe4e6'" onmouseout="this.style.background='#fff1f2'">
                                            Batal Antre
                                        </button>
                                    </form>
                                @else
                                    <span style="color: #cbd5e1; font-size: 12px;">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div style="text-align: center; padding: 48px 20px;">
            <div style="width: 64px; height: 64px; border-radius: 50%; background: #f1f5f9; color: #94a3b8; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
            </div>
            <h3 style="font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 6px;">
                Belum Ada Antrean Buku
            </h3>
            <p style="color: #64748b; font-size: 13.5px; max-width: 420px; margin: 0 auto 20px;">
                Jika buku yang kamu cari sedang habis dipinjam oleh murid lain, kamu dapat memesannya agar masuk daftar antrean prioritas.
            </p>
            <a href="{{ route('anggota.katalog') }}" style="display: inline-flex; align-items: center; gap: 8px; background: #4f46e5; color: #ffffff; padding: 10px 22px; border-radius: 12px; font-size: 13.5px; font-weight: 700; text-decoration: none;">
                Jelajahi Katalog Buku
            </a>
        </div>
    @endif

</div>
@endsection

