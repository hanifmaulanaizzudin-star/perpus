<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Kartu Anggota - {{ $anggota->nama_lengkap }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #f1f5f9;
            color: #0f172a;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 24px;
            -webkit-font-smoothing: antialiased;
        }

        .controls {
            margin-bottom: 24px;
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            font-size: 13.5px;
            font-weight: 600;
            font-family: inherit;
            border-radius: 10px;
            cursor: pointer;
            text-decoration: none;
            border: none;
            transition: all 0.18s ease-in-out;
        }

        .btn-print {
            background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.3);
        }
        .btn-print:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(79, 70, 229, 0.4);
        }

        .btn-back {
            background: #ffffff;
            color: #334155;
            border: 1px solid #cbd5e1;
        }
        .btn-back:hover {
            background: #f8fafc;
        }

        /* Standard ID Card Size: 85.6mm x 53.98mm (~340px x 215px on screen) */
        .member-card-wrapper {
            width: 360px;
            height: 228px;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 30px -5px rgba(15, 23, 42, 0.15), 0 0 1px 1px rgba(0,0,0,0.04);
            overflow: hidden;
            position: relative;
            display: flex;
            flex-direction: column;
            border: 1px solid #e2e8f0;
        }

        .card-header-bar {
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 60%, #4338ca 100%);
            color: #ffffff;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-header-brand {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .card-header-icon {
            width: 24px;
            height: 24px;
            background: #ffffff;
            color: #4338ca;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 800;
        }

        .card-header-title {
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            line-height: 1.1;
        }
        .card-header-subtitle {
            font-size: 8.5px;
            opacity: 0.8;
            font-weight: 500;
            letter-spacing: 0.04em;
        }

        .card-header-pill {
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(4px);
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 2px 8px;
            border-radius: 9999px;
            letter-spacing: 0.05em;
        }

        .card-body {
            padding: 12px 16px;
            display: flex;
            gap: 14px;
            align-items: center;
            flex: 1;
        }

        .card-photo-avatar {
            width: 58px;
            height: 72px;
            background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
            border: 1px solid #c7d2fe;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #4338ca;
            flex-shrink: 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.04);
        }

        .card-avatar-initials {
            font-size: 20px;
            font-weight: 800;
        }

        .card-photo-tag {
            font-size: 8px;
            font-weight: 700;
            color: #6366f1;
            text-transform: uppercase;
            margin-top: 2px;
        }

        .card-info {
            flex: 1;
            overflow: hidden;
        }

        .card-member-name {
            font-size: 13.5px;
            font-weight: 800;
            color: #0f172a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.25;
            margin-bottom: 4px;
        }

        .card-info-table {
            width: 100%;
            font-size: 10px;
            border-collapse: collapse;
        }

        .card-info-table td {
            padding: 1.5px 0;
            vertical-align: top;
        }

        .card-info-table td.label {
            color: #64748b;
            width: 55px;
            font-weight: 500;
        }

        .card-info-table td.val {
            color: #1e293b;
            font-weight: 600;
        }

        .card-barcode-section {
            background: #fafbfc;
            border-top: 1px solid #edf2f7;
            padding: 6px 12px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        #barcode-svg {
            width: 100%;
            height: 38px;
            display: block;
        }

        .card-footer-notice {
            font-size: 8px;
            color: #94a3b8;
            font-weight: 500;
            text-align: center;
            margin-top: 2px;
        }

        /* Print Settings */
        @media print {
            body {
                background: white;
                padding: 0;
                margin: 0;
                min-height: auto;
                justify-content: flex-start;
            }

            .controls {
                display: none !important;
            }

            .member-card-wrapper {
                box-shadow: none !important;
                border: 1px solid #cbd5e1 !important;
                page-break-inside: avoid;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                margin: 20px auto;
            }
        }
    </style>
</head>
<body>

    <div class="controls">
        <button onclick="window.print()" class="btn btn-print">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <polyline points="6 9 6 2 18 2 18 9"/>
                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                <rect x="6" y="14" width="12" height="8"/>
            </svg>
            Cetak Kartu Sekarang
        </button>

        <a href="{{ route('petugas.anggota.index') }}" class="btn btn-back">
            Kembali ke Data Anggota
        </a>
    </div>

    <!-- Member Card Box -->
    <div class="member-card-wrapper">
        
        <!-- Header -->
        <div class="card-header-bar">
            <div class="card-header-brand">
                <div class="card-header-icon">P</div>
                <div>
                    <div class="card-header-title">PerpusKu Digital</div>
                    <div class="card-header-subtitle">KARTU TANDA ANGGOTA</div>
                </div>
            </div>
            @if ($anggota->status_keanggotaan === 'aktif')
                <div class="card-header-pill">MEMBER</div>
            @elseif ($anggota->status_keanggotaan === 'ditangguhkan')
                <div class="card-header-pill" style="background: rgba(245, 158, 11, 0.4); color: #fff;">DITANGGUHKAN</div>
            @else
                <div class="card-header-pill" style="background: rgba(220, 38, 38, 0.4); color: #fff;">NONAKTIF</div>
            @endif
        </div>

        <!-- Body -->
        <div class="card-body">
            <div class="card-photo-avatar">
                <div class="card-avatar-initials">
                    {{ strtoupper(substr($anggota->nama_lengkap, 0, 2)) }}
                </div>
                <div class="card-photo-tag">{{ strtoupper($anggota->status_keanggotaan) }}</div>
            </div>

            <div class="card-info">
                <div class="card-member-name" title="{{ $anggota->nama_lengkap }}">
                    {{ $anggota->nama_lengkap }}
                </div>

                <table class="card-info-table">
                    <tr>
                        <td class="label">No. Induk</td>
                        <td class="val">: <strong>{{ $anggota->nomor_induk }}</strong></td>
                    </tr>
                    <tr>
                        <td class="label">Kelas</td>
                        <td class="val">: {{ $anggota->kelas ?? '-' }} {{ $anggota->jurusan ? '(' . $anggota->jurusan . ')' : '' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Bergabung</td>
                        <td class="val">: {{ $anggota->tanggal_daftar ? \Carbon\Carbon::parse($anggota->tanggal_daftar)->format('d/m/Y') : date('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td class="label">Status</td>
                        <td class="val">: 
                            @if ($anggota->status_keanggotaan === 'aktif')
                                <span style="color: #059669; font-weight: 700;">AKTIF</span>
                            @elseif ($anggota->status_keanggotaan === 'ditangguhkan')
                                <span style="color: #d97706; font-weight: 700;">DITANGGUHKAN</span>
                            @else
                                <span style="color: #dc2626; font-weight: 700;">NONAKTIF (TIDAK BERLAKU)</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Barcode Area -->
        <div class="card-barcode-section">
            <svg id="barcode-svg"></svg>
            <div class="card-footer-notice">Tunjukkan barcode kartu ini kepada petugas saat sirkulasi buku</div>
        </div>

    </div>

    <!-- JsBarcode CDN -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <script>
        JsBarcode("#barcode-svg", "{{ $anggota->nomor_induk }}", {
            format: "CODE128",
            width: 1.5,
            height: 30,
            displayValue: true,
            fontSize: 9,
            margin: 2
        });
    </script>

</body>
</html>

