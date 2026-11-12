<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Perpustakaan ({{ \Carbon\Carbon::parse($tanggalMulai)->format('d-m-Y') }} s.d {{ \Carbon\Carbon::parse($tanggalSelesai)->format('d-m-Y') }})</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
            background: #f1f5f9;
            color: #0f172a;
            padding: 30px;
            font-size: 12px;
            line-height: 1.5;
        }

        .sheet {
            background: #ffffff;
            max-width: 900px;
            margin: 0 auto;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        /* Kop Surat */
        .kop-surat {
            display: flex;
            align-items: center;
            gap: 20px;
            border-bottom: 3px double #0f172a;
            padding-bottom: 16px;
            margin-bottom: 24px;
        }

        .kop-logo {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            background: #1e1b4b;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            font-weight: 800;
            flex-shrink: 0;
        }

        .kop-text {
            flex: 1;
            text-align: center;
        }

        .kop-text h2 {
            font-size: 18px;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: #0f172a;
            line-height: 1.2;
        }

        .kop-text h3 {
            font-size: 14px;
            font-weight: 700;
            color: #334155;
            margin-top: 2px;
        }

        .kop-text p {
            font-size: 11px;
            color: #64748b;
            margin-top: 4px;
        }

        /* Judul Laporan */
        .report-title-box {
            text-align: center;
            margin-bottom: 24px;
        }

        .report-title {
            font-size: 15px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #0f172a;
        }

        .report-subtitle {
            font-size: 12px;
            color: #475569;
            margin-top: 4px;
        }

        /* Ringkasan Angka */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 24px;
        }

        .summary-card {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 12px 14px;
            background: #f8fafc;
        }

        .summary-card .label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
        }

        .summary-card .val {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            margin-top: 2px;
        }

        /* Tabel Data */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-bottom: 24px;
        }

        table th, table td {
            border: 1px solid #cbd5e1;
            padding: 6px 10px;
            vertical-align: top;
        }

        table th {
            background-color: #f1f5f9;
            font-weight: 700;
            color: #0f172a;
            text-align: left;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 0.03em;
        }

        /* Tanda Tangan */
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            page-break-inside: avoid;
        }

        .signature-box {
            width: 240px;
            text-align: center;
        }

        .signature-box .role {
            color: #475569;
            margin-bottom: 65px;
        }

        .signature-box .name {
            font-weight: 700;
            color: #0f172a;
            border-top: 1px solid #0f172a;
            padding-top: 4px;
        }

        /* Floating Controls */
        .controls {
            max-width: 900px;
            margin: 0 auto 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 12.5px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            border: none;
            font-family: inherit;
        }

        .btn-print {
            background: #1e1b4b;
            color: #ffffff;
        }

        .btn-back {
            background: #ffffff;
            color: #334155;
            border: 1px solid #cbd5e1;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .controls {
                display: none !important;
            }
            .sheet {
                box-shadow: none;
                padding: 0;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

    <div class="controls">
        <a href="{{ route('petugas.laporan.index') }}" class="btn btn-back">
            &larr; Kembali ke Dashboard
        </a>
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <a href="{{ route('petugas.laporan.cetak-keuangan', ['tanggal_mulai' => $tanggalMulai, 'tanggal_selesai' => $tanggalSelesai]) }}" class="btn" style="background: #0f766e; color: #ffffff;">
                💰 Cetak LPJ Keuangan (Khusus Atasan)
            </a>
            <a href="{{ route('petugas.laporan.cetak-sirkulasi', ['tanggal_mulai' => $tanggalMulai, 'tanggal_selesai' => $tanggalSelesai]) }}" class="btn" style="background: #4338ca; color: #ffffff;">
                📚 Cetak Sirkulasi (Khusus Atasan)
            </a>
            <a href="{{ route('petugas.laporan.export-keuangan', ['tanggal_mulai' => $tanggalMulai, 'tanggal_selesai' => $tanggalSelesai]) }}" class="btn" style="background: #15803d; color: #ffffff;">
                📥 Unduh Excel
            </a>
            <button onclick="window.print()" class="btn btn-print">
                🖨️ Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <div class="sheet">

        {{-- Kop Surat --}}
        <div class="kop-surat">
            <div class="kop-logo">P</div>
            <div class="kop-text">
                <h2>{{ strtoupper($pengaturan->nama_perpustakaan ?? 'PERPUSTAKAAN PERPUSKU') }}</h2>
                <h3>UNIT PELAKSANA TEKNIS PERPUSTAKAAN SEKOLAH</h3>
                <p>{{ $pengaturan->alamat ?? 'Jl. Pendidikan No. 45, Kompleks Perpustakaan Sekolah' }} &bull; Telp/WA: (021) 7890123</p>
            </div>
        </div>

        {{-- Judul Laporan --}}
        <div class="report-title-box">
            <div class="report-title">LAPORAN REKAPITULASI SIRKULASI & KAS PERPUSTAKAAN</div>
            <div class="report-subtitle">
                Periode: <strong>{{ \Carbon\Carbon::parse($tanggalMulai)->translatedFormat('d F Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($tanggalSelesai)->translatedFormat('d F Y') }}</strong>
            </div>
        </div>

        {{-- Ringkasan Angka --}}
        <div class="summary-grid">
            <div class="summary-card">
                <div class="label">Total Peminjaman Buku</div>
                <div class="val">{{ $peminjaman->count() }} Transaksi</div>
            </div>
            <div class="summary-card">
                <div class="label">Pengembalian Terverifikasi</div>
                <div class="val">{{ $pengembalian->count() }} Transaksi</div>
            </div>
            <div class="summary-card" style="background: #f0fdf4; border-color: #bbf7d0;">
                <div class="label" style="color: #166534;">Total Kas Masuk (Kas Bersih)</div>
                <div class="val" style="color: #166534;">Rp {{ number_format($totalKasMasuk ?? $totalGantiRugi, 0, ',', '.') }}</div>
            </div>
        </div>

        {{-- Tabel 1: Rekap Sirkulasi Peminjaman --}}
        <div style="font-weight: 700; text-transform: uppercase; font-size: 11px; margin-bottom: 6px; color: #0f172a;">
            I. DAFTAR TRANSAKSI PEMINJAMAN BUKU
        </div>
        <table>
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th style="width: 120px;">Kode Pinjam</th>
                    <th>Peminjam (Nama & NISN)</th>
                    <th>Judul Buku</th>
                    <th style="width: 75px;">Tgl Pinjam</th>
                    <th style="width: 75px;">Tgl Tempo</th>
                    <th style="width: 80px;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($peminjaman as $idx => $p)
                    <tr>
                        <td style="text-align: center;">{{ $idx + 1 }}</td>
                        <td><code>{{ $p->kode_peminjaman }}</code></td>
                        <td>
                            <strong>{{ $p->anggota->nama_lengkap ?? 'Anggota' }}</strong>
                            <div>{{ $p->anggota->nomor_induk ?? '-' }} ({{ $p->anggota->kelas ?? '-' }})</div>
                        </td>
                        <td>
                            @foreach ($p->detailPeminjaman as $d)
                                <div>&bull; {{ $d->buku->judul ?? 'Buku' }} ({{ $d->jumlah_pinjam }}x)</div>
                            @endforeach
                        </td>
                        <td>{{ \Carbon\Carbon::parse($p->tanggal_pinjam)->format('d/m/Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($p->tanggal_jatuh_tempo)->format('d/m/Y') }}</td>
                        <td>
                            @if ($p->status === 'dikembalikan')
                                Dikembalikan
                            @elseif ($p->status === 'terlambat')
                                <strong>Terlambat</strong>
                            @else
                                Dipinjam
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: #64748b;">Tidak ada data peminjaman di periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Tabel 2: Rekap Kas & Pengembalian --}}
        <div style="font-weight: 700; text-transform: uppercase; font-size: 11px; margin-bottom: 6px; color: #0f172a;">
            II. DAFTAR PENGEMBALIAN BUKU & KAS GANTI RUGI
        </div>
        <table>
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th style="width: 120px;">Kode Kembali</th>
                    <th>Peminjam</th>
                    <th>Hasil Verifikasi Fisik & Ganti Rugi</th>
                    <th style="width: 80px;">Tgl Kembali</th>
                    <th style="width: 100px; text-align: right;">Ganti Rugi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pengembalian as $idx => $k)
                    @php
                        $subKas = $k->detailPengembalian->sum('biaya_ganti_rugi');
                    @endphp
                    <tr>
                        <td style="text-align: center;">{{ $idx + 1 }}</td>
                        <td><code>{{ $k->kode_pengembalian }}</code></td>
                        <td>{{ $k->peminjaman->anggota->nama_lengkap ?? '-' }}</td>
                        <td>
                            @foreach ($k->detailPengembalian as $d)
                                <div>
                                    &bull; {{ $d->detailPeminjaman->buku->judul ?? 'Buku' }}: 
                                    <strong>{{ strtoupper(str_replace('_', ' ', $d->kondisi_buku)) }}</strong>
                                    @if ($d->catatan) (<em>{{ $d->catatan }}</em>) @endif
                                </div>
                            @endforeach
                        </td>
                        <td>{{ $k->tanggal_pengembalian ? $k->tanggal_pengembalian->format('d/m/Y') : '-' }}</td>
                        <td style="text-align: right; font-weight: 700;">
                            Rp {{ number_format($subKas, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: #64748b;">Tidak ada data pengembalian di periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr style="background: #f8fafc; font-weight: 800;">
                    <td colspan="5" style="text-align: right; text-transform: uppercase;">Total Penerimaan Kas Ganti Rugi:</td>
                    <td style="text-align: right; color: #166534;">Rp {{ number_format($totalGantiRugi, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        {{-- Tanda Tangan Resmi --}}
        <div class="signature-section">
            <div class="signature-box">
                <div>Mengetahui,</div>
                <div class="role">Kepala Perpustakaan Sekolah</div>
                <div class="name">Dra. Hj. Siti Nurjanah, M.Pd.</div>
                <div style="font-size: 10px; color: #64748b;">NIP. 19750812 200003 2 001</div>
            </div>

            <div class="signature-box">
                <div>Dicetak pada: {{ date('d F Y') }}</div>
                <div class="role">Petugas Administrasi Perpustakaan</div>
                <div class="name">{{ auth('petugas')->user()->nama_lengkap ?? 'Administrator' }}</div>
                <div style="font-size: 10px; color: #64748b;">NIP/ID: {{ auth('petugas')->user()->nama_pengguna ?? 'petugas' }}</div>
            </div>
        </div>

    </div>

</body>
</html>

