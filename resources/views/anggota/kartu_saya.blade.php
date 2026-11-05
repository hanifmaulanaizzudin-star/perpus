@extends('layouts.anggota')

@section('title', 'Kartu Anggota Saya')

@section('content')
<div class="card-box" style="max-width: 680px; margin: 0 auto; text-align: center;">

    <div style="margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid #edf2f7;">
        <h1 style="font-size: 22px; font-weight: 800; color: #0f172a; margin-bottom: 4px; letter-spacing: -0.02em;">
            Kartu Tanda Anggota Digital
        </h1>
        <p style="color: #64748b; font-size: 13.5px; margin: 0;">
            Gunakan kartu digital ini untuk meminjam buku di meja sirkulasi perpustakaan.
        </p>
    </div>

    <!-- The Member Card Display -->
    <div style="display: flex; justify-content: center; margin: 28px 0;">
        <div id="printable-member-card" style="width: 360px; height: 228px; background: #ffffff; border-radius: 16px; box-shadow: 0 12px 30px -5px rgba(15, 23, 42, 0.16); overflow: hidden; position: relative; display: flex; flex-direction: column; border: 1px solid #e2e8f0; text-align: left;">
            
            <!-- Header -->
            <div style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 60%, #4338ca 100%); color: #ffffff; padding: 12px 16px; display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="width: 24px; height: 24px; background: #ffffff; color: #4338ca; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 800;">P</div>
                    <div>
                        <div style="font-size: 11px; font-weight: 800; letter-spacing: 0.05em; text-transform: uppercase; line-height: 1.1;">PerpusKu Digital</div>
                        <div style="font-size: 8.5px; opacity: 0.8; font-weight: 500; letter-spacing: 0.04em;">KARTU TANDA ANGGOTA</div>
                    </div>
                </div>
                @if ($anggota->status_keanggotaan === 'aktif')
                    <div style="background: rgba(255, 255, 255, 0.18); backdrop-filter: blur(4px); font-size: 9px; font-weight: 700; text-transform: uppercase; padding: 2px 8px; border-radius: 9999px;">MEMBER</div>
                @elseif ($anggota->status_keanggotaan === 'ditangguhkan')
                    <div style="background: rgba(245, 158, 11, 0.4); backdrop-filter: blur(4px); font-size: 9px; font-weight: 700; text-transform: uppercase; padding: 2px 8px; border-radius: 9999px; color: #fff;">DITANGGUHKAN</div>
                @else
                    <div style="background: rgba(220, 38, 38, 0.4); backdrop-filter: blur(4px); font-size: 9px; font-weight: 700; text-transform: uppercase; padding: 2px 8px; border-radius: 9999px; color: #fff;">NONAKTIF</div>
                @endif
            </div>

            <!-- Body -->
            <div style="padding: 12px 16px; display: flex; gap: 14px; align-items: center; flex: 1;">
                <div style="width: 58px; height: 72px; background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%); border: 1px solid #c7d2fe; border-radius: 8px; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #4338ca; flex-shrink: 0; box-shadow: 0 2px 4px rgba(0,0,0,0.04);">
                    <div style="font-size: 20px; font-weight: 800;">
                        {{ strtoupper(substr($anggota->nama_lengkap, 0, 2)) }}
                    </div>
                    <div style="font-size: 8px; font-weight: 700; color: #6366f1; text-transform: uppercase; margin-top: 2px;">SISWA</div>
                </div>

                <div style="flex: 1; overflow: hidden;">
                    <div style="font-size: 13.5px; font-weight: 800; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; line-height: 1.25; margin-bottom: 4px;" title="{{ $anggota->nama_lengkap }}">
                        {{ $anggota->nama_lengkap }}
                    </div>

                    <table style="width: 100%; font-size: 10px; border-collapse: collapse;">
                        <tr>
                            <td style="color: #64748b; width: 55px; padding: 1.5px 0;">No. Induk</td>
                            <td style="color: #1e293b; font-weight: 600; padding: 1.5px 0;">: <strong>{{ $anggota->nomor_induk }}</strong></td>
                        </tr>
                        <tr>
                            <td style="color: #64748b; padding: 1.5px 0;">Kelas</td>
                            <td style="color: #1e293b; font-weight: 600; padding: 1.5px 0;">: {{ $anggota->kelas ?? '-' }} {{ $anggota->jurusan ? '(' . $anggota->jurusan . ')' : '' }}</td>
                        </tr>
                        <tr>
                            <td style="color: #64748b; padding: 1.5px 0;">Bergabung</td>
                            <td style="color: #1e293b; font-weight: 600; padding: 1.5px 0;">: {{ $anggota->tanggal_daftar ? \Carbon\Carbon::parse($anggota->tanggal_daftar)->format('d/m/Y') : date('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td style="color: #64748b; padding: 1.5px 0;">Status</td>
                            <td style="padding: 1.5px 0;">: 
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
            <div style="background: #fafbfc; border-top: 1px solid #edf2f7; padding: 6px 12px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                <svg id="member-card-barcode" style="width: 100%; height: 38px; display: block;"></svg>
                <div style="font-size: 8px; color: #94a3b8; font-weight: 500; text-align: center; margin-top: 2px;">Tunjukkan barcode ini ke petugas saat meminjam buku</div>
            </div>

        </div>
    </div>

    <!-- Actions -->
    <div style="display: flex; justify-content: center; gap: 12px; margin-top: 24px;">
        <button onclick="window.print()" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <polyline points="6 9 6 2 18 2 18 9"/>
                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                <rect x="6" y="14" width="12" height="8"/>
            </svg>
            Cetak Kartu Fisik
        </button>

        <a href="{{ route('anggota.dashboard') }}" class="btn btn-secondary">
            Kembali ke Dashboard
        </a>
    </div>

    <div style="margin-top: 20px; font-size: 12px; color: #64748b;">
        Tips: Kamu juga bisa screenshot atau simpan kartu ini di HP kamu untuk ditunjukkan langsung saat di perpustakaan.
    </div>

</div>

<!-- JsBarcode CDN -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script>
    JsBarcode("#member-card-barcode", "{{ $anggota->nomor_induk }}", {
        format: "CODE128",
        width: 1.5,
        height: 30,
        displayValue: true,
        fontSize: 9,
        margin: 2
    });
</script>
@endsection

