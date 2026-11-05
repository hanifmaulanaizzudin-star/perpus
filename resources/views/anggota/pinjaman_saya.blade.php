@extends('layouts.anggota')

@section('title', 'Riwayat Pinjaman Saya')

@section('content')
<div class="card-box">

    {{-- Header --}}
    <div style="margin-bottom: 24px; padding-bottom: 18px; border-bottom: 1px solid #edf2f7; display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 14px;">
        <div>
            <h1 style="font-size: 22px; font-weight: 800; color: #0f172a; margin-bottom: 4px; letter-spacing: -0.02em;">
                Riwayat Peminjaman Buku
            </h1>
            <p style="color: #64748b; font-size: 13.5px; margin: 0;">
                Catatan seluruh transaksi peminjaman buku perpustakaan kamu yang sedang berjalan maupun telah selesai.
            </p>
        </div>
        <div style="background: #eef2ff; color: #4338ca; font-weight: 700; font-size: 13px; padding: 6px 14px; border-radius: 9999px;">
            Total {{ $riwayatPinjaman->total() }} Transaksi
        </div>
    </div>

    @if ($riwayatPinjaman->count() > 0)
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th style="width: 160px;">Kode Transaksi</th>
                        <th>Buku yang Dipinjam</th>
                        <th style="width: 140px;">Tgl Pinjam</th>
                        <th style="width: 140px;">Jatuh Tempo</th>
                        <th style="width: 130px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($riwayatPinjaman as $pinjam)
                        @php
                            $jatuhTempo = \Carbon\Carbon::parse($pinjam->tanggal_jatuh_tempo)->startOfDay();
                            $hariIni = \Carbon\Carbon::today()->startOfDay();
                            $isOverdue = $hariIni->greaterThan($jatuhTempo) && $pinjam->status !== 'dikembalikan';
                            $selisihHari = $hariIni->diffInDays($jatuhTempo);
                        @endphp
                        <tr>
                            <td style="color: #94a3b8; font-weight: 600;">
                                {{ $loop->iteration + ($riwayatPinjaman->currentPage() - 1) * $riwayatPinjaman->perPage() }}
                            </td>
                            <td>
                                <code style="background: #f1f5f9; padding: 3px 8px; border-radius: 6px; font-size: 12.5px; font-weight: 700; color: #4f46e5;">{{ $pinjam->kode_peminjaman }}</code>
                            </td>
                            <td>
                                @foreach ($pinjam->detailPeminjaman as $detail)
                                    <div style="display: flex; gap: 12px; align-items: center; margin-bottom: 8px;">
                                        @if ($detail->buku && $detail->buku->gambar_sampul)
                                            <img src="{{ asset('storage/' . $detail->buku->gambar_sampul) }}" alt="Sampul" style="width: 38px; height: 50px; object-fit: cover; border-radius: 6px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); flex-shrink: 0;">
                                        @else
                                            <div style="width: 38px; height: 50px; background: #eef2ff; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #6366f1; border: 1px solid #e0e7ff; flex-shrink: 0;">
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                                                </svg>
                                            </div>
                                        @endif
                                        <div>
                                            <div style="font-weight: 700; font-size: 13.5px; color: #0f172a;">
                                                {{ $detail->buku->judul ?? 'Buku Dihapus' }}
                                            </div>
                                            <div style="font-size: 12px; color: #64748b;">
                                                Kode: <strong>{{ $detail->buku->kode_buku ?? '-' }}</strong> &bull; Rak: <span style="color: #4f46e5; font-weight: 600;">{{ $detail->buku->lokasi_rak ?? '-' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </td>
                            <td style="color: #475569; font-weight: 500;">
                                {{ \Carbon\Carbon::parse($pinjam->tanggal_pinjam)->translatedFormat('d M Y') }}
                            </td>
                            <td>
                                <div style="font-weight: 700; color: {{ $isOverdue ? '#dc2626' : '#0f172a' }};">
                                    {{ $jatuhTempo->translatedFormat('d M Y') }}
                                </div>
                                @if ($isOverdue)
                                    <div style="color: #dc2626; font-size: 11.5px; font-weight: 600;">Lewat tenggat waktu</div>
                                @endif
                            </td>
                            <td>
                                @if ($pinjam->status === 'dikembalikan')
                                    <span class="badge badge-aktif">Dikembalikan</span>
                                @elseif ($pinjam->status === 'terlambat' || $isOverdue)
                                    <span class="badge badge-danger">Terlambat</span>
                                @else
                                    <span class="badge" style="background: #eef2ff; color: #4338ca; border: 1px solid #c7d2fe;">Sedang Dipinjam</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top: 24px;">
            {{ $riwayatPinjaman->links() }}
        </div>
    @else
        <div style="text-align: center; padding: 50px 20px; background: #fafbfc; border: 1px dashed #e2e8f0; border-radius: 14px;">
            <div style="width: 50px; height: 50px; border-radius: 50%; background: #eef2ff; color: #4f46e5; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 12px;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                </svg>
            </div>
            <h3 style="font-size: 15px; font-weight: 700; color: #1e293b; margin-bottom: 4px;">Belum Ada Riwayat Pinjaman</h3>
            <p style="font-size: 13.5px; color: #64748b; margin-bottom: 16px;">Kamu belum pernah meminjam buku di perpustakaan ini.</p>
            <a href="{{ route('anggota.katalog') }}" class="btn btn-primary" style="font-size: 13px;">
                Cari Buku di Katalog Sekarang
            </a>
        </div>
    @endif

</div>
@endsection
