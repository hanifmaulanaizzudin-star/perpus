@extends('layouts.app')

@section('title', 'Detail Buku')

@section('content')
<div class="card-box" style="max-width: 860px;">

    {{-- Alerts --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div style="display: flex; gap: 32px; flex-wrap: wrap; margin-bottom: 28px;">
        
        {{-- Cover Image & Barcode --}}
        <div style="flex-shrink: 0; width: 220px; margin: 0 auto;">
            <div style="width: 200px; height: 280px; margin: 0 auto; background: #f8fafc; border-radius: 10px; overflow: hidden; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center;">
                @if ($buku->gambar_sampul)
                    <img src="{{ asset('storage/' . $buku->gambar_sampul) }}" alt="{{ $buku->judul }}" style="width: 100%; height: 100%; object-fit: cover;">
                @else
                    <div style="color: #94a3b8; display: flex; flex-direction: column; align-items: center; gap: 6px;">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                        </svg>
                        <span style="font-size: 11px;">Tanpa Sampul</span>
                    </div>
                @endif
            </div>

            <div style="margin-top: 14px; text-align: center;">
                <span class="badge {{ $buku->status == 'tersedia' ? 'badge-aktif' : 'badge-nonaktif' }}">
                    Status: {{ ucfirst($buku->status) }}
                </span>
            </div>

            {{-- Barcode Preview --}}
            <div style="margin-top: 18px; padding: 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; text-align: center;">
                <div style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px;">
                    Barcode Koleksi
                </div>
                <div style="background: #ffffff; padding: 6px; border-radius: 4px; border: 1px solid #e2e8f0; display: inline-block; width: 100%;">
                    <svg id="barcode-preview" style="width: 100%; height: auto; display: block;"></svg>
                </div>
                <div style="margin-top: 10px;">
                    <a href="{{ route('petugas.buku.cetak-barcode', $buku->id) }}" target="_blank" class="btn btn-secondary" style="font-size: 12px; width: 100%; justify-content: center;">
                        Cetak Stiker Barcode
                    </a>
                </div>
            </div>
        </div>

        {{-- Book Metadata --}}
        <div style="flex: 1; min-width: 280px;">
            <div style="margin-bottom: 8px;">
                <span style="font-size: 11.5px; font-weight: 600; color: #475569; background: #f1f5f9; padding: 3px 9px; border-radius: 4px;">
                    {{ $buku->kategori->nama_kategori ?? 'Umum' }}
                </span>
            </div>

            <h1 style="font-size: 22px; font-weight: 700; color: #0f172a; margin-bottom: 6px; line-height: 1.35; letter-spacing: -0.01em;">
                {{ $buku->judul }}
            </h1>

            <p style="color: #64748b; font-size: 13.5px; margin-bottom: 20px;">
                Penulis: <strong style="color: #334155;">{{ $buku->penulis ?? '-' }}</strong> &bull; Penerbit: <strong style="color: #334155;">{{ $buku->penerbit ?? '-' }}</strong> ({{ $buku->tahun_terbit ?? '-' }})
            </p>

            <table style="width: 100%; margin-bottom: 20px;">
                <tbody>
                    <tr>
                        <td style="width: 140px; color: #64748b; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">Kode Buku</td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                            <code style="background: #f1f5f9; padding: 2px 6px; border-radius: 4px; font-size: 12.5px; color: #0f172a;">{{ $buku->kode_buku }}</code>
                        </td>
                    </tr>
                    <tr>
                        <td style="color: #64748b; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">Nomor ISBN</td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; color: #334155;">{{ $buku->isbn ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="color: #64748b; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">Lokasi Rak</td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-weight: 600; color: #0f172a;">
                            {{ $buku->lokasi_rak ?? 'Belum Ditentukan' }}
                        </td>
                    </tr>
                    <tr>
                        <td style="color: #64748b; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">Ketersediaan Stok</td>
                        <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                            <strong style="color: {{ $buku->stok_tersedia > 0 ? '#065f46' : '#991b1b' }};">
                                {{ $buku->stok_tersedia }}
                            </strong> 
                            tersedia dari total {{ $buku->jumlah_stok }} eksemplar
                        </td>
                    </tr>
                </tbody>
            </table>

            <div style="margin-top: 18px;">
                <h2 style="font-size: 13.5px; font-weight: 600; color: #0f172a; margin-bottom: 6px;">Sinopsis / Deskripsi:</h2>
                <div style="font-size: 13.5px; line-height: 1.65; color: #475569;">
                    {{ $buku->deskripsi ?? 'Tidak ada sinopsis untuk buku ini.' }}
                </div>
            </div>
        </div>

    </div>

    {{-- Actions --}}
    <div class="form-actions" style="border-top: 1px solid #e2e8f0; padding-top: 20px; display: flex; gap: 10px;">
        <a href="{{ route('petugas.buku.cetak-barcode', $buku->id) }}" target="_blank" class="btn btn-primary">
            Cetak Stiker Barcode
        </a>
        <a href="{{ route('petugas.buku.edit', $buku->id) }}" class="btn btn-secondary">
            Edit Data Buku
        </a>
        <a href="{{ route('petugas.buku.index') }}" class="btn btn-secondary">
            &larr; Kembali ke Daftar
        </a>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script>
    JsBarcode("#barcode-preview", "{{ $buku->kode_buku }}", {
        format: "CODE128",
        width: 1.6,
        height: 48,
        displayValue: true,
        fontSize: 12,
        margin: 5
    });
</script>
@endsection
