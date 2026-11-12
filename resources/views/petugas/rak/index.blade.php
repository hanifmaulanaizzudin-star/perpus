@extends('layouts.app')

@section('title', 'Data Rak Buku')

@section('content')
<div class="card-box">

    {{-- Pesan Notifikasi Sukses / Error --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Toolbar: Tombol Tambah & Form Pencarian --}}
    <div class="toolbar" style="flex-wrap: wrap; gap: 10px;">
        <div>
            <a href="{{ route('petugas.rak.create') }}" class="btn btn-primary">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="vertical-align: -2px;">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Tambah Rak Buku
            </a>
        </div>

        <form action="{{ route('petugas.rak.index') }}" method="GET" class="search-box">
            <input
                type="text"
                name="search"
                placeholder="Cari kode rak, nama, atau lokasi gedung..."
                value="{{ request('search') }}"
            >
            <button type="submit" class="btn btn-secondary">Cari</button>
            @if(request('search'))
                <a href="{{ route('petugas.rak.index') }}" class="btn btn-secondary">Reset</a>
            @endif
        </form>
    </div>

    {{-- Tabel Data Rak --}}
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th style="width: 120px;">Kode Rak</th>
                    <th>Nama Rak Buku</th>
                    <th>Lokasi Gedung / Lantai</th>
                    <th style="width: 130px; text-align: center;">Koleksi Buku</th>
                    <th style="width: 100px;">Status</th>
                    <th style="width: 170px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rak as $item)
                    <tr>
                        <td>{{ $loop->iteration + ($rak->currentPage() - 1) * $rak->perPage() }}</td>
                        <td>
                            <code style="font-weight: 700; color: #0f766e; background: #f0fdf4; border: 1px solid #bbf7d0; padding: 3px 8px; border-radius: 6px;">
                                {{ $item->kode_rak }}
                            </code>
                        </td>
                        <td>
                            <strong style="color: #0f172a;">{{ $item->nama_rak }}</strong>
                            @if (!empty($item->keterangan))
                                <div style="font-size: 11.5px; color: #64748b; margin-top: 2px;">{{ Str::limit($item->keterangan, 60) }}</div>
                            @endif
                        </td>
                        <td style="color: #475569;">
                            {{ $item->lokasi_gedung ?? '-' }}
                        </td>
                        <td style="text-align: center;">
                            <span class="badge" style="background: #eef2ff; color: #4338ca; border: 1px solid #c7d2fe; font-size: 11.5px; font-weight: 700;">
                                {{ $item->buku_count }} Judul Buku
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $item->status == 'aktif' ? 'badge-aktif' : 'badge-nonaktif' }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <div class="actions" style="justify-content: center;">
                                <a href="{{ route('petugas.rak.show', $item->id) }}" class="act-detail" title="Lihat Buku di Rak Ini">Detail</a>
                                <a href="{{ route('petugas.rak.edit', $item->id) }}" class="act-edit">Edit</a>

                                <form action="{{ route('petugas.rak.destroy', $item->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus rak ini?')"
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
                        <td colspan="7" style="text-align: center; color: #64748b; padding: 35px 0;">
                            Belum ada data rak buku di perpustakaan. Silakan klik tombol "Tambah Rak Buku".
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="pagination">
        {{ $rak->links() }}
    </div>

</div>
@endsection

