@extends('layouts.app')

@section('title', 'Data Pengembalian Buku')

@section('content')
<div class="card-box">

    {{-- Alerts --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Toolbar --}}
    <div class="toolbar">
        <a href="{{ route('petugas.pengembalian.create') }}" class="btn btn-primary">
            + Catat Pengembalian Buku
        </a>

        <form action="{{ route('petugas.pengembalian.index') }}" method="GET" class="search-box">
            <input
                type="text"
                name="search"
                placeholder="Cari kode atau nama peminjam..."
                value="{{ request('search') }}"
            >
            <button type="submit" class="btn btn-secondary">Cari</button>
            @if(request('search'))
                <a href="{{ route('petugas.pengembalian.index') }}" class="btn btn-secondary">Reset</a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th style="width: 160px;">Kode Pengembalian</th>
                    <th style="width: 150px;">Kode Pinjam</th>
                    <th>Peminjam (Anggota)</th>
                    <th style="width: 140px;">Kondisi Buku</th>
                    <th>Waktu Dikembalikan</th>
                    <th style="width: 140px;">Petugas</th>
                    <th style="width: 80px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pengembalian as $item)
                    <tr>
                        <td style="color: #64748b;">
                            {{ $loop->iteration + ($pengembalian->currentPage() - 1) * $pengembalian->perPage() }}
                        </td>
                        <td>
                            <code>{{ $item->kode_pengembalian }}</code>
                        </td>
                        <td>
                            <a href="{{ route('petugas.peminjaman.show', $item->peminjaman_id) }}" style="color: #2563eb; text-decoration: none; font-weight: 500;">
                                {{ $item->peminjaman->kode_peminjaman ?? '-' }}
                            </a>
                        </td>
                        <td>
                            <strong style="color: #0f172a;">{{ $item->peminjaman->anggota->nama_lengkap ?? 'Anggota Dihapus' }}</strong>
                            <div style="font-size: 12px; color: #64748b;">
                                No. {{ $item->peminjaman->anggota->nomor_induk ?? '-' }}
                            </div>
                        </td>
                        <td>
                            @php
                                $hasLost = $item->detailPengembalian->contains('kondisi_buku', 'hilang');
                                $hasDamaged = $item->detailPengembalian->contains('kondisi_buku', 'rusak_berat');
                            @endphp
                            @if ($hasLost)
                                <span class="badge" style="background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; font-size: 11px;">Buku Hilang</span>
                            @elseif ($hasDamaged)
                                <span class="badge badge-warning" style="font-size: 11px;">Buku Rusak</span>
                            @else
                                <span class="badge badge-aktif" style="font-size: 11px;">Baik Lengkap</span>
                            @endif
                        </td>
                        <td style="color: #334155; font-size: 13px;">
                            {{ $item->tanggal_pengembalian ? $item->tanggal_pengembalian->format('d/m/Y, H:i') : '-' }} WIB
                        </td>
                        <td style="font-size: 13px; color: #475569;">
                            {{ $item->petugas->nama_lengkap ?? '-' }}
                        </td>
                        <td style="text-align: center;">
                            <a href="{{ route('petugas.pengembalian.show', $item->id) }}" class="act-detail" style="font-size: 12px; padding: 4px 10px;">
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; color: #64748b; padding: 35px 0;">
                            Belum ada riwayat transaksi pengembalian buku.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="pagination">
        {{ $pengembalian->links() }}
    </div>

</div>
@endsection
