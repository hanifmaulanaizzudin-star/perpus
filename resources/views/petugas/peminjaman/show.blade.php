@extends('layouts.app')

@section('title', 'Detail Peminjaman')

@section('content')
<div class="card-box" style="max-width: 900px;">

    {{-- Alerts --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Header Struk / Transaksi --}}
    <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid #e2e8f0; padding-bottom: 20px; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <span style="font-size: 11.5px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">KODE TRANSAKSI</span>
            <h1 style="font-size: 22px; font-weight: 700; color: #0f172a; margin-top: 4px; letter-spacing: -0.01em;">{{ $peminjaman->kode_peminjaman }}</h1>
            <div style="font-size: 12.5px; color: #64748b; margin-top: 2px;">
                Dicatat pada: {{ $peminjaman->dibuat_pada ? $peminjaman->dibuat_pada->format('d M Y, H:i') : '-' }} WIB
            </div>
        </div>

        <div style="text-align: right;">
            <span style="font-size: 11.5px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 6px;">STATUS PEMINJAMAN</span>
            @if ($peminjaman->status === 'dipinjam')
                <span class="badge" style="background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; font-size: 13px; padding: 5px 14px;">Sedang Dipinjam</span>
            @elseif ($peminjaman->status === 'terlambat')
                <span class="badge badge-danger" style="font-size: 13px; padding: 5px 14px;">Terlambat</span>
            @elseif ($peminjaman->status === 'dikembalikan')
                <span class="badge badge-aktif" style="font-size: 13px; padding: 5px 14px;">Sudah Dikembalikan</span>
            @else
                <span class="badge badge-neutral" style="font-size: 13px; padding: 5px 14px;">{{ ucfirst($peminjaman->status) }}</span>
            @endif
        </div>
    </div>

    {{-- Two Column Info --}}
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 28px; background: #f8fafc; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0;">
        <div>
            <h2 style="font-size: 12px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 12px;">
                Informasi Peminjam
            </h2>
            <div style="font-size: 13.5px; margin-bottom: 6px; color: #0f172a;">
                Nama: <strong>{{ $peminjaman->anggota->nama_lengkap ?? '-' }}</strong>
            </div>
            <div style="font-size: 13.5px; margin-bottom: 6px; color: #475569;">
                No. Induk: <code style="font-size: 12px; background: #ffffff; padding: 2px 6px; border: 1px solid #e2e8f0; border-radius: 4px; color: #0f172a;">{{ $peminjaman->anggota->nomor_induk ?? '-' }}</code>
            </div>
            <div style="font-size: 13px; margin-bottom: 6px; color: #475569; display: flex; align-items: center; gap: 8px;">
                <span>Telepon: {{ $peminjaman->anggota->no_telepon ?? '-' }}</span>
                @if (!empty($peminjaman->anggota->no_telepon) && in_array($peminjaman->status, ['dipinjam', 'terlambat']))
                    @php
                        $rawPhone = preg_replace('/[^0-9]/', '', $peminjaman->anggota->no_telepon ?? '');
                        $waPhone = str_starts_with($rawPhone, '0') ? ('62' . substr($rawPhone, 1)) : $rawPhone;
                        $bukuTitles = $peminjaman->detailPeminjaman->map(fn($d) => $d->buku->judul ?? 'Buku')->implode(', ');
                        $isLate = ($peminjaman->status === 'terlambat');
                        $waMsg = $isLate
                            ? "Halo {$peminjaman->anggota->nama_lengkap}, kami dari Perpustakaan PerpusKu menginformasikan bahwa pinjaman buku *{$bukuTitles}* (Kode: {$peminjaman->kode_peminjaman}) telah *TERLAMBAT* (Jatuh tempo: " . \Carbon\Carbon::parse($peminjaman->tanggal_jatuh_tempo)->format('d/m/Y') . "). Mohon segera mengembalikan buku ke perpustakaan ya. Terima kasih."
                            : "Halo {$peminjaman->anggota->nama_lengkap}, pengingat dari Perpustakaan PerpusKu: Pinjaman buku *{$bukuTitles}* (Kode: {$peminjaman->kode_peminjaman}) akan jatuh tempo pada " . \Carbon\Carbon::parse($peminjaman->tanggal_jatuh_tempo)->format('d/m/Y') . ". Mohon dikembalikan tepat waktu ya. Terima kasih.";
                    @endphp
                    @if ($waPhone)
                        <a href="https://wa.me/{{ $waPhone }}?text={{ urlencode($waMsg) }}" target="_blank" class="btn" style="background: #25D366; color: #fff; font-size: 11px; padding: 2px 8px; border-radius: 9999px; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
                            </svg>
                            Hubungi via WA
                        </a>
                    @endif
                @endif
            </div>
            <div style="font-size: 13px; color: #64748b;">
                Alamat: {{ $peminjaman->anggota->alamat ?? '-' }}
            </div>
        </div>

        <div>
            <h2 style="font-size: 12px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 12px;">
                Jadwal & Petugas
            </h2>
            <div style="font-size: 13.5px; margin-bottom: 6px; color: #0f172a;">
                Tanggal Pinjam: <strong>{{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->translatedFormat('d F Y') }}</strong>
            </div>
            <div style="font-size: 13.5px; margin-bottom: 6px; color: {{ $peminjaman->status === 'terlambat' ? '#b91c1c' : '#0f172a' }};">
                Jatuh Tempo: <strong>{{ \Carbon\Carbon::parse($peminjaman->tanggal_jatuh_tempo)->translatedFormat('d F Y') }}</strong>
                @if (($peminjaman->jumlah_perpanjangan ?? 0) > 0)
                    <span class="badge" style="background: #eef2ff; color: #4338ca; font-size: 11px; margin-left: 6px;">(Diperpanjang {{ $peminjaman->jumlah_perpanjangan }}x)</span>
                @endif
            </div>
            <div style="font-size: 13.5px; margin-bottom: 6px; color: #475569;">
                Petugas: <strong>{{ $peminjaman->petugas->nama_lengkap ?? '-' }}</strong>
            </div>
            <div style="font-size: 13px; color: #64748b;">
                Catatan: <em>{{ $peminjaman->catatan ?? '-' }}</em>
            </div>
        </div>
    </div>

    {{-- Book Items Table --}}
    <div style="margin-bottom: 28px;">
        <h2 style="font-size: 14.5px; font-weight: 600; margin-bottom: 12px; color: #0f172a;">
            Daftar Buku yang Dipinjam ({{ $peminjaman->detailPeminjaman->count() }} Judul)
        </h2>

        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th style="width: 65px;">Sampul</th>
                        <th style="width: 130px;">Kode Buku</th>
                        <th>Judul Buku</th>
                        <th>Kategori</th>
                        <th style="width: 120px;">Lokasi Rak</th>
                        <th style="width: 90px; text-align: center;">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($peminjaman->detailPeminjaman as $idx => $detail)
                        <tr>
                            <td style="color: #64748b;">{{ $idx + 1 }}</td>
                            <td>
                                @if ($detail->buku && $detail->buku->gambar_sampul)
                                    <img src="{{ asset('storage/' . $detail->buku->gambar_sampul) }}" alt="Sampul" style="width: 38px; height: 52px; object-fit: cover; border-radius: 4px; border: 1px solid #e2e8f0;">
                                @else
                                    <div style="width: 38px; height: 52px; background: #f1f5f9; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #94a3b8; border: 1px solid #e2e8f0;">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                                        </svg>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <code>{{ $detail->buku->kode_buku ?? '-' }}</code>
                            </td>
                            <td>
                                <div style="font-weight: 600; color: #0f172a;">
                                    {{ $detail->buku->judul ?? 'Buku Dihapus' }}
                                </div>
                                <div style="font-size: 12px; color: #64748b;">
                                    {{ $detail->buku->penulis ?? '-' }}
                                </div>
                            </td>
                            <td>
                                <span style="font-size: 11.5px; background: #f1f5f9; padding: 2px 7px; border-radius: 4px; color: #475569;">
                                    {{ $detail->buku->kategori->nama_kategori ?? 'Umum' }}
                                </span>
                            </td>
                            <td style="color: #334155; font-size: 13px;">
                                {{ $detail->buku->lokasi_rak ?? '-' }}
                            </td>
                            <td style="text-align: center; font-weight: 600; color: #0f172a;">
                                {{ $detail->jumlah_pinjam }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Actions --}}
    <div style="display: flex; gap: 10px; border-top: 1px solid #e2e8f0; padding-top: 20px; align-items: center; flex-wrap: wrap;">
        @if (in_array($peminjaman->status, ['dipinjam', 'terlambat']))
            <a href="{{ route('petugas.pengembalian.create', ['peminjaman_id' => $peminjaman->id]) }}" class="btn btn-primary">
                Proses Pengembalian Buku
            </a>
            @if ($peminjaman->status === 'dipinjam')
                @if (($peminjaman->jumlah_perpanjangan ?? 0) < 1)
                    <form action="{{ route('petugas.peminjaman.perpanjang', $peminjaman->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin memperpanjang masa peminjaman {{ $peminjaman->kode_peminjaman }} selama 7 hari?');">
                        @csrf
                        <button type="submit" class="btn" style="background: #eef2ff; color: #4338ca; border: 1px solid #c7d2fe; font-weight: 600;">
                            🔄 Perpanjang Masa Pinjam (+7 Hari)
                        </button>
                    </form>
                @else
                    <span class="badge" style="background: #f1f5f9; color: #64748b; font-size: 12px; padding: 8px 12px; border: 1px solid #e2e8f0;">
                        ℹ️ Sudah Diperpanjang 1 Kali (Maksimal)
                    </span>
                @endif
            @endif
        @endif
        <a href="{{ route('petugas.peminjaman.index') }}" class="btn btn-secondary">
            &larr; Kembali ke Daftar
        </a>
    </div>

</div>
@endsection
