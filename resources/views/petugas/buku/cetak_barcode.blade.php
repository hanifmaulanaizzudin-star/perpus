<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Barcode - {{ $buku->kode_buku }}</title>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background: #f1f5f9;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .controls {
            margin-bottom: 25px;
            display: flex;
            gap: 12px;
        }

        .btn {
            padding: 10px 20px;
            font-size: 14px;
            font-weight: bold;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-print {
            background: #2563eb;
            color: white;
        }

        .btn-back {
            background: #64748b;
            color: white;
        }

        /* Desain Stiker Label Barcode */
        .barcode-card {
            background: white;
            width: 320px;
            padding: 18px;
            border-radius: 10px;
            border: 2px dashed #94a3b8;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            text-align: center;
        }

        .perpus-title {
            font-size: 11px;
            letter-spacing: 1.5px;
            color: #64748b;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 6px;
            margin-bottom: 8px;
        }

        .book-title {
            font-size: 15px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .book-category {
            font-size: 12px;
            color: #475569;
            margin-bottom: 10px;
        }

        .barcode-wrapper {
            margin: 10px auto;
            display: flex;
            justify-content: center;
        }

        .barcode-wrapper svg {
            width: 100%;
            max-width: 260px;
            height: auto;
        }

        .book-footer {
            margin-top: 8px;
            font-size: 12px;
            color: #334155;
            display: flex;
            justify-content: space-between;
            border-top: 1px dashed #cbd5e1;
            padding-top: 6px;
        }

        /* Saat di-print ke kertas stiker label */
        @media print {
            body {
                background: white;
                padding: 0;
                margin: 0;
            }

            .controls {
                display: none !important;
            }

            .barcode-card {
                border: 1px solid #000;
                box-shadow: none;
                margin: 0;
                page-break-inside: avoid;
            }
        }
    </style>

    {{-- Library JsBarcode CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
</head>
<body>

    <div class="controls">
        <button onclick="window.print()" class="btn btn-print">🖨️ Cetak Stiker Sekarang</button>
        <a href="{{ route('petugas.buku.show', $buku->id) }}" class="btn btn-back">← Kembali ke Detail Buku</a>
    </div>

    {{-- Kartu Label Barcode Siap Tempel --}}
    <div class="barcode-card">
        <div class="perpus-title">📚 PERPUSTAKAAN</div>
        
        <div class="book-title" title="{{ $buku->judul }}">{{ $buku->judul }}</div>
        <div class="book-category">{{ $buku->kategori->nama_kategori ?? 'Umum' }}</div>

        <div class="barcode-wrapper">
            <svg id="barcode"></svg>
        </div>

        <div class="book-footer">
            <span>Rak: <strong>{{ $buku->lokasi_rak ?? '-' }}</strong></span>
            <span>Stok: <strong>{{ $buku->jumlah_stok }}</strong></span>
        </div>
    </div>

    <script>
        // Generate Barcode Code128 dari Kode Buku
        JsBarcode("#barcode", "{{ $buku->kode_buku }}", {
            format: "CODE128",
            lineColor: "#000",
            width: 2.2,
            height: 55,
            displayValue: true,
            fontSize: 14,
            font: "Arial",
            textMargin: 4
        });
    </script>

</body>
</html>

