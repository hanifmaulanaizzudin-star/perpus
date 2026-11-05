@extends('layouts.anggota')

@section('title', 'Dashboard Anggota')

@section('content')
<div style="display: flex; flex-direction: column; gap: 26px;">

    {{-- Warm & Engaging Welcome Banner --}}
    <div style="background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 60%, #06b6d4 100%); color: #ffffff; border-radius: 20px; padding: 32px 36px; box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.35); position: relative; overflow: hidden; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
        
        {{-- Background decorative bubble --}}
        <div style="position: absolute; right: -40px; top: -40px; width: 220px; height: 220px; background: rgba(255, 255, 255, 0.1); border-radius: 50%; pointer-events: none;"></div>

        <div style="position: relative; z-index: 2; max-width: 620px;">
            <div style="display: inline-flex; align-items: center; gap: 8px; background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(8px); padding: 4px 12px; border-radius: 9999px; font-size: 11.5px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 12px;">
                <span>Portal Perpustakaan</span>
                <span>&bull;</span>
                <span>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
            </div>
            <h1 style="font-size: 26px; font-weight: 700; margin-bottom: 8px; letter-spacing: -0.02em; line-height: 1.25;">
                Halo, {{ auth('anggota')->user()->nama_lengkap }}!
            </h1>
            <p style="font-size: 14px; opacity: 0.95; line-height: 1.6; margin: 0;">
                Mau baca buku apa hari ini? Jelajahi katalog koleksi kami atau cek masa pengembalian buku yang sedang kamu bawa agar terhindar dari denda.
            </p>
        </div>

        <div style="position: relative; z-index: 2; display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('anggota.kartu-saya') }}" style="display: inline-flex; align-items: center; gap: 8px; background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.4); color: #ffffff; padding: 11px 20px; border-radius: 12px; font-size: 13.5px; font-weight: 700; text-decoration: none; transition: transform 0.15s ease;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="5" width="20" height="14" rx="2"/>
                    <line x1="2" y1="10" x2="22" y2="10"/>
                </svg>
                Kartu Anggota
            </a>
            <a href="{{ route('anggota.katalog') }}" style="display: inline-flex; align-items: center; gap: 8px; background: #ffffff; color: #4338ca; padding: 11px 22px; border-radius: 12px; font-size: 13.5px; font-weight: 700; text-decoration: none; box-shadow: 0 4px 14px rgba(0,0,0,0.12); transition: transform 0.15s ease;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                Cari Buku
            </a>
        </div>
    </div>

    {{-- Lively Metric Cards --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 18px;">
        
        {{-- Stat 1 --}}
        <div class="card-box" style="margin-bottom: 0; padding: 22px 24px; display: flex; align-items: center; justify-content: space-between; transition: transform 0.2s ease, box-shadow 0.2s ease;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 10px 25px -4px rgba(15,23,42,0.08)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 20px -2px rgba(15,23,42,0.04)'">
            <div>
                <span style="font-size: 12.5px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em;">Buku Dibawa</span>
                <div style="font-size: 30px; font-weight: 800; color: #0f172a; margin: 4px 0 2px;">{{ $totalBukuDibawa }}</div>
                <span style="font-size: 12px; color: #4f46e5; font-weight: 500;">Buku fisik kamu bawa</span>
            </div>
            <div style="width: 52px; height: 52px; border-radius: 14px; background: #eef2ff; color: #4f46e5; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(79, 70, 229, 0.15);">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                </svg>
            </div>
        </div>

        {{-- Stat 2 --}}
        <div class="card-box" style="margin-bottom: 0; padding: 22px 24px; display: flex; align-items: center; justify-content: space-between; transition: transform 0.2s ease, box-shadow 0.2s ease;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 10px 25px -4px rgba(15,23,42,0.08)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 20px -2px rgba(15,23,42,0.04)'">
            <div>
                <span style="font-size: 12.5px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em;">Pinjaman Aktif</span>
                <div style="font-size: 30px; font-weight: 800; color: #0f172a; margin: 4px 0 2px;">{{ $pinjamanAktif }}</div>
                <span style="font-size: 12px; color: #d97706; font-weight: 500;">Belum dikembalikan</span>
            </div>
            <div style="width: 52px; height: 52px; border-radius: 14px; background: #fffbeb; color: #d97706; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(217, 119, 6, 0.15);">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
            </div>
        </div>

        {{-- Stat 3 --}}
        <div class="card-box" style="margin-bottom: 0; padding: 22px 24px; display: flex; align-items: center; justify-content: space-between; transition: transform 0.2s ease, box-shadow 0.2s ease;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 10px 25px -4px rgba(15,23,42,0.08)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 20px -2px rgba(15,23,42,0.04)'">
            <div>
                <span style="font-size: 12.5px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em;">Sudah Selesai</span>
                <div style="font-size: 30px; font-weight: 800; color: #0f172a; margin: 4px 0 2px;">{{ $pinjamanSelesai }}</div>
                <span style="font-size: 12px; color: #059669; font-weight: 500;">Transaksi tuntas</span>
            </div>
            <div style="width: 52px; height: 52px; border-radius: 14px; background: #ecfdf5; color: #059669; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(5, 150, 105, 0.15);">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </div>
        </div>

    </div>

    {{-- Active Loans Card --}}
    <div class="card-box">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <div>
                <h2 style="font-size: 16px; font-weight: 700; color: #0f172a; margin-bottom: 3px;">
                    Buku yang Sedang Kamu Pinjam
                </h2>
                <p style="font-size: 13px; color: #64748b; margin: 0;">Pastikan kamu mengembalikan buku sebelum jatuh tempo ya!</p>
            </div>
            <a href="{{ route('anggota.pinjaman-saya') }}" style="font-size: 13px; font-weight: 600; color: #4f46e5; text-decoration: none; display: flex; align-items: center; gap: 4px;">
                Semua Riwayat Pinjaman &rarr;
            </a>
        </div>

        @if ($pinjamanBerjalan->count() > 0)
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 70px;">Buku</th>
                            <th>Judul & Informasi</th>
                            <th style="width: 140px;">Tgl Pinjam</th>
                            <th style="width: 140px;">Jatuh Tempo</th>
                            <th style="width: 150px;">Sisa Waktu</th>
                            <th style="width: 110px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pinjamanBerjalan as $pinjam)
                            @foreach ($pinjam->detailPeminjaman as $detail)
                                @php
                                    $jatuhTempo = \Carbon\Carbon::parse($pinjam->tanggal_jatuh_tempo)->startOfDay();
                                    $hariIni = \Carbon\Carbon::today()->startOfDay();
                                    $isOverdue = $hariIni->greaterThan($jatuhTempo);
                                    $selisihHari = $hariIni->diffInDays($jatuhTempo);
                                @endphp
                                <tr>
                                    <td>
                                        @if ($detail->buku && $detail->buku->gambar_sampul)
                                            <img src="{{ asset('storage/' . $detail->buku->gambar_sampul) }}" alt="{{ $detail->buku->judul }}" style="width: 46px; height: 64px; object-fit: cover; border-radius: 6px; box-shadow: 0 4px 8px rgba(0,0,0,0.12);">
                                        @else
                                            <div style="width: 46px; height: 64px; background: #eef2ff; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #6366f1; border: 1px solid #e0e7ff;">
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div style="font-weight: 700; color: #0f172a; font-size: 14px; margin-bottom: 3px;">
                                            {{ $detail->buku->judul ?? 'Buku Tidak Tersedia' }}
                                        </div>
                                        <div style="font-size: 12px; color: #64748b; display: flex; align-items: center; gap: 8px;">
                                            <span>Penulis: {{ $detail->buku->penulis ?? '-' }}</span>
                                            <span>&bull;</span>
                                            <span>Rak: <strong style="color: #4f46e5;">{{ $detail->buku->lokasi_rak ?? '-' }}</strong></span>
                                        </div>
                                    </td>
                                    <td style="color: #475569;">
                                        {{ \Carbon\Carbon::parse($pinjam->tanggal_pinjam)->translatedFormat('d M Y') }}
                                    </td>
                                    <td>
                                        <span style="font-weight: 700; color: {{ $isOverdue ? '#dc2626' : '#0f172a' }};">
                                            {{ $jatuhTempo->translatedFormat('d M Y') }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($isOverdue)
                                            <span class="badge badge-danger">Lewat {{ $selisihHari }} hari</span>
                                        @elseif ($selisihHari == 0)
                                            <span class="badge badge-warning">Hari ini terakhir!</span>
                                        @else
                                            <span class="badge" style="background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd;">Sisa {{ $selisihHari }} hari</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge" style="background: #eef2ff; color: #4338ca; border: 1px solid #c7d2fe;">Dipinjam</span>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="text-align: center; padding: 42px 20px; background: #fafbfc; border: 1px dashed #e2e8f0; border-radius: 12px;">
                <div style="width: 50px; height: 50px; border-radius: 50%; background: #eef2ff; color: #4f46e5; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 12px;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                    </svg>
                </div>
                <h3 style="font-size: 15px; font-weight: 700; color: #1e293b; margin-bottom: 4px;">Kamu Sedang Tidak Meminjam Buku</h3>
                <p style="font-size: 13px; color: #64748b; margin-bottom: 16px;">Ada banyak buku menarik yang baru masuk di perpustakaan!</p>
                <a href="{{ route('anggota.katalog') }}" class="btn btn-primary" style="font-size: 13px; padding: 8px 18px;">
                    Jelajahi Katalog Buku
                </a>
            </div>
        @endif
    </div>

    {{-- New Arrivals Grid --}}
    <div class="card-box">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 22px;">
            <div>
                <h2 style="font-size: 16px; font-weight: 700; color: #0f172a; margin-bottom: 3px;">
                    Koleksi Buku Pilihan Terbaru
                </h2>
                <p style="font-size: 13px; color: #64748b; margin: 0;">Buku terpopuler dan terbitan baru yang siap kamu pinjam hari ini.</p>
            </div>
            <a href="{{ route('anggota.katalog') }}" style="font-size: 13px; font-weight: 600; color: #4f46e5; text-decoration: none;">
                Buka Katalog Lengkap &rarr;
            </a>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(230px, 1fr)); gap: 20px;">
            @foreach ($bukuTerbaru as $b)
                <div style="border: 1px solid #edf2f7; border-radius: 14px; padding: 16px; display: flex; flex-direction: column; background: #ffffff; transition: all 0.22s ease-in-out; box-shadow: 0 2px 8px rgba(0,0,0,0.03);" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 12px 24px -4px rgba(15,23,42,0.08)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.03)'">
                    
                    {{-- Cover with 3D Book Shadow --}}
                    <div style="height: 200px; background: #f8fafc; border-radius: 10px; margin-bottom: 14px; overflow: hidden; display: flex; align-items: center; justify-content: center; position: relative;">
                        @if ($b->gambar_sampul)
                            <img src="{{ asset('storage/' . $b->gambar_sampul) }}" alt="{{ $b->judul }}" style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            <div style="color: #94a3b8; display: flex; flex-direction: column; align-items: center; gap: 6px;">
                                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                                </svg>
                                <span style="font-size: 11.5px;">Sampul Belum Ada</span>
                            </div>
                        @endif

                        {{-- Category pill overlay --}}
                        <div style="position: absolute; top: 10px; left: 10px;">
                            <span style="font-size: 11px; font-weight: 700; color: #4338ca; background: rgba(255, 255, 255, 0.92); backdrop-filter: blur(4px); padding: 3px 9px; border-radius: 9999px; box-shadow: 0 2px 6px rgba(0,0,0,0.06);">
                                {{ $b->kategori->nama_kategori ?? 'Umum' }}
                            </span>
                        </div>
                    </div>

                    <h3 style="font-size: 14.5px; font-weight: 700; color: #0f172a; margin: 0 0 4px; line-height: 1.35; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;" title="{{ $b->judul }}">
                        {{ $b->judul }}
                    </h3>

                    <p style="font-size: 12.5px; color: #64748b; margin-bottom: 14px;">
                        {{ $b->penulis ?? 'Penulis tidak dicantumkan' }}
                    </p>

                    {{-- Footer --}}
                    <div style="margin-top: auto; padding-top: 12px; border-top: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                        @if ($b->stok_tersedia > 0)
                            <span style="font-size: 12px; font-weight: 700; color: #059669; background: #ecfdf5; padding: 3px 9px; border-radius: 9999px;">
                                Tersedia ({{ $b->stok_tersedia }})
                            </span>
                        @else
                            <span style="font-size: 12px; font-weight: 600; color: #dc2626; background: #fef2f2; padding: 3px 9px; border-radius: 9999px;">
                                Kosong
                            </span>
                        @endif

                        <a href="{{ route('anggota.katalog.detail', $b->id) }}" class="btn btn-secondary" style="font-size: 12px; padding: 6px 12px; border-radius: 8px;">
                            Detail
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
