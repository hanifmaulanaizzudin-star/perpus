@extends('layouts.anggota')

@section('title', $buku->judul)

@section('content')
<div class="card-box" style="max-width: 950px; margin: 0 auto;">

    {{-- Alerts --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Breadcrumb Navigation --}}
    <div style="margin-bottom: 24px; font-size: 13px; color: #64748b; display: flex; align-items: center; gap: 8px;">
        <a href="{{ route('anggota.katalog') }}" style="color: #4f46e5; text-decoration: none; font-weight: 600;">Katalog</a>
        <span>/</span>
        <span style="color: #64748b;">{{ $buku->kategori->nama_kategori ?? 'Umum' }}</span>
        <span>/</span>
        <span style="color: #0f172a; font-weight: 600;">{{ $buku->judul }}</span>
    </div>

    <div style="display: flex; gap: 40px; flex-wrap: wrap;">
        
        {{-- Cover Image with 3D Book Elevation --}}
        <div style="width: 240px; flex-shrink: 0; margin: 0 auto;">
            <div style="width: 240px; height: 330px; background: #f8fafc; border-radius: 14px; overflow: hidden; display: flex; align-items: center; justify-content: center; box-shadow: -4px 10px 30px rgba(15, 23, 42, 0.16); border: 1px solid rgba(0,0,0,0.06);">
                @if ($buku->gambar_sampul)
                    <img src="{{ asset('storage/' . $buku->gambar_sampul) }}" alt="{{ $buku->judul }}" style="width: 100%; height: 100%; object-fit: cover;">
                @else
                    <div style="color: #94a3b8; display: flex; flex-direction: column; align-items: center; gap: 8px;">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                        </svg>
                        <span style="font-size: 12px;">Sampul Belum Ada</span>
                    </div>
                @endif
            </div>

            <div style="margin-top: 20px; text-align: center;">
                @if ($buku->stok_tersedia > 0)
                    <div class="badge badge-aktif" style="font-size: 13.5px; padding: 8px 18px; width: 100%; justify-content: center; box-shadow: 0 2px 6px rgba(5, 150, 105, 0.15);">
                        Tersedia {{ $buku->stok_tersedia }} di Rak Perpustakaan
                    </div>
                @else
                    <div class="badge badge-danger" style="font-size: 13px; padding: 6px 14px; width: 100%; justify-content: center; margin-bottom: 12px;">
                        Stok Fisik Sedang Dipinjam Semua
                    </div>

                    @if ($reservasiAktif)
                        @if ($reservasiAktif->status === 'tersedia')
                            <div style="background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 10px; padding: 12px; text-align: left; margin-bottom: 12px;">
                                <div style="font-weight: 800; color: #065f46; font-size: 13px; display: flex; align-items: center; gap: 6px;">
                                    <span>🎉</span> BUKU SIAP DIAMBIL!
                                </div>
                                <p style="font-size: 12px; color: #047857; margin: 4px 0 6px;">
                                    Buku ini sudah disisihkan untuk Anda di meja sirkulasi perpustakaan.
                                </p>
                                <div style="font-size: 11px; color: #065f46; font-weight: 700;">
                                    Tenggat Ambil: {{ $reservasiAktif->batas_ambil ? $reservasiAktif->batas_ambil->translatedFormat('d M Y, H:i') : '-' }} WIB
                                </div>
                            </div>
                        @else
                            <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; padding: 12px; text-align: left; margin-bottom: 12px;">
                                <div style="font-weight: 800; color: #92400e; font-size: 13px; display: flex; align-items: center; gap: 6px;">
                                    <span>🔖</span> STATUS: DALAM ANTREAN
                                </div>
                                <div style="font-size: 12.5px; color: #b45309; margin: 4px 0;">
                                    Posisi Antrean Anda: <strong>#{{ $reservasiAktif->posisi_antrean }}</strong>
                                </div>
                                <div style="font-size: 11px; color: #78350f;">
                                    Kode: <code>{{ $reservasiAktif->kode_reservasi }}</code>
                                </div>
                                
                                <form action="{{ route('anggota.reservasi.batal', $reservasiAktif->id) }}" method="POST" style="margin-top: 10px;" onsubmit="return confirm('Yakin ingin membatalkan antrean reservasi ini?');">
                                    @csrf
                                    <button type="submit" class="btn" style="background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; font-size: 11.5px; width: 100%; padding: 5px; font-weight: 600;">
                                        Batalkan Antrean
                                    </button>
                                </form>
                            </div>
                        @endif
                    @else
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px 12px; text-align: center;">
                            <div style="font-size: 12px; color: #64748b; margin-bottom: 10px;">
                                Terdapat <strong>{{ $totalAntreanBuku }} siswa</strong> sedang mengantre buku ini.
                            </div>
                            <form action="{{ route('anggota.reservasi.store', $buku->id) }}" method="POST" onsubmit="return confirm('Ajukan reservasi online untuk buku {{ $buku->judul }}? Anda akan diberitahu saat buku ini dikembalikan.');">
                                @csrf
                                <button type="submit" class="btn btn-primary" style="width: 100%; font-size: 13px; padding: 9px; display: inline-flex; align-items: center; justify-content: center; gap: 6px; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);">
                                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                                    Booking / Antre Buku Ini
                                </button>
                            </form>
                        </div>
                    @endif
                @endif
            </div>
        </div>

        {{-- Book Information --}}
        <div style="flex: 1; min-width: 300px;">
            <div style="margin-bottom: 8px;">
                <span style="font-size: 12px; font-weight: 700; color: #4338ca; background: #eef2ff; padding: 4px 12px; border-radius: 9999px;">
                    {{ $buku->kategori->nama_kategori ?? 'Kategori Umum' }}
                </span>
            </div>

            <h1 style="font-size: 26px; font-weight: 800; color: #0f172a; margin-bottom: 8px; line-height: 1.3; letter-spacing: -0.02em;">
                {{ $buku->judul }}
            </h1>

            <p style="color: #64748b; font-size: 14px; margin-bottom: 24px;">
                Ditulis oleh <strong style="color: #1e293b;">{{ $buku->penulis ?? '-' }}</strong> &bull; Diterbitkan oleh <strong style="color: #1e293b;">{{ $buku->penerbit ?? '-' }}</strong> ({{ $buku->tahun_terbit ?? '-' }})
            </p>

            {{-- Metadata Table --}}
            <table style="width: 100%; margin-bottom: 24px;">
                <tbody>
                    <tr>
                        <td style="width: 160px; color: #64748b; padding: 11px 0; border-bottom: 1px solid #f1f5f9;">Kode Buku</td>
                        <td style="padding: 11px 0; border-bottom: 1px solid #f1f5f9;">
                            <code style="background: #f1f5f9; padding: 3px 8px; border-radius: 6px; font-size: 13px; font-weight: 700; color: #4f46e5;">{{ $buku->kode_buku }}</code>
                        </td>
                    </tr>
                    <tr>
                        <td style="color: #64748b; padding: 11px 0; border-bottom: 1px solid #f1f5f9;">Nomor ISBN</td>
                        <td style="padding: 11px 0; border-bottom: 1px solid #f1f5f9; color: #1e293b; font-weight: 500;">{{ $buku->isbn ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="color: #64748b; padding: 11px 0; border-bottom: 1px solid #f1f5f9;">Lokasi Rak</td>
                        <td style="padding: 11px 0; border-bottom: 1px solid #f1f5f9;">
                            <span style="background: #f0fdf4; color: #166534; font-weight: 700; padding: 4px 10px; border-radius: 6px; border: 1px solid #bbf7d0;">
                                {{ $buku->nama_lokasi_rak }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td style="color: #64748b; padding: 11px 0; border-bottom: 1px solid #f1f5f9;">Total Koleksi</td>
                        <td style="padding: 11px 0; border-bottom: 1px solid #f1f5f9; color: #1e293b; font-weight: 600;">{{ $buku->jumlah_stok }} eksemplar</td>
                    </tr>
                </tbody>
            </table>

            {{-- Synopsis --}}
            <div style="margin-bottom: 24px;">
                <h2 style="font-size: 15px; font-weight: 700; color: #0f172a; margin-bottom: 10px;">Ringkasan & Sinopsis</h2>
                <div style="font-size: 14px; line-height: 1.8; color: #475569; background: #fafbfc; padding: 16px 20px; border-radius: 12px; border: 1px solid #edf2f7;">
                    {{ $buku->deskripsi ?? 'Belum ada ringkasan atau sinopsis untuk buku ini.' }}
                </div>
            </div>

            {{-- Borrowing Instruction Box --}}
            <div style="background: #eef2ff; border: 1px solid #e0e7ff; border-radius: 12px; padding: 16px 18px; font-size: 13.5px; color: #3730a3; display: flex; gap: 12px; align-items: flex-start;">
                <div style="width: 28px; height: 28px; border-radius: 50%; background: #4f46e5; color: white; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-weight: bold; font-size: 13px;">
                    i
                </div>
                <div style="line-height: 1.5;">
                    <strong style="color: #1e1b4b; display: block; margin-bottom: 2px;">Cara Meminjam Buku Ini:</strong>
                    Silakan temukan buku ini pada rak <strong>{{ $buku->lokasi_rak ?? 'terkait' }}</strong>, lalu tunjukkan stiker barcode pada buku ke meja sirkulasi petugas perpustakaan untuk di-scan.
                </div>
            </div>

        </div>

    </div>

    {{-- Back Action --}}
    <div style="border-top: 1px solid #edf2f7; padding-top: 22px; margin-top: 32px;">
        <a href="{{ route('anggota.katalog') }}" class="btn btn-secondary">
            &larr; Kembali ke Katalog Buku
        </a>
    </div>

</div>
@endsection
