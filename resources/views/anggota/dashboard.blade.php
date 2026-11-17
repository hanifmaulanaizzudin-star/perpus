@extends('layouts.anggota')

@section('title', 'Dashboard')

@section('content')
<div style="display: flex; flex-direction: column; gap: 20px;">

    {{-- Clean & Welcoming Header Section --}}
    <div class="card-box" style="margin-bottom: 0; padding: 24px 28px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600; color: #2563eb; margin-bottom: 6px;">
                <span style="width: 7px; height: 7px; background: #10b981; border-radius: 50%; display: inline-block;"></span>
                <span>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
            </div>
            <h1 style="font-size: 22px; font-weight: 800; color: #0f172a; margin-bottom: 4px; letter-spacing: -0.02em;">
                Halo, {{ auth('anggota')->user()->nama_lengkap }}! 👋
            </h1>
            <p style="font-size: 13.5px; color: #64748b; margin: 0; max-width: 580px; line-height: 1.5;">
                @if ($pinjamanAktif > 0)
                    Kamu sedang meminjam <strong style="color: #0f172a;">{{ $pinjamanAktif }} transaksi buku</strong>. Pastikan untuk mengembalikan tepat waktu ya!
                @else
                    Kamu tidak memiliki tanggungan pinjaman buku saat ini. Mau cari buku referensi atau cerita menarik hari ini?
                @endif
            </p>
        </div>

        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <a href="{{ route('anggota.kartu-saya') }}" class="btn btn-secondary">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="5" width="20" height="14" rx="2"/>
                    <line x1="2" y1="10" x2="22" y2="10"/>
                </svg>
                <span>Kartu Anggota</span>
            </a>
            <a href="{{ route('anggota.katalog') }}" class="btn btn-primary">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <span>Cari Buku</span>
            </a>
        </div>
    </div>

    {{-- Clean Modern Metric Cards --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
        
        {{-- Card 1: Buku Dibawa --}}
        <div class="card-box" style="margin-bottom: 0; padding: 18px 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                <span style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;">Buku Dibawa</span>
                <div style="width: 32px; height: 32px; border-radius: 8px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                    </svg>
                </div>
            </div>
            <div style="font-size: 26px; font-weight: 800; color: #0f172a; line-height: 1.1; margin-bottom: 4px;">
                {{ $totalBukuDibawa }}
            </div>
            <span style="font-size: 12px; color: #64748b;">Buku fisik di tanganmu</span>
        </div>

        {{-- Card 2: Pinjaman Aktif --}}
        <div class="card-box" style="margin-bottom: 0; padding: 18px 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                <span style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;">Pinjaman Aktif</span>
                <div style="width: 32px; height: 32px; border-radius: 8px; background: #fffbeb; color: #d97706; display: flex; align-items: center; justify-content: center;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>
            </div>
            <div style="font-size: 26px; font-weight: 800; color: #0f172a; line-height: 1.1; margin-bottom: 4px;">
                {{ $pinjamanAktif }}
            </div>
            <span style="font-size: 12px; color: #64748b;">Belum dikembalikan</span>
        </div>

        {{-- Card 3: Sudah Selesai --}}
        <div class="card-box" style="margin-bottom: 0; padding: 18px 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                <span style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;">Sudah Selesai</span>
                <div style="width: 32px; height: 32px; border-radius: 8px; background: #ecfdf5; color: #059669; display: flex; align-items: center; justify-content: center;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                </div>
            </div>
            <div style="font-size: 26px; font-weight: 800; color: #0f172a; line-height: 1.1; margin-bottom: 4px;">
                {{ $pinjamanSelesai }}
            </div>
            <span style="font-size: 12px; color: #64748b;">Transaksi tuntas</span>
        </div>

        {{-- Card 4: Antrean Booking --}}
        <a href="{{ route('anggota.reservasi-saya') }}" class="card-box" style="margin-bottom: 0; padding: 18px 20px; text-decoration: none; color: inherit; display: block; transition: all 0.15s ease;" onmouseover="this.style.borderColor='#93c5fd'" onmouseout="this.style.borderColor='#e2e8f0'">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                <span style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;">Antrean Booking</span>
                <div style="width: 32px; height: 32px; border-radius: 8px; background: #f5f3ff; color: #7c3aed; display: flex; align-items: center; justify-content: center;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <div style="font-size: 26px; font-weight: 800; color: #7c3aed; line-height: 1.1; margin-bottom: 4px;">
                {{ $reservasiAktif }}
            </div>
            <span style="font-size: 12px; color: #2563eb; font-weight: 600;">Lihat antrean buku &rarr;</span>
        </a>

    </div>

    {{-- Active Loans Section --}}
    <div class="card-box" style="margin-bottom: 0;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; padding-bottom: 14px; border-bottom: 1px solid #f1f5f9;">
            <div>
                <h2 style="font-size: 15px; font-weight: 700; color: #0f172a; margin: 0 0 2px;">
                    Buku yang Sedang Kamu Pinjam
                </h2>
                <p style="font-size: 12.5px; color: #64748b; margin: 0;">Pantau tenggat waktu agar terhindar dari denda keterlambatan.</p>
            </div>
            <a href="{{ route('anggota.pinjaman-saya') }}" style="font-size: 12.5px; font-weight: 600; color: #2563eb; text-decoration: none;">
                Semua Riwayat &rarr;
            </a>
        </div>

        @if ($pinjamanBerjalan->count() > 0)
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 50px;">Buku</th>
                            <th>Judul & Informasi</th>
                            <th style="width: 130px;">Tgl Pinjam</th>
                            <th style="width: 140px;">Jatuh Tempo</th>
                            <th style="width: 140px;">Sisa Waktu</th>
                            <th style="width: 100px;">Status</th>
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
                                            <img src="{{ asset('storage/' . $detail->buku->gambar_sampul) }}" alt="{{ $detail->buku->judul }}" style="width: 38px; height: 52px; object-fit: cover; border-radius: 6px; border: 1px solid #e2e8f0;">
                                        @else
                                            <div style="width: 38px; height: 52px; background: #f8fafc; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #94a3b8; border: 1px solid #e2e8f0;">
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div style="font-weight: 700; color: #0f172a; font-size: 13.5px; margin-bottom: 2px;">
                                            {{ $detail->buku->judul ?? 'Buku Tidak Tersedia' }}
                                        </div>
                                        <div style="font-size: 12px; color: #64748b;">
                                            Penulis: {{ $detail->buku->penulis ?? '-' }} &bull; Rak: <span style="color: #2563eb; font-weight: 600;">{{ $detail->buku->lokasi_rak ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td style="color: #475569; font-size: 13px;">
                                        {{ \Carbon\Carbon::parse($pinjam->tanggal_pinjam)->translatedFormat('d M Y') }}
                                    </td>
                                    <td>
                                        <span style="font-weight: 700; color: {{ $isOverdue ? '#dc2626' : '#0f172a' }}; font-size: 13px;">
                                            {{ $jatuhTempo->translatedFormat('d M Y') }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($isOverdue)
                                            <span class="badge badge-danger">Lewat {{ $selisihHari }} hari</span>
                                        @elseif ($selisihHari == 0)
                                            <span class="badge badge-warning">Hari ini terakhir!</span>
                                        @else
                                            <span class="badge" style="background: #f0f9ff; color: #0369a1; border: 1px solid #bae6fd;">Sisa {{ $selisihHari }} hari</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #dbeafe;">Dipinjam</span>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="text-align: center; padding: 36px 20px;">
                <div style="width: 44px; height: 44px; border-radius: 50%; background: #f1f5f9; color: #94a3b8; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 10px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                    </svg>
                </div>
                <div style="font-size: 14px; font-weight: 700; color: #0f172a; margin-bottom: 2px;">Tidak Ada Buku yang Sedang Dipinjam</div>
                <p style="font-size: 13px; color: #64748b; margin: 0 0 14px;">Kamu sedang bebas dari tanggungan pinjaman. Jelajahi katalog koleksi kami!</p>
                <a href="{{ route('anggota.katalog') }}" class="btn btn-secondary" style="font-size: 12.5px;">
                    Jelajahi Katalog Buku
                </a>
            </div>
        @endif
    </div>

    {{-- Clean New Arrivals Section --}}
    <div class="card-box" style="margin-bottom: 0;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; padding-bottom: 14px; border-bottom: 1px solid #f1f5f9;">
            <div>
                <h2 style="font-size: 15px; font-weight: 700; color: #0f172a; margin: 0 0 2px;">
                    Koleksi Buku Pilihan Terbaru
                </h2>
                <p style="font-size: 12.5px; color: #64748b; margin: 0;">Buku bacaan terbaru yang siap dipinjam di perpustakaan.</p>
            </div>
            <a href="{{ route('anggota.katalog') }}" style="font-size: 12.5px; font-weight: 600; color: #2563eb; text-decoration: none;">
                Buka Katalog Lengkap &rarr;
            </a>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 16px;">
            @foreach ($bukuTerbaru as $b)
                <div style="border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px; display: flex; flex-direction: column; background: #ffffff; transition: all 0.15s ease;" onmouseover="this.style.borderColor='#cbd5e1'" onmouseout="this.style.borderColor='#e2e8f0'">
                    
                    {{-- Cover Box --}}
                    <div style="height: 180px; background: #f8fafc; border-radius: 8px; margin-bottom: 12px; overflow: hidden; display: flex; align-items: center; justify-content: center; position: relative; border: 1px solid #f1f5f9;">
                        @if ($b->gambar_sampul)
                            <img src="{{ asset('storage/' . $b->gambar_sampul) }}" alt="{{ $b->judul }}" style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            <div style="color: #94a3b8; display: flex; flex-direction: column; align-items: center; gap: 4px;">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                                </svg>
                                <span style="font-size: 11px;">Sampul Belum Ada</span>
                            </div>
                        @endif

                        {{-- Category Badge --}}
                        <div style="position: absolute; top: 8px; left: 8px;">
                            <span style="font-size: 10.5px; font-weight: 600; color: #1e293b; background: rgba(255, 255, 255, 0.95); padding: 2px 8px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
                                {{ $b->kategori->nama_kategori ?? 'Umum' }}
                            </span>
                        </div>
                    </div>

                    <h3 style="font-size: 14px; font-weight: 700; color: #0f172a; margin: 0 0 3px; line-height: 1.35; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;" title="{{ $b->judul }}">
                        {{ $b->judul }}
                    </h3>

                    <p style="font-size: 12px; color: #64748b; margin: 0 0 12px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        {{ $b->penulis ?? 'Penulis tidak dicantumkan' }}
                    </p>

                    <div style="margin-top: auto; padding-top: 10px; border-top: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                        @if ($b->stok_tersedia > 0)
                            <span style="font-size: 11.5px; font-weight: 600; color: #059669;">
                                Stok: {{ $b->stok_tersedia }}
                            </span>
                        @else
                            <span style="font-size: 11.5px; font-weight: 600; color: #dc2626;">
                                Habis Dipinjam
                            </span>
                        @endif

                        <a href="{{ route('anggota.katalog.detail', $b->id) }}" class="btn btn-secondary" style="font-size: 11.5px; padding: 4px 10px; border-radius: 6px;">
                            Detail
                        </a>
                    </div>

                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
