<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Sirkulasi Koleksi Buku ({{ \Carbon\Carbon::parse($tanggalMulai)->format('d-m-Y') }} s.d {{ \Carbon\Carbon::parse($tanggalSelesai)->format('d-m-Y') }})</title>

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
            font-size: 11.5px;
            line-height: 1.5;
        }

        .sheet {
            background: #ffffff;
            max-width: 920px;
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
            margin-bottom: 22px;
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

        /* Judul */
        .report-title-box {
            text-align: center;
            margin-bottom: 22px;
        }

        .report-title {
            font-size: 16px;
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

        /* Ringkasan Sirkulasi */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 22px;
        }

        .summary-card {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 10px 12px;
            background: #f8fafc;
        }

        .summary-card .label {
            font-size: 9.5px;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
        }

        .summary-card .val {
            font-size: 16px;
            font-weight: 800;
            color: #0f172a;
            margin-top: 2px;
        }

        /* Tabel */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-bottom: 24px;
        }

        table th, table td {
            border: 1px solid #cbd5e1;
            padding: 6px 9px;
            vertical-align: top;
        }

        table th {
            background-color: #f1f5f9;
            font-weight: 700;
            color: #0f172a;
            text-align: left;
            text-transform: uppercase;
            font-size: 9.5px;
            letter-spacing: 0.03em;
        }

        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 35px;
            page-break-inside: avoid;
        }

        .signature-box {
            width: 250px;
            text-align: center;
        }

        .signature-box .role {
            color: #475569;
            margin-bottom: 60px;
            font-size: 11px;
        }

        .signature-box .name {
            font-weight: 700;
            color: #0f172a;
            border-top: 1px solid #0f172a;
            padding-top: 4px;
            font-size: 11.5px;
        }

        .controls {
            max-width: 920px;
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
            &larr; Kembali ke Dashboard Laporan
        </a>
        <button onclick="window.print()" class="btn btn-print">
            🖨️ Cetak / Simpan PDF
        </button>
    </div>

    <div class="sheet">

        {{-- Kop Surat --}}
        <div class="kop-surat">
            <div class="kop-logo">📚</div>
            <div class="kop-text">
                <h2>{{ strtoupper($pengaturan->nama_perpustakaan ?? 'PERPUSTAKAAN PERPUSKU') }}</h2>
                <h3>UNIT PELAKSANA TEKNIS PERPUSTAKAAN SEKOLAH</h3>
                <p>{{ $pengaturan->alamat ?? 'Jl. Pendidikan No. 45' }} &bull; Telp: (021) 7890123</p>
            </div>
        </div>

        {{-- Judul --}}
        <div class="report-title-box">
            <div class="report-title">LAPORAN REKAPITULASI SIRKULASI KOLEKSI & STATISTIK LITERASI</div>
            <div class="report-subtitle">
                Periode: <strong>{{ \Carbon\Carbon::parse($tanggalMulai)->translatedFormat('d F Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($tanggalSelesai)->translatedFormat('d F Y') }}</strong>
            </div>
        </div>

        {{-- Ringkasan Indikator --}}
        <div class="summary-grid">
            <div class="summary-card">
                <div class="label">Total Sirkulasi</div>
                <div class="val">{{ $totalPeminjaman }} Transaksi</div>
            </div>
            <div class="summary-card">
                <div class="label">Volume Buku Dipinjam</div>
                <div class="val">{{ $totalBukuDipinjam }} Eksemplar</div>
            </div>
            <div class="summary-card">
                <div class="label">Tuntas Dikembalikan</div>
                <div class="val" style="color: #059669;">{{ $peminjamanSelesai }} Transaksi</div>
            </div>
            <div class="summary-card">
                <div class="label">Buku Sedang Beredar</div>
                <div class="val" style="color: #2563eb;">{{ $peminjamanAktif }} Aktif</div>
            </div>
        </div>

        {{-- Catatan Eksekutif --}}
        <div style="background: #f8fafc; border-left: 3px solid #1e1b4b; padding: 8px 12px; margin-bottom: 18px; font-size: 10.5px; color: #475569;">
            <strong>Ringkasan Eksekutif Koleksi:</strong> Dari seluruh aktivitas sirkulasi periode ini, tercatat {{ $totalBukuHilang }} buku dinyatakan hilang dalam masa peminjaman dan {{ $totalBukuRusak }} buku mengalami kerusakan fisik yang telah ditindaklanjuti dengan ganti rugi.
        </div>

        {{-- Top Koleksi Terpopuler --}}
        <div style="font-weight: 700; text-transform: uppercase; font-size: 11px; margin-bottom: 6px; color: #0f172a;">
            I. TOP 10 KOLEKSI BUKU PALING SERING DIPINJAM (MINAT BACA TERTINGGI)
        </div>
        <table>
            <thead>
                <tr>
                    <th style="width: 30px; text-align: center;">No</th>
                    <th>Judul Buku & Pengarang</th>
                    <th style="width: 140px;">Kategori</th>
                    <th style="width: 100px; text-align: center;">Frekuensi Pinjam</th>
                    <th style="width: 110px; text-align: center;">Total Eksemplar</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bukuPopuler as $idx => $item)
                    <tr>
                        <td style="text-align: center;">{{ $idx + 1 }}</td>
                        <td>
                            <strong>{{ $item->buku->judul ?? 'Buku Dihapus' }}</strong>
                            <div style="font-size: 10px; color: #64748b;">Penulis: {{ $item->buku->penulis ?? '-' }} | Penerbit: {{ $item->buku->penerbit ?? '-' }}</div>
                        </td>
                        <td>{{ $item->buku->kategori->nama_kategori ?? 'Umum' }}</td>
                        <td style="text-align: center; font-weight: 700; color: #1e1b4b;">{{ $item->total_dipinjam }} kali</td>
                        <td style="text-align: center; font-weight: 700; color: #059669;">{{ $item->total_eksemplar }} buku</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: #64748b; padding: 16px;">Belum ada data peminjaman di periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Rekap Status Sirkulasi Berjalan --}}
        <div style="font-weight: 700; text-transform: uppercase; font-size: 11px; margin-bottom: 6px; color: #0f172a;">
            II. DISTRIBUSI STATUS SIRKULASI BUKU PERIODE INI
        </div>
        <table>
            <thead>
                <tr>
                    <th>Indikator Kinerja Sirkulasi</th>
                    <th style="width: 140px; text-align: center;">Jumlah Transaksi</th>
                    <th style="width: 140px; text-align: center;">Persentase (%)</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $persenSelesai = $totalPeminjaman > 0 ? round(($peminjamanSelesai / $totalPeminjaman) * 100, 1) : 0;
                    $persenAktif = $totalPeminjaman > 0 ? round(($peminjamanAktif / $totalPeminjaman) * 100, 1) : 0;
                    $persenTerlambat = $totalPeminjaman > 0 ? round(($peminjamanTerlambat / $totalPeminjaman) * 100, 1) : 0;
                @endphp
                <tr>
                    <td><strong>Selesai Dikembalikan (Tepat Waktu & Tertib)</strong></td>
                    <td style="text-align: center; font-weight: 700; color: #059669;">{{ $peminjamanSelesai }}</td>
                    <td style="text-align: center; font-weight: 700;">{{ $persenSelesai }}%</td>
                </tr>
                <tr>
                    <td><strong>Sedang Beredar di Siswa (Masa Pinjam Aktif)</strong></td>
                    <td style="text-align: center; font-weight: 700; color: #2563eb;">{{ $peminjamanAktif }}</td>
                    <td style="text-align: center; font-weight: 700;">{{ $persenAktif }}%</td>
                </tr>
                <tr>
                    <td><strong>Melewati Batas Tenggat Waktu (Terlambat)</strong></td>
                    <td style="text-align: center; font-weight: 700; color: #dc2626;">{{ $peminjamanTerlambat }}</td>
                    <td style="text-align: center; font-weight: 700;">{{ $persenTerlambat }}%</td>
                </tr>
            </tbody>
            <tfoot>
                <tr style="background: #f8fafc; font-weight: 800;">
                    <td>TOTAL SIRKULASI DIAWASI:</td>
                    <td style="text-align: center;">{{ $totalPeminjaman }}</td>
                    <td style="text-align: center;">100%</td>
                </tr>
            </tfoot>
        </table>

        {{-- Tanda Tangan --}}
        <div class="signature-section">
            <div class="signature-box">
                <div>Mengetahui,</div>
                <div class="role">Kepala Perpustakaan Sekolah</div>
                <div class="name">Dra. Hj. Siti Nurjanah, M.Pd.</div>
                <div style="font-size: 9.5px; color: #64748b;">NIP. 19750812 200003 2 001</div>
            </div>

            <div class="signature-box">
                <div>Dicetak pada: {{ date('d F Y') }}</div>
                <div class="role">Koordinator Layanan Sirkulasi</div>
                <div class="name">{{ auth('petugas')->user()->nama_lengkap ?? 'Administrator Perpustakaan' }}</div>
                <div style="font-size: 9.5px; color: #64748b;">NIP/Petugas: {{ auth('petugas')->user()->nama_pengguna ?? 'petugas' }}</div>
            </div>
        </div>

    </div>

</body>
</html>

