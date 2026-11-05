@extends('layouts.anggota')

@section('title', 'Katalog Buku Perpustakaan')

@section('content')
<div class="card-box">

    {{-- Page Header --}}
    <div style="margin-bottom: 24px; padding-bottom: 18px; border-bottom: 1px solid #edf2f7; display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 14px;">
        <div>
            <h1 style="font-size: 22px; font-weight: 800; color: #0f172a; margin-bottom: 4px; letter-spacing: -0.02em;">
                Koleksi & Katalog Buku
            </h1>
            <p style="color: #64748b; font-size: 13.5px; margin: 0;">
                Temukan buku bacaan favorit, modul pelajaran, ensiklopedia, dan karya sastra menarik.
            </p>
        </div>
        <div style="background: #eef2ff; color: #4338ca; font-weight: 700; font-size: 13px; padding: 6px 14px; border-radius: 9999px;">
            Total {{ $buku->total() }} Judul Buku
        </div>
    </div>

    {{-- Quick Category Filter Chips --}}
    <div style="display: flex; gap: 8px; overflow-x: auto; padding-bottom: 14px; margin-bottom: 18px;">
        <a href="{{ route('anggota.katalog') }}" style="text-decoration: none; padding: 7px 16px; border-radius: 9999px; font-size: 13px; font-weight: 600; white-space: nowrap; transition: all 0.15s ease; {{ !request('kategori_id') ? 'background: #4f46e5; color: #ffffff; box-shadow: 0 2px 8px rgba(79,70,229,0.3);' : 'background: #f1f5f9; color: #475569;' }}">
            Semua Kategori
        </a>
        @foreach ($kategoriList as $kat)
            <a href="{{ route('anggota.katalog', array_merge(request()->query(), ['kategori_id' => $kat->id])) }}" style="text-decoration: none; padding: 7px 16px; border-radius: 9999px; font-size: 13px; font-weight: 600; white-space: nowrap; transition: all 0.15s ease; {{ request('kategori_id') == $kat->id ? 'background: #4f46e5; color: #ffffff; box-shadow: 0 2px 8px rgba(79,70,229,0.3);' : 'background: #f1f5f9; color: #475569;' }}">
                {{ $kat->nama_kategori }}
            </a>
        @endforeach
    </div>

    {{-- Filter & Search Form --}}
    <form action="{{ route('anggota.katalog') }}" method="GET" style="display: flex; gap: 12px; margin-bottom: 28px; flex-wrap: wrap;">
        @if (request('kategori_id'))
            <input type="hidden" name="kategori_id" value="{{ request('kategori_id') }}">
        @endif

        <div style="flex: 1; min-width: 260px; position: relative;">
            <input 
                type="text" 
                name="search" 
                class="form-control" 
                placeholder="Cari berdasarkan judul buku, nama penulis, atau kode..." 
                value="{{ request('search') }}"
                style="padding: 11px 16px; border: 1px solid #cbd5e1; border-radius: 10px; width: 100%; font-size: 13.5px; outline: none; font-family: inherit; transition: border-color 0.15s ease;"
                onfocus="this.style.borderColor='#4f46e5'"
                onblur="this.style.borderColor='#cbd5e1'"
            >
        </div>

        <button type="submit" class="btn btn-primary" style="padding: 11px 22px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <circle cx="11" cy="11" r="8"/>
                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            Cari Buku
        </button>

        @if (request('search') || request('kategori_id'))
            <a href="{{ route('anggota.katalog') }}" class="btn btn-secondary" style="padding: 11px 16px;">
                Reset Filter
            </a>
        @endif
    </form>

    {{-- Grid Buku --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 22px; margin-bottom: 30px;">
        @forelse ($buku as $item)
            <div style="border: 1px solid #edf2f7; border-radius: 14px; overflow: hidden; background: #ffffff; display: flex; flex-direction: column; transition: all 0.22s ease-in-out; box-shadow: 0 2px 8px rgba(0,0,0,0.03);" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 14px 28px -4px rgba(15,23,42,0.1)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.03)'">
                
                {{-- Cover Image with Availability Badge --}}
                <div style="height: 220px; background: #f8fafc; display: flex; align-items: center; justify-content: center; position: relative;">
                    @if ($item->gambar_sampul)
                        <img src="{{ asset('storage/' . $item->gambar_sampul) }}" alt="{{ $item->judul }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        <div style="color: #94a3b8; display: flex; flex-direction: column; align-items: center; gap: 6px;">
                            <svg width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                            </svg>
                            <span style="font-size: 11.5px;">Sampul Belum Ada</span>
                        </div>
                    @endif

                    {{-- Stock Indicator Pill --}}
                    <div style="position: absolute; top: 12px; right: 12px;">
                        @if ($item->stok_tersedia > 0)
                            <span class="badge badge-aktif" style="box-shadow: 0 2px 6px rgba(0,0,0,0.08);">Tersedia ({{ $item->stok_tersedia }})</span>
                        @else
                            <span class="badge badge-danger" style="box-shadow: 0 2px 6px rgba(0,0,0,0.08);">Stok Kosong</span>
                        @endif
                    </div>
                </div>

                {{-- Book Meta --}}
                <div style="padding: 18px; flex: 1; display: flex; flex-direction: column;">
                    <div style="margin-bottom: 6px;">
                        <span style="font-size: 11px; font-weight: 700; color: #4338ca; background: #eef2ff; padding: 3px 9px; border-radius: 9999px;">
                            {{ $item->kategori->nama_kategori ?? 'Umum' }}
                        </span>
                    </div>

                    <h2 style="font-size: 15px; font-weight: 700; color: #0f172a; margin-bottom: 4px; line-height: 1.35; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;" title="{{ $item->judul }}">
                        {{ $item->judul }}
                    </h2>

                    <p style="font-size: 12.5px; color: #64748b; margin-bottom: 14px;">
                        {{ $item->penulis ?? 'Penulis tidak dicantumkan' }} @if($item->tahun_terbit) &bull; {{ $item->tahun_terbit }} @endif
                    </p>

                    <div style="font-size: 12px; color: #475569; margin-bottom: 16px; padding-top: 10px; border-top: 1px dashed #edf2f7; display: flex; align-items: center; gap: 6px;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2.2" style="flex-shrink: 0;">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                        <span>Rak: <strong style="color: #0f172a;">{{ $item->lokasi_rak ?? 'Tanyakan Petugas' }}</strong></span>
                    </div>

                    <div style="margin-top: auto;">
                        <a href="{{ route('anggota.katalog.detail', $item->id) }}" class="btn btn-secondary" style="width: 100%; text-align: center; font-size: 13px; padding: 8px 12px; border-radius: 9px;">
                            Lihat Detail & Sinopsis
                        </a>
                    </div>
                </div>

            </div>
        @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 50px 20px; background: #fafbfc; border: 1px dashed #e2e8f0; border-radius: 14px;">
                <p style="font-size: 15px; font-weight: 700; color: #334155; margin-bottom: 6px;">Buku Belum Ditemukan</p>
                <p style="font-size: 13.5px; color: #64748b; margin-bottom: 16px;">Tidak ada koleksi buku yang cocok dengan kata kunci pencarian Anda.</p>
                <a href="{{ route('anggota.katalog') }}" class="btn btn-primary" style="font-size: 13px;">
                    Lihat Seluruh Koleksi
                </a>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div style="margin-top: 24px;">
        {{ $buku->links() }}
    </div>

</div>
@endsection
