@extends('layouts.app')

@section('title', 'Detail Rak Buku')

@section('content')
<div class="card-box" style="max-width: 900px; margin: 0 auto;">

    <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid #e2e8f0; padding-bottom: 20px; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
                <code style="font-size: 14px; font-weight: 800; color: #0f766e; background: #f0fdf4; border: 1px solid #bbf7d0; padding: 4px 10px; border-radius: 6px;">
                    {{ $rak->kode_rak }}
                </code>
                <span class="badge {{ $rak->status == 'aktif' ? 'badge-aktif' : 'badge-nonaktif' }}">
                    {{ ucfirst($rak->status) }}
                </span>
            </div>
            <h1 style="font-size: 22px; font-weight: 700; color: #0f172a; margin: 0;">{{ $rak->nama_rak }}</h1>
            <div style="font-size: 13px; color: #64748b; margin-top: 4px;">
                📍 Lokasi: <strong>{{ $rak->lokasi_gedung ?? 'Belum ditentukan' }}</strong>
            </div>
        </div>

        <div style="display: flex; gap: 8px;">
            <a href="{{ route('petugas.rak.edit', $rak->id) }}" class="btn btn-primary" style="font-weight: 600;">
                ✏️ Edit Rak
            </a>
            <a href="{{ route('petugas.rak.index') }}" class="btn btn-secondary">
                &larr; Kembali
            </a>
        </div>
    </div>

    @if (!empty($rak->keterangan))
        <div style="background: #f8fafc; border-left: 4px solid #0f766e; padding: 12px 16px; border-radius: 6px; margin-bottom: 24px; font-size: 13px; color: #475569;">
            <strong>Keterangan Rak:</strong> {{ $rak->keterangan }}
        </div>
    @endif

    {{-- Tabel Buku yang berada di Rak ini --}}
    <div>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
            <h2 style="font-size: 16px; font-weight: 700; color: #0f172a; margin: 0;">
                📚 Koleksi Buku yang Tersimpan di Rak Ini ({{ $bukuDiRak->total() }} Judul)
            </h2>
            <a href="{{ route('petugas.buku.create') }}" class="btn btn-secondary" style="font-size: 12px; padding: 5px 12px;">
                + Tambah Buku ke Rak Ini
            </a>
        </div>

        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th style="width: 45px;">No</th>
                        <th style="width: 120px;">Kode Buku</th>
                        <th>Judul Buku</th>
                        <th>Kategori</th>
                        <th style="width: 100px; text-align: center;">Stok Tersedia</th>
                        <th style="width: 100px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($bukuDiRak as $buku)
                        <tr>
                            <td>{{ $loop->iteration + ($bukuDiRak->currentPage() - 1) * $bukuDiRak->perPage() }}</td>
                            <td><code>{{ $buku->kode_buku }}</code></td>
                            <td>
                                <strong style="color: #0f172a;">{{ $buku->judul }}</strong>
                                <div style="font-size: 11.5px; color: #64748b;">{{ $buku->penulis ?? '-' }}</div>
                            </td>
                            <td>
                                <span style="font-size: 11.5px; background: #f1f5f9; padding: 2px 8px; border-radius: 4px; color: #475569;">
                                    {{ $buku->kategori->nama_kategori ?? 'Umum' }}
                                </span>
                            </td>
                            <td style="text-align: center; font-weight: 700; color: #059669;">
                                {{ $buku->stok_tersedia }} / {{ $buku->jumlah_stok }}
                            </td>
                            <td style="text-align: center;">
                                <a href="{{ route('petugas.buku.show', $buku->id) }}" class="act-detail" style="font-size: 11.5px;">Lihat Buku</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: #64748b; padding: 30px 0;">
                                Belum ada buku yang ditempatkan di rak ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $bukuDiRak->links() }}
        </div>
    </div>

</div>
@endsection

