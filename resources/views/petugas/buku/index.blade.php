@extends('layouts.app')

@section('title', 'Koleksi Buku Perpustakaan')

@section('content')
<div class="card-box" style="padding: 0; overflow: hidden;">

    {{-- Header Card & Toolbar --}}
    <div style="padding: 22px 28px; border-bottom: 1px solid #edf2f7;">
        
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="toolbar" style="margin-bottom: 0;">
            <div>
                <a href="{{ route('petugas.buku.create') }}" class="btn btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Tambah Koleksi Buku
                </a>
            </div>

            <form action="{{ route('petugas.buku.index') }}" method="GET" class="search-box">
                <input
                    type="text"
                    name="search"
                    placeholder="Cari judul, kode, atau penulis..."
                    value="{{ request('search') }}"
                >
                <button type="submit" class="btn btn-secondary">Cari</button>
                @if(request('search'))
                    <a href="{{ route('petugas.buku.index') }}" class="btn btn-secondary">Reset</a>
                @endif
            </form>
        </div>
    </div>

    {{-- Tabel Buku --}}
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th style="width: 70px;">Sampul</th>
                    <th style="width: 130px;">Kode Buku</th>
                    <th>Judul & Penulis</th>
                    <th>Kategori</th>
                    <th style="width: 140px;">Stok (Tersedia)</th>
                    <th style="width: 110px;">Status</th>
                    <th style="width: 220px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($buku as $item)
                    <tr>
                        <td style="color: #94a3b8; font-weight: 600;">{{ $loop->iteration + ($buku->currentPage() - 1) * $buku->perPage() }}</td>
                        
                        <td>
                            @if ($item->gambar_sampul)
                                <img src="{{ asset('storage/' . $item->gambar_sampul) }}" alt="Sampul" style="width: 40px; height: 56px; object-fit: cover; border-radius: 6px; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">
                            @else
                                <div style="width: 40px; height: 56px; background: #eef2ff; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #6366f1; border: 1px solid #e0e7ff; font-size: 11px;">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                                    </svg>
                                </div>
                            @endif
                        </td>

                        <td>
                            <code style="font-size: 12.5px; background: #f1f5f9; padding: 3px 8px; border-radius: 6px; font-weight: 700; color: #4f46e5;">
                                {{ $item->kode_buku }}
                            </code>
                        </td>

                        <td>
                            <div style="font-weight: 700; color: #0f172a; margin-bottom: 2px; font-size: 14px;">{{ $item->judul }}</div>
                            <div style="font-size: 12px; color: #64748b;">
                                {{ $item->penulis ?? 'Tanpa Penulis' }} &bull; {{ $item->tahun_terbit ?? '-' }}
                            </div>
                        </td>

                        <td>
                            <span style="font-size: 12px; font-weight: 600; color: #4338ca; background: #eef2ff; padding: 3px 9px; border-radius: 9999px;">
                                {{ $item->kategori->nama_kategori ?? 'Umum' }}
                            </span>
                        </td>

                        <td>
                            <div style="font-weight: 700; color: {{ $item->stok_tersedia > 0 ? '#059669' : '#dc2626' }}; font-size: 13.5px;">
                                {{ $item->stok_tersedia }} <span style="font-weight: normal; color: #64748b; font-size: 12px;">/ {{ $item->jumlah_stok }} buku</span>
                            </div>
                        </td>

                        <td>
                            <span class="badge {{ $item->status == 'tersedia' ? 'badge-aktif' : 'badge-nonaktif' }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>

                        <td style="text-align: center;">
                            <div class="actions" style="justify-content: center;">
                                <a href="{{ route('petugas.buku.cetak-barcode', $item->id) }}" target="_blank" class="act-barcode" title="Cetak Stiker Barcode">
                                    Barcode
                                </a>
                                <a href="{{ route('petugas.buku.show', $item->id) }}" class="act-detail">
                                    Detail
                                </a>
                                <a href="{{ route('petugas.buku.edit', $item->id) }}" class="act-edit">
                                    Edit
                                </a>

                                <form action="{{ route('petugas.buku.destroy', $item->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Yakin ingin menghapus koleksi buku ini?')"
                                      style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="act-delete">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; color: #94a3b8; padding: 40px 0;">
                            Belum ada koleksi buku yang terdaftar di sistem perpustakaan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="padding: 18px 28px; border-top: 1px solid #edf2f7;">
        {{ $buku->links() }}
    </div>

</div>
@endsection
