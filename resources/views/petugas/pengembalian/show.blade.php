@extends('layouts.app')

@php
    $adaBukuHilang = $pengembalian->detailPengembalian->contains('kondisi_buku', 'hilang');
    $adaBukuRusak = $pengembalian->detailPengembalian->contains('kondisi_buku', 'rusak_berat');
@endphp

@section('title', $adaBukuHilang ? 'Bukti Penyelesaian Buku Hilang' : 'Detail Pengembalian Buku')

@section('content')
<div class="card-box" style="max-width: 950px; margin: 0 auto;">

    {{-- Alerts --}}
    @if (session('success'))
        <div class="alert alert-success" style="font-weight: 600; font-size: 14px;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-right: 6px; flex-shrink: 0;">
                <polyline points="20 6 9 17 4 12"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Header Struk Pengembalian / Penyelesaian --}}
    <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid #e2e8f0; padding-bottom: 20px; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            @if ($adaBukuHilang)
                <span style="font-size: 11.5px; color: #dc2626; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; display: inline-flex; align-items: center; gap: 4px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    BERITA ACARA & BUKTI PENYELESAIAN BUKU HILANG
                </span>
            @elseif ($adaBukuRusak)
                <span style="font-size: 11.5px; color: #d97706; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em;">
                    BUKTI PENGEMBALIAN & PENYELESAIAN BUKU RUSAK
                </span>
            @else
                <span style="font-size: 11.5px; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">
                    BUKTI PENGEMBALIAN BUKU LENGKAP
                </span>
            @endif

            <h1 style="font-size: 24px; font-weight: 800; color: #0f172a; margin-top: 4px; letter-spacing: -0.01em;">
                {{ $pengembalian->kode_pengembalian }}
            </h1>
            <div style="font-size: 12.5px; color: #64748b; margin-top: 2px;">
                Diverifikasi pada: {{ $pengembalian->tanggal_pengembalian ? $pengembalian->tanggal_pengembalian->translatedFormat('d F Y, H:i') : '-' }} WIB
            </div>
        </div>

        <div style="text-align: right;">
            @if ($adaBukuHilang)
                <span class="badge" style="background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; font-size: 13px; padding: 7px 16px; font-weight: 700; box-shadow: 0 2px 6px rgba(220, 38, 38, 0.1);">
                    Selesai (Ada Buku Hilang & Ganti Rugi)
                </span>
            @elseif ($adaBukuRusak)
                <span class="badge badge-warning" style="font-size: 13px; padding: 7px 16px; font-weight: 700;">
                    Selesai (Ada Buku Rusak)
                </span>
            @else
                <span class="badge badge-aktif" style="font-size: 13px; padding: 7px 16px; font-weight: 700;">
                    Selesai Dikembalikan (Lengkap)
                </span>
            @endif
        </div>
    </div>

    {{-- Info Grid --}}
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px; background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0;">
        <div>
            <h2 style="font-size: 12px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 12px;">
                Informasi Peminjam
            </h2>
            <div style="font-size: 14px; margin-bottom: 6px; color: #0f172a;">
                Nama Anggota: <strong>{{ $pengembalian->peminjaman->anggota->nama_lengkap ?? '-' }}</strong>
            </div>
            <div style="font-size: 13px; margin-bottom: 6px; color: #475569;">
                No. Induk / NISN: <code style="font-size: 12px; background: #ffffff; padding: 2px 7px; border: 1px solid #e2e8f0; border-radius: 6px; color: #0f172a; font-weight: 700;">{{ $pengembalian->peminjaman->anggota->nomor_induk ?? '-' }}</code>
            </div>
            <div style="font-size: 13px; color: #475569;">
                Referensi Pinjaman: 
                <a href="{{ route('petugas.peminjaman.show', $pengembalian->peminjaman_id) }}" style="color: #4338ca; text-decoration: none; font-weight: 600;">
                    {{ $pengembalian->peminjaman->kode_peminjaman ?? '-' }}
                </a>
            </div>
        </div>

        <div>
            <h2 style="font-size: 12px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 12px;">
                Petugas Verifikator
            </h2>
            <div style="font-size: 14px; margin-bottom: 6px; color: #0f172a;">
                Waktu Transaksi: <strong>{{ $pengembalian->tanggal_pengembalian ? $pengembalian->tanggal_pengembalian->translatedFormat('d F Y, H:i') : '-' }} WIB</strong>
            </div>
            <div style="font-size: 13px; margin-bottom: 6px; color: #475569;">
                Petugas Penerima: <strong>{{ $pengembalian->petugas->nama_lengkap ?? '-' }}</strong>
            </div>
            <div style="font-size: 12.5px; color: #64748b;">
                Perpustakaan: <strong>PerpusKu Digital</strong>
            </div>
        </div>
    </div>

    {{-- REKAPITULASI KEUANGAN & PENYELESAIAN (Khusus Buku Hilang / Denda) --}}
    @if ($pengembalian->catatan)
        <div style="background: {{ $adaBukuHilang ? '#fff1f2' : '#f0fdf4' }}; border: 1px solid {{ $adaBukuHilang ? '#fecdd3' : '#bbf7d0' }}; border-radius: 12px; padding: 18px 22px; margin-bottom: 28px;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; flex-wrap: wrap; gap: 8px;">
                <h3 style="font-size: 14px; font-weight: 800; color: {{ $adaBukuHilang ? '#9f1239' : '#166534' }}; margin: 0; text-transform: uppercase; letter-spacing: 0.04em;">
                    Rekapitulasi Keuangan & Penyelesaian Transaksi
                </h3>
                <span class="badge" style="background: {{ $adaBukuHilang ? '#ffe4e6' : '#dcfce7' }}; color: {{ $adaBukuHilang ? '#9f1239' : '#166534' }}; font-weight: 700;">
                    Tercatat Resmi di Sistem
                </span>
            </div>
            <div style="font-size: 13.5px; color: #334155; line-height: 1.7; white-space: pre-line; background: #ffffff; padding: 14px 18px; border-radius: 8px; border: 1px solid {{ $adaBukuHilang ? '#ffe4e6' : '#e2e8f0' }}; font-family: inherit;">
                {{ $pengembalian->catatan }}
            </div>
        </div>
    @endif

    {{-- Books Table --}}
    <div style="margin-bottom: 28px;">
        <h2 style="font-size: 15px; font-weight: 700; margin-bottom: 12px; color: #0f172a;">
            Daftar Buku & Hasil Pemeriksaan Fisik
        </h2>

        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th style="width: 65px;">Sampul</th>
                        <th style="width: 130px;">Kode Buku</th>
                        <th>Judul Buku</th>
                        <th style="width: 170px;">Kondisi Buku</th>
                        <th>Rincian Ganti Rugi / Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pengembalian->detailPengembalian as $idx => $detail)
                        <tr>
                            <td style="color: #64748b;">{{ $idx + 1 }}</td>
                            <td>
                                @if ($detail->detailPeminjaman && $detail->detailPeminjaman->buku && $detail->detailPeminjaman->buku->gambar_sampul)
                                    <img src="{{ asset('storage/' . $detail->detailPeminjaman->buku->gambar_sampul) }}" alt="Sampul" style="width: 38px; height: 52px; object-fit: cover; border-radius: 4px; border: 1px solid #e2e8f0;">
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
                                <code>{{ $detail->detailPeminjaman->buku->kode_buku ?? '-' }}</code>
                            </td>
                            <td>
                                <strong style="color: #0f172a; font-size: 13.5px;">{{ $detail->detailPeminjaman->buku->judul ?? 'Buku Dihapus' }}</strong>
                                <div style="font-size: 12px; color: #64748b;">{{ $detail->detailPeminjaman->buku->penulis ?? '-' }}</div>
                            </td>
                            <td>
                                @if ($detail->kondisi_buku === 'baik')
                                    <span class="badge badge-aktif">Kondisi Baik</span>
                                @elseif ($detail->kondisi_buku === 'rusak_ringan')
                                    <span class="badge badge-warning">Rusak Ringan</span>
                                @elseif ($detail->kondisi_buku === 'rusak_berat')
                                    <span class="badge badge-danger">Rusak Berat (Ganti)</span>
                                @elseif ($detail->kondisi_buku === 'hilang')
                                    <span class="badge" style="background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; font-weight: 700; padding: 4px 10px;">
                                        Buku Hilang
                                    </span>
                                @else
                                    <span class="badge badge-neutral">{{ ucfirst(str_replace('_', ' ', $detail->kondisi_buku)) }}</span>
                                @endif
                            </td>
                            <td>
                                @if ($detail->biaya_ganti_rugi > 0)
                                    <div style="margin-bottom: 4px;">
                                        <span style="display: inline-block; background: #fff1f2; color: #9f1239; border: 1px solid #fecdd3; padding: 2px 8px; border-radius: 6px; font-weight: 700; font-size: 12px;">
                                            Biaya Ganti Rugi: Rp {{ number_format($detail->biaya_ganti_rugi, 0, ',', '.') }}
                                        </span>
                                    </div>
                                @endif
                                @if ($detail->catatan)
                                    <div style="font-size: 12.5px; font-weight: 500; color: {{ $detail->kondisi_buku === 'hilang' ? '#991b1b' : '#334155' }};">
                                        {{ $detail->catatan }}
                                    </div>
                                @elseif (!($detail->biaya_ganti_rugi > 0))
                                    <span style="color: #94a3b8; font-size: 12.5px;">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Actions --}}
    <div class="form-actions" style="border-top: 1px solid #e2e8f0; padding-top: 20px; display: flex; gap: 10px; flex-wrap: wrap;">
        <button onclick="window.print()" class="btn btn-primary" style="font-size: 13.5px; padding: 9px 18px;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" style="margin-right: 4px;">
                <polyline points="6 9 6 2 18 2 18 9"/>
                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                <rect x="6" y="14" width="12" height="8"/>
            </svg>
            {{ $adaBukuHilang ? 'Cetak Berita Acara Buku Hilang' : 'Cetak Bukti Pengembalian' }}
        </button>
        <a href="{{ route('petugas.pengembalian.index') }}" class="btn btn-secondary">
            &larr; Kembali ke Daftar Pengembalian
        </a>
    </div>

    {{-- Official Print Signature Area (Only visible when printing) --}}
    <div class="print-signatures" style="display: none; margin-top: 45px; padding-top: 20px; border-top: 1px dashed #cbd5e1;">
        <div style="margin-bottom: 20px; font-size: 12.5px; color: #475569; text-align: justify; line-height: 1.6;">
            @if ($adaBukuHilang)
                Surat/Berita Acara ini menyatakan bahwa peminjam yang bersangkutan telah menyelesaikan kewajiban ganti rugi atas buku yang dinyatakan hilang/rusak sesuai dengan tata tertib perpustakaan sekolah.
            @else
                Surat ini menyatakan bahwa peminjam telah mengembalikan seluruh buku pinjaman dalam keadaan lengkap dan bebas dari tanggungan perpustakaan.
            @endif
        </div>

        <div style="display: flex; justify-content: space-between; text-align: center; font-size: 13px;">
            <div style="width: 220px;">
                <p style="margin-bottom: 60px; color: #475569;">Yang Menyerahkan / Menyelesaikan</p>
                <p style="font-weight: 700; color: #0f172a; border-top: 1px solid #94a3b8; padding-top: 4px;">
                    {{ $pengembalian->peminjaman->anggota->nama_lengkap ?? 'Anggota' }}
                </p>
                <p style="font-size: 11px; color: #64748b;">NISN: {{ $pengembalian->peminjaman->anggota->nomor_induk ?? '-' }}</p>
            </div>

            <div style="width: 220px;">
                <p style="margin-bottom: 60px; color: #475569;">Petugas Perpustakaan</p>
                <p style="font-weight: 700; color: #0f172a; border-top: 1px solid #94a3b8; padding-top: 4px;">
                    {{ $pengembalian->petugas->nama_lengkap ?? 'Petugas' }}
                </p>
                <p style="font-size: 11px; color: #64748b;">PerpusKu Digital System</p>
            </div>
        </div>
    </div>

</div>

<style>
@media print {
    body {
        background: #ffffff !important;
        color: #000000 !important;
        font-size: 12px !important;
    }
    .sidebar, .topbar, .form-actions, .btn {
        display: none !important;
    }
    .main-content {
        margin-left: 0 !important;
        padding: 0 !important;
        width: 100% !important;
    }
    .card-box {
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
        max-width: 100% !important;
    }
    .print-signatures {
        display: block !important;
    }
}
</style>
@endsection
