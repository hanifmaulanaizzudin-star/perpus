<html xmlns:o="urn:schemas-microsoft-com:office:office"
      xmlns:x="urn:schemas-microsoft-com:office:excel"
      xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    {!! '<' . '!--[if gte mso 9]><xml><' . 'x:ExcelWorkbook><' . 'x:ExcelWorksheets><' . 'x:ExcelWorksheet><' . 'x:Name>Kas Perpustakaan</' . 'x:Name><' . 'x:WorksheetOptions><' . 'x:DisplayGridlines/></' . 'x:WorksheetOptions></' . 'x:ExcelWorksheet></' . 'x:ExcelWorksheets></' . 'x:ExcelWorkbook></xml><![endif]-->' !!}
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            color: #000000;
        }
        .header-title {
            font-size: 15pt;
            font-weight: bold;
            text-align: center;
        }
        .header-subtitle {
            font-size: 11pt;
            font-weight: bold;
            text-align: center;
            color: #333333;
        }
        .header-desc {
            font-size: 9.5pt;
            text-align: center;
            color: #666666;
        }
        .meta-label {
            font-weight: bold;
            background-color: #f1f5f9;
        }
        .table-data {
            border-collapse: collapse;
            width: 100%;
        }
        .th-header {
            background-color: #0f766e;
            color: #ffffff;
            font-weight: bold;
            text-align: center;
            border: 1px solid #000000;
            padding: 6px;
        }
        .td-data {
            border: 1px solid #999999;
            padding: 5px;
            vertical-align: middle;
        }
        .td-center {
            text-align: center;
        }
        .td-right {
            text-align: right;
        }
        .td-total {
            background-color: #e2e8f0;
            font-weight: bold;
            border: 1px solid #000000;
        }
        .badge-locked {
            color: #065f46;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <table>
        <tr>
            <td colspan="7" class="header-title">
                {{ strtoupper($pengaturan->nama_perpustakaan ?? 'PERPUSTAKAAN PERPUSKU') }}
            </td>
        </tr>
        <tr>
            <td colspan="7" class="header-subtitle">
                LAPORAN PERTANGGUNGJAWABAN (LPJ) KAS & KEUANGAN PERPUSTAKAAN
            </td>
        </tr>
        <tr>
            <td colspan="7" class="header-desc">
                {{ $pengaturan->alamat ?? 'Jl. Pendidikan No. 45' }} | Periode: {{ \Carbon\Carbon::parse($tanggalMulai)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($tanggalSelesai)->translatedFormat('d F Y') }}
            </td>
        </tr>
        <tr>
            <td colspan="7" style="text-align: center; font-size: 9pt; color: #047857;">
                [STATUS DATA: TERVERIFIKASI & TERKUNCI PERMANEN - IMMUTABLE AUDIT TRAIL]
            </td>
        </tr>
        <tr><td colspan="7"></td></tr>

        {{-- Ringkasan Eksekutif --}}
        <tr>
            <td colspan="2" class="meta-label">Total Penerimaan Kas:</td>
            <td colspan="2" style="font-weight: bold; color: #0f766e;">Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}</td>
            <td class="meta-label">Tanggal Export:</td>
            <td colspan="2">{{ date('d F Y H:i') }} WIB</td>
        </tr>
        <tr>
            <td colspan="2" class="meta-label">Kas Ganti Rugi Buku:</td>
            <td colspan="2">Rp {{ number_format($totalGantiRugi, 0, ',', '.') }}</td>
            <td class="meta-label">Diexport Oleh:</td>
            <td colspan="2">{{ auth('petugas')->user()->nama_lengkap ?? 'Administrator' }}</td>
        </tr>
        <tr>
            <td colspan="2" class="meta-label">Kas Denda Keterlambatan:</td>
            <td colspan="2">Rp {{ number_format($totalDenda, 0, ',', '.') }}</td>
            <td class="meta-label">Total Transaksi:</td>
            <td colspan="2">{{ $pengembalian->count() }} Pengembalian</td>
        </tr>
        <tr><td colspan="7"></td></tr>

        {{-- Header Tabel --}}
        <thead>
            <tr>
                <th class="th-header" style="width: 40px;">No</th>
                <th class="th-header" style="width: 140px;">No. Bukti Kas</th>
                <th class="th-header" style="width: 90px;">Tgl Bayar</th>
                <th class="th-header" style="width: 130px;">Ref. Pinjam</th>
                <th class="th-header" style="width: 320px;">Uraian Penerimaan Kas / Ganti Rugi</th>
                <th class="th-header" style="width: 160px;">Petugas Penerima</th>
                <th class="th-header" style="width: 130px;">Jumlah Kas (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $nomor = 1;
                $adaKas = false;
            @endphp
            @forelse ($pengembalian as $k)
                @php
                    $kasTotal = $k->total_kas_masuk;
                @endphp
                @if ($kasTotal > 0)
                    @php $adaKas = true; @endphp
                    <tr>
                        <td class="td-data td-center">{{ $nomor++ }}</td>
                        <td class="td-data td-center" style="font-family: monospace;">{{ $k->kode_pengembalian }}</td>
                        <td class="td-data td-center">{{ $k->tanggal_pengembalian ? $k->tanggal_pengembalian->format('d/m/Y') : '-' }}</td>
                        <td class="td-data td-center" style="font-family: monospace;">{{ $k->peminjaman->kode_peminjaman ?? '-' }}</td>
                        <td class="td-data">
                            <strong>{{ $k->uraian_kas }}</strong>
                            @if (!empty($k->catatan))
                                <br><span style="font-size: 8.5pt; color: #555555;">{{ str_replace("\n", " | ", $k->catatan) }}</span>
                            @endif
                        </td>
                        <td class="td-data">{{ $k->petugas->nama_lengkap ?? 'Petugas' }}</td>
                        <td class="td-data td-right" style="font-weight: bold; mso-number-format:'\#\,\#\#0';">
                            {{ $kasTotal }}
                        </td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="7" class="td-data td-center">Tidak ada transaksi pada periode ini.</td>
                </tr>
            @endforelse

            @if (!$adaKas && $pengembalian->count() > 0)
                <tr>
                    <td colspan="7" class="td-data td-center">Semua buku kembali dalam kondisi baik tepat waktu (Nihil Kas Masuk).</td>
                </tr>
            @endif
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" class="td-total td-right" style="text-transform: uppercase;">
                    TOTAL PENERIMAAN KAS BERSIH:
                </td>
                <td class="td-total td-right" style="color: #047857; mso-number-format:'\#\,\#\#0';">
                    {{ $totalKasMasuk }}
                </td>
            </tr>
        </tfoot>
    </table>

    <br><br>

    {{-- Tanda Tangan Resmi Pengesahan --}}
    <table>
        <tr>
            <td colspan="3" style="text-align: center;">
                Mengetahui & Menyetujui,<br>
                <strong>Kepala Perpustakaan Sekolah</strong>
                <br><br><br><br>
                <u><strong>Dra. Hj. Siti Nurjanah, M.Pd.</strong></u><br>
                NIP. 19750812 200003 2 001
            </td>
            <td></td>
            <td colspan="3" style="text-align: center;">
                Tanggal Export: {{ date('d F Y') }}<br>
                <strong>Bendahara / Pengelola Kas Perpustakaan</strong>
                <br><br><br><br>
                <u><strong>{{ auth('petugas')->user()->nama_lengkap ?? 'Administrator Perpustakaan' }}</strong></u><br>
                NIP/ID: {{ auth('petugas')->user()->nama_pengguna ?? 'petugas' }}
            </td>
        </tr>
    </table>

</body>
</html>

