<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LPJ Keuangan Kas Perpustakaan ({{ \Carbon\Carbon::parse($tanggalMulai)->format('d-m-Y') }} s.d {{ \Carbon\Carbon::parse($tanggalSelesai)->format('d-m-Y') }})</title>

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
            background: #0f766e;
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
            margin-bottom: 20px;
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

        .security-badge {
            display: inline-block;
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
            font-size: 10px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 999px;
            margin-top: 6px;
            letter-spacing: 0.04em;
        }

        /* Ringkasan Angka Kas */
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

        .summary-card.highlight {
            background: #f0fdf4;
            border-color: #86efac;
        }

        .summary-card.highlight .val {
            color: #166534;
        }

        /* Tabel Data Kas */
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

        /* Tanda Tangan */
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

        /* Floating Controls */
        .controls {
            max-width: 920px;
            margin: 0 auto 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
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
            background: #0f766e;
            color: #ffffff;
        }

        .btn-excel {
            background: #15803d;
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
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('petugas.laporan.export-keuangan', ['tanggal_mulai' => $tanggalMulai, 'tanggal_selesai' => $tanggalSelesai]) }}" class="btn btn-excel">
                📥 Unduh Format Excel (.XLS)
            </a>
            <button onclick="window.print()" class="btn btn-print">
                🖨️ Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <div class="sheet">

        {{-- Kop Surat Resmi --}}
        <div class="kop-surat">
            <div class="kop-logo">Rp</div>
            <div class="kop-text">
                <h2>{{ strtoupper($pengaturan->nama_perpustakaan ?? 'PERPUSTAKAAN PERPUSKU') }}</h2>
                <h3>UNIT PELAKSANA TEKNIS PERPUSTAKAAN SEKOLAH</h3>
                <p>{{ $pengaturan->alamat ?? 'Jl. Pendidikan No. 45, Kompleks Perpustakaan Sekolah' }} &bull; Telp: (021) 7890123</p>
            </div>
        </div>

        {{-- Judul Laporan Khusus Keuangan --}}
        <div class="report-title-box">
            <div class="report-title">LAPORAN PERTANGGUNGJAWABAN (LPJ) KAS & KEUANGAN PERPUSTAKAAN</div>
            <div class="report-subtitle">
                Periode: <strong>{{ \Carbon\Carbon::parse($tanggalMulai)->translatedFormat('d F Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($tanggalSelesai)->translatedFormat('d F Y') }}</strong>
            </div>
            <div>
                <span class="security-badge">🔒 DATA TERVERIFIKASI & TERKUNCI (IMMUTABLE AUDIT TRAIL)</span>
            </div>
        </div>

        {{-- Ringkasan Keuangan --}}
        <div class="summary-grid">
            <div class="summary-card highlight">
                <div class="label" style="color: #166534;">TOTAL KAS DITERIMA</div>
                <div class="val">Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}</div>
            </div>
            <div class="summary-card">
                <div class="label">Kas Ganti Rugi Buku</div>
                <div class="val">Rp {{ number_format($totalGantiRugi, 0, ',', '.') }}</div>
            </div>
            <div class="summary-card">
                <div class="label">Kas Denda Keterlambatan</div>
                <div class="val">Rp {{ number_format($totalDenda, 0, ',', '.') }}</div>
            </div>
            <div class="summary-card">
                <div class="label">Total Pengembalian</div>
                <div class="val">{{ $pengembalian->count() }} Transaksi</div>
            </div>
        </div>

        {{-- Keterangan Audit Integritas Data --}}
        <div style="background: #f8fafc; border-left: 3px solid #0f766e; padding: 8px 12px; margin-bottom: 16px; font-size: 10.5px; color: #475569;">
            <strong>Catatan Kebijakan Audit:</strong> Laporan ini disajikan khusus untuk Pimpinan/Atasan. Dokumen berfokus pada aliran dana masuk kas perpustakaan, kode bukti pengembalian, uraian ganti rugi, dan akuntabilitas petugas penerima kas. Seluruh transaksi kas yang telah diverifikasi bersifat permanen dan tidak dapat diubah maupun dimanipulasi oleh petugas administrasi.
        </div>

        {{-- Tabel Rincian Kas Masuk --}}
        <div style="font-weight: 700; text-transform: uppercase; font-size: 11px; margin-bottom: 6px; color: #0f172a;">
            DAFTAR PENERIMAAN KAS MASUK PERPUSTAKAAN
        </div>
        <table>
            <thead>
                <tr>
                    <th style="width: 25px; text-align: center;">No</th>
                    <th style="width: 125px;">No. Bukti Kas</th>
                    <th style="width: 80px;">Tgl Bayar</th>
                    <th style="width: 110px;">Ref. Pinjam</th>
                    <th>Uraian Penerimaan Kas / Ganti Rugi</th>
                    <th style="width: 130px;">Petugas Penerima</th>
                    <th style="width: 105px; text-align: right;">Jumlah Kas</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $nomor = 1;
                    $transaksiDenganKas = 0;
                @endphp
                @forelse ($pengembalian as $k)
                    @php
                        $subGantiRugi = $k->total_ganti_rugi;
                        $subDenda = $k->nominal_denda;
                        $totalSubKas = $k->total_kas_masuk;
                    @endphp
                    @if ($totalSubKas > 0)
                        @php $transaksiDenganKas++; @endphp
                        <tr>
                            <td style="text-align: center;">{{ $nomor++ }}</td>
                            <td><code>{{ $k->kode_pengembalian }}</code></td>
                            <td>{{ $k->tanggal_pengembalian ? $k->tanggal_pengembalian->format('d/m/Y') : '-' }}</td>
                            <td><code>{{ $k->peminjaman->kode_peminjaman ?? '-' }}</code></td>
                            <td>
                                <div><strong>{{ $k->uraian_kas }}</strong></div>
                                @if (!empty($k->catatan))
                                    <div style="font-size: 10px; color: #64748b; margin-top: 2px;">
                                        <em>Ket: {{ Str::limit(str_replace("\n", " | ", $k->catatan), 110) }}</em>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $k->petugas->nama_lengkap ?? 'Petugas' }}</strong>
                                <div style="font-size: 9.5px; color: #64748b;">ID: {{ $k->petugas->nama_pengguna ?? '-' }}</div>
                            </td>
                            <td style="text-align: right; font-weight: 700; color: #0f766e;">
                                Rp {{ number_format($totalSubKas, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: #64748b; padding: 20px;">
                            Tidak ada transaksi penerimaan kas pada periode ini.
                        </td>
                    </tr>
                @endforelse

                @if ($transaksiDenganKas === 0 && $pengembalian->count() > 0)
                    <tr>
                        <td colspan="7" style="text-align: center; color: #64748b; padding: 16px;">
                            Terdapat {{ $pengembalian->count() }} transaksi pengembalian pada periode ini, seluruh buku dikembalikan tepat waktu dalam kondisi baik (Nihil Kas Masuk).
                        </td>
                    </tr>
                @endif
            </tbody>
            <tfoot>
                <tr style="background: #f8fafc; font-weight: 800; font-size: 11.5px;">
                    <td colspan="6" style="text-align: right; text-transform: uppercase;">
                        TOTAL PENERIMAAN KAS BERSIH (PERIODE INI):
                    </td>
                    <td style="text-align: right; color: #047857; font-size: 12.5px;">
                        Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>

        {{-- Lembar Pengesahan Resmi --}}
        <div class="signature-section">
            <div class="signature-box">
                <div>Mengetahui & Menyetujui,</div>
                <div class="role">Kepala Perpustakaan Sekolah</div>
                <div class="name">Dra. Hj. Siti Nurjanah, M.Pd.</div>
                <div style="font-size: 9.5px; color: #64748b;">NIP. 19750812 200003 2 001</div>
            </div>

            <div class="signature-box">
                <div>Tanggal Cetak: {{ date('d F Y') }}</div>
                <div class="role">Bendahara / Pengelola Kas Perpustakaan</div>
                <div class="name">{{ auth('petugas')->user()->nama_lengkap ?? 'Administrator Perpustakaan' }}</div>
                <div style="font-size: 9.5px; color: #64748b;">NIP/Petugas: {{ auth('petugas')->user()->nama_pengguna ?? 'petugas' }}</div>
            </div>
        </div>

    </div>

</body>
</html>

