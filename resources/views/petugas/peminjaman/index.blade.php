@extends('layouts.app')

@section('title', 'Data Peminjaman Buku')

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
        <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
            <a href="{{ route('petugas.peminjaman.create') }}" class="btn btn-primary">
                + Transaksi Peminjaman (Scan)
            </a>

            <form action="{{ route('petugas.peminjaman.blast-wa') }}" method="POST" onsubmit="return confirm('Kirim pesan WhatsApp pengingat otomatis ke SEMUA murid yang terlambat sekarang?')">
                @csrf
                <button type="submit" class="btn" style="background: #25D366; color: #ffffff; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 4px 12px rgba(37, 211, 102, 0.25);" title="Kirim notifikasi tagihan ke semua nomor WA murid yang terlambat">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
                    </svg>
                    ⚡ Kirim WA Otomatis (Semua Terlambat)
                </button>
            </form>
        </div>

        <form action="{{ route('petugas.peminjaman.index') }}" method="GET" class="search-box">
            <select name="status" class="form-control" style="max-width: 150px; font-size: 13px;">
                <option value="">Semua Status</option>
                <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                <option value="dikembalikan" {{ request('status') == 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
            </select>

            <input
                type="text"
                name="search"
                placeholder="Cari kode atau nama peminjam..."
                value="{{ request('search') }}"
            >
            <button type="submit" class="btn btn-secondary">Cari</button>
            @if(request('search') || request('status'))
                <a href="{{ route('petugas.peminjaman.index') }}" class="btn btn-secondary">Reset</a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th style="width: 160px;">Kode Transaksi</th>
                    <th>Peminjam (Anggota)</th>
                    <th style="width: 180px;">Jadwal Pinjam</th>
                    <th style="width: 100px;">Total Buku</th>
                    <th style="width: 110px;">Status</th>
                    <th style="width: 140px;">Petugas</th>
                    <th style="width: 120px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($peminjaman as $item)
                    <tr>
                        <td style="color: #64748b;">
                            {{ $loop->iteration + ($peminjaman->currentPage() - 1) * $peminjaman->perPage() }}
                        </td>
                        <td>
                            <code>{{ $item->kode_peminjaman }}</code>
                        </td>
                        <td>
                            <strong style="color: #0f172a;">{{ $item->anggota->nama_lengkap ?? 'Anggota Dihapus' }}</strong>
                            <div style="font-size: 12px; color: #64748b;">
                                No. {{ $item->anggota->nomor_induk ?? '-' }}
                            </div>
                        </td>
                        <td>
                            <div style="font-size: 12.5px; color: #475569;">
                                Pinjam: {{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') }}
                            </div>
                            <div style="font-size: 12.5px; font-weight: 600; color: {{ $item->status === 'terlambat' ? '#b91c1c' : '#0f172a' }};">
                                Tempo: {{ \Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->format('d/m/Y') }}
                            </div>
                        </td>
                        <td>
                            <span style="font-weight: 600; color: #0f172a;">{{ $item->detailPeminjaman->sum('jumlah_pinjam') }}</span> buku
                        </td>
                        <td>
                            @if ($item->status === 'dipinjam')
                                <span class="badge" style="background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe;">Dipinjam</span>
                            @elseif ($item->status === 'terlambat')
                                <span class="badge badge-danger">Terlambat</span>
                            @elseif ($item->status === 'dikembalikan')
                                <span class="badge badge-aktif">Dikembalikan</span>
                            @else
                                <span class="badge badge-neutral">{{ ucfirst($item->status) }}</span>
                            @endif
                        </td>
                        <td style="font-size: 13px; color: #475569;">
                            {{ $item->petugas->nama_lengkap ?? '-' }}
                        </td>
                        <td style="text-align: center;">
                            <div style="display: flex; gap: 5px; justify-content: center; align-items: center; flex-wrap: wrap;">
                                <a href="{{ route('petugas.peminjaman.show', $item->id) }}" class="act-detail" style="font-size: 11.5px; padding: 4px 8px;">Detail</a>
                                @if (in_array($item->status, ['dipinjam', 'terlambat']))
                                    @if ($item->status === 'dipinjam')
                                        @if (($item->jumlah_perpanjangan ?? 0) < 1)
                                            <form action="{{ route('petugas.peminjaman.perpanjang', $item->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Perpanjang masa peminjaman {{ $item->kode_peminjaman }} selama 7 hari?');">
                                                @csrf
                                                <button type="submit" class="btn" style="background: #eef2ff; color: #4338ca; border: 1px solid #c7d2fe; font-size: 11.5px; padding: 4px 8px;" title="Perpanjang Pinjaman (+7 hari)">
                                                    🔄 Perpanjang
                                                </button>
                                            </form>
                                        @else
                                            <span class="badge" style="background: #f1f5f9; color: #64748b; font-size: 10px; padding: 3px 6px;" title="Sudah pernah diperpanjang 1 kali">1x Perpanjang</span>
                                        @endif
                                    @endif
                                    <a href="{{ route('petugas.pengembalian.create', ['peminjaman_id' => $item->id]) }}" class="btn" style="background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; font-size: 11.5px; padding: 4px 8px;" title="Kembalikan Buku">
                                        Kembali
                                    </a>
                                    @if (!empty($item->anggota->no_telepon))
                                        @php
                                            $rawPhone = preg_replace('/[^0-9]/', '', $item->anggota->no_telepon ?? '');
                                            $waPhone = str_starts_with($rawPhone, '0') ? ('62' . substr($rawPhone, 1)) : $rawPhone;
                                            $bukuTitles = $item->detailPeminjaman->map(fn($d) => $d->buku->judul ?? 'Buku')->implode(', ');
                                            $isLate = ($item->status === 'terlambat');
                                            $waMsg = $isLate
                                                ? "Halo {$item->anggota->nama_lengkap}, kami dari Perpustakaan PerpusKu menginformasikan bahwa pinjaman buku *{$bukuTitles}* (Kode: {$item->kode_peminjaman}) telah *TERLAMBAT* (Jatuh tempo: " . \Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->format('d/m/Y') . "). Mohon segera mengembalikan buku ke perpustakaan ya. Terima kasih."
                                                : "Halo {$item->anggota->nama_lengkap}, pengingat dari Perpustakaan PerpusKu: Pinjaman buku *{$bukuTitles}* (Kode: {$item->kode_peminjaman}) akan jatuh tempo pada " . \Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->format('d/m/Y') . ". Mohon dikembalikan tepat waktu ya. Terima kasih.";
                                        @endphp
                                        @if ($waPhone)
                                            <a href="https://wa.me/{{ $waPhone }}?text={{ urlencode($waMsg) }}" target="_blank" class="btn" style="background: #25D366; color: #fff; font-size: 11.5px; padding: 4px 7px; display: inline-flex; align-items: center; gap: 3px;" title="Kirim Pengingat WhatsApp">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
                                                </svg>
                                                WA
                                            </a>
                                        @endif
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; color: #64748b; padding: 35px 0;">
                            Belum ada riwayat transaksi peminjaman buku.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="pagination">
        {{ $peminjaman->links() }}
    </div>

</div>
@endsection
