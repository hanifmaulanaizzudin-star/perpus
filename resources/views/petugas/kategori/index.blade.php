@extends('layouts.app')

@section('title', 'Data Kategori Buku')

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
    <div class="toolbar">
        <a href="{{ route('petugas.kategori.create') }}" class="btn btn-primary">
            + Tambah Kategori
        </a>

        <form action="{{ route('petugas.kategori.index') }}" method="GET" class="search-box">
            <input
                type="text"
                name="search"
                placeholder="Cari nama kategori..."
                value="{{ request('search') }}"
            >
            <button type="submit" class="btn btn-secondary">Cari</button>
            @if(request('search'))
                <a href="{{ route('petugas.kategori.index') }}" class="btn btn-secondary">Reset</a>
            @endif
        </form>
    </div>

    {{-- Tabel Data --}}
    <table>
        <thead>
            <tr>
                <th style="width: 60px;">No</th>
                <th>Nama Kategori</th>
                <th>Deskripsi</th>
                <th style="width: 120px;">Status</th>
                <th style="width: 180px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($kategori as $item)
                <tr>
                    <td>{{ $loop->iteration + ($kategori->currentPage() - 1) * $kategori->perPage() }}</td>
                    <td><strong>{{ $item->nama_kategori }}</strong></td>
                    <td>{{ $item->deskripsi ?? '-' }}</td>
                    <td>
                        <span class="badge {{ $item->status == 'aktif' ? 'badge-aktif' : 'badge-nonaktif' }}">
                            {{ ucfirst($item->status) }}
                        </span>
                    </td>
                    <td>
                        <div class="actions">
                            <a href="{{ route('petugas.kategori.show', $item->id) }}" class="act-detail">Detail</a>
                            <a href="{{ route('petugas.kategori.edit', $item->id) }}" class="act-edit">Edit</a>

                            <form action="{{ route('petugas.kategori.destroy', $item->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Yakin ingin menghapus kategori ini?')"
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
                    <td colspan="5" style="text-align: center; color: #64748b; padding: 30px;">
                        Belum ada data kategori buku.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination --}}
    <div class="pagination">
        {{ $kategori->links() }}
    </div>

</div>
@endsection