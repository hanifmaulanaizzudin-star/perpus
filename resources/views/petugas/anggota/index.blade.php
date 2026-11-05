@extends('layouts.app')

@section('title', 'Data Anggota')

@section('content')
<div class="card-box">

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Toolbar --}}
    <div class="toolbar">
        <a href="{{ route('petugas.anggota.create') }}" class="btn btn-primary">
            + Tambah Anggota
        </a>

        <form action="{{ route('petugas.anggota.index') }}" method="GET" class="search-box">
            <input
                type="text"
                name="search"
                placeholder="Cari nama, nomor induk, username..."
                value="{{ request('search') }}"
            >
            <button type="submit" class="btn btn-secondary">Cari</button>
            @if(request('search'))
                <a href="{{ route('petugas.anggota.index') }}" class="btn btn-secondary">Reset</a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th style="width: 140px;">Nomor Induk</th>
                    <th>Nama Anggota</th>
                    <th>Kelas / Jurusan</th>
                    <th>Username</th>
                    <th style="width: 120px;">Status</th>
                    <th style="width: 160px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($anggota as $index => $item)
                    <tr>
                        <td style="color: #64748b;">{{ $anggota->firstItem() + $index }}</td>
                        <td>
                            <code>{{ $item->nomor_induk }}</code>
                        </td>
                        <td>
                            <strong style="color: #0f172a;">{{ $item->nama_lengkap }}</strong>
                            <div style="font-size: 12px; color: #64748b;">
                                {{ $item->email ?? '-' }}
                            </div>
                        </td>
                        <td style="font-size: 13px; color: #334155;">
                            {{ $item->kelas ?? '-' }} {{ $item->jurusan ? '(' . $item->jurusan . ')' : '' }}
                        </td>
                        <td style="font-size: 13px; color: #475569;">
                            {{ $item->nama_pengguna }}
                        </td>
                        <td>
                            @if ($item->status_keanggotaan === 'aktif')
                                <span class="badge badge-aktif">Aktif</span>
                            @elseif ($item->status_keanggotaan === 'ditangguhkan')
                                <span class="badge badge-warning">Ditangguhkan</span>
                            @else
                                <span class="badge badge-nonaktif">Nonaktif</span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <div class="actions" style="justify-content: center;">
                                <a href="{{ route('petugas.anggota.cetak-kartu', $item->id) }}" target="_blank" class="act-barcode" title="Cetak Kartu Anggota">Kartu</a>
                                <a href="{{ route('petugas.anggota.show', $item->id) }}" class="act-detail">Detail</a>
                                <a href="{{ route('petugas.anggota.edit', $item->id) }}" class="act-edit">Edit</a>
                                <form action="{{ route('petugas.anggota.destroy', $item->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Yakin ingin menghapus data anggota ini?')"
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
                            Belum ada data anggota perpustakaan yang terdaftar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="pagination">
        {{ $anggota->links() }}
    </div>

</div>
@endsection
