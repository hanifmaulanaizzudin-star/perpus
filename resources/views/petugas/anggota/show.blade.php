@extends('layouts.app')

@section('title', 'Detail Anggota: ' . $anggota->nama_lengkap)

@section('content')
<div class="card-box" style="max-width: 960px; margin: 0 auto;">

    {{-- Member Profile Header --}}
    <div style="display: flex; align-items: center; justify-content: space-between; gap: 20px; padding-bottom: 24px; border-bottom: 1px solid #e2e8f0; margin-bottom: 24px; flex-wrap: wrap;">
        <div style="display: flex; align-items: center; gap: 18px;">
            <div style="width: 64px; height: 64px; border-radius: 16px; background: linear-gradient(135deg, #1e1b4b 0%, #312e81 60%, #4338ca 100%); color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 22px; font-weight: 800; flex-shrink: 0; box-shadow: 0 4px 12px rgba(67, 56, 202, 0.25);">
                {{ strtoupper(substr($anggota->nama_lengkap, 0, 2)) }}
            </div>
            <div>
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px; flex-wrap: wrap;">
                    <h1 style="font-size: 22px; font-weight: 800; color: #0f172a; margin: 0; letter-spacing: -0.02em;">
                        {{ $anggota->nama_lengkap }}
                    </h1>
                    @if ($anggota->status_keanggotaan === 'aktif')
                        <span class="badge badge-aktif">Aktif</span>
                    @elseif ($anggota->status_keanggotaan === 'ditangguhkan')
                        <span class="badge badge-warning">Ditangguhkan</span>
                    @else
                        <span class="badge badge-nonaktif">Nonaktif</span>
                    @endif
                </div>
                <p style="font-size: 13px; color: #64748b; margin: 0;">
                    No. Induk: <code style="background: #f1f5f9; padding: 2px 7px; border-radius: 6px; font-size: 12px; font-weight: 700; color: #0f172a;">{{ $anggota->nomor_induk }}</code>
                    &bull; Bergabung sejak {{ $anggota->tanggal_daftar ? \Carbon\Carbon::parse($anggota->tanggal_daftar)->translatedFormat('d F Y') : '-' }}
                </p>
            </div>
        </div>

        {{-- Clearance / Bebas Pustaka Badge --}}
        <div>
            @if ($bebasPustaka)
                <div style="display: inline-flex; align-items: center; gap: 8px; background: #ecfdf5; border: 1px solid #a7f3d0; padding: 8px 16px; border-radius: 9999px; color: #065f46; font-size: 13px; font-weight: 700; box-shadow: 0 2px 6px rgba(16, 185, 129, 0.1);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    Bebas Pustaka (Tidak Ada Tanggungan)
                </div>
            @else
                <div style="display: inline-flex; align-items: center; gap: 8px; background: #fef2f2; border: 1px solid #fecaca; padding: 8px 16px; border-radius: 9999px; color: #991b1b; font-size: 13px; font-weight: 700; box-shadow: 0 2px 6px rgba(239, 68, 68, 0.1);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    Belum Bebas Pustaka ({{ $totalBukuDipinjam }} Buku Dipinjam)
                </div>
            @endif
        </div>
    </div>

    {{-- Metrics Cards --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 28px;">
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px 20px;">
            <div style="font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; color: #64748b; margin-bottom: 4px;">
                Total Riwayat Pinjam
            </div>
            <div style="font-size: 24px; font-weight: 800; color: #0f172a;">
                {{ $totalPinjaman }} <span style="font-size: 13px; font-weight: 500; color: #64748b;">transaksi</span>
            </div>
        </div>

        <div style="background: {{ $totalBukuDipinjam > 0 ? '#eff6ff' : '#f8fafc' }}; border: 1px solid {{ $totalBukuDipinjam > 0 ? '#bfdbfe' : '#e2e8f0' }}; border-radius: 12px; padding: 16px 20px;">
            <div style="font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; color: {{ $totalBukuDipinjam > 0 ? '#1e40af' : '#64748b' }}; margin-bottom: 4px;">
                Buku Sedang Dipinjam
            </div>
            <div style="font-size: 24px; font-weight: 800; color: {{ $totalBukuDipinjam > 0 ? '#1d4ed8' : '#0f172a' }};">
                {{ $totalBukuDipinjam }} <span style="font-size: 13px; font-weight: 500; color: #64748b;">eksemplar</span>
            </div>
        </div>

        <div style="background: {{ $pinjamanTerlambat->count() > 0 ? '#fef2f2' : '#f8fafc' }}; border: 1px solid {{ $pinjamanTerlambat->count() > 0 ? '#fecaca' : '#e2e8f0' }}; border-radius: 12px; padding: 16px 20px;">
            <div style="font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; color: {{ $pinjamanTerlambat->count() > 0 ? '#991b1b' : '#64748b' }}; margin-bottom: 4px;">
                Pinjaman Terlambat
            </div>
            <div style="font-size: 24px; font-weight: 800; color: {{ $pinjamanTerlambat->count() > 0 ? '#dc2626' : '#0f172a' }};">
                {{ $pinjamanTerlambat->count() }} <span style="font-size: 13px; font-weight: 500; color: #64748b;">transaksi</span>
            </div>
        </div>
    </div>

    {{-- Details Profile Grid --}}
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 20px; margin-bottom: 30px;">
        <h2 style="font-size: 14px; font-weight: 700; color: #0f172a; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 16px; display: flex; align-items: center; justify-content: space-between;">
            <span>Biodata Anggota</span>
            
            {{-- Quick WhatsApp Button --}}
            @php
                $rawPhone = preg_replace('/[^0-9]/', '', $anggota->no_telepon ?? '');
                if (str_starts_with($rawPhone, '0')) {
                    $waPhone = '62' . substr($rawPhone, 1);
                } else {
                    $waPhone = $rawPhone;
                }
            @endphp
            @if ($waPhone)
                <a href="https://wa.me/{{ $waPhone }}?text={{ urlencode('Halo ' . $anggota->nama_lengkap . ', kami dari Perpustakaan PerpusKu ingin mengonfirmasi terkait keanggotaan/pinjaman perpustakaan.') }}" 
                   target="_blank" 
                   class="btn" 
                   style="background: #25D366; color: #ffffff; font-size: 12px; font-weight: 600; padding: 4px 12px; border-radius: 9999px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 6px rgba(37, 211, 102, 0.25);">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
                    </svg>
                    Hubungi via WhatsApp
                </a>
            @endif
        </h2>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div>
                <table style="width: 100%; font-size: 13.5px; border-collapse: collapse;">
                    <tr>
                        <td style="color: #64748b; width: 140px; padding: 7px 0;">Nama Lengkap</td>
                        <td style="color: #0f172a; font-weight: 600; padding: 7px 0;">: {{ $anggota->nama_lengkap }}</td>
                    </tr>
                    <tr>
                        <td style="color: #64748b; padding: 7px 0;">No. Induk / NISN</td>
                        <td style="color: #0f172a; font-weight: 600; padding: 7px 0;">: {{ $anggota->nomor_induk }}</td>
                    </tr>
                    <tr>
                        <td style="color: #64748b; padding: 7px 0;">Username Login</td>
                        <td style="color: #0f172a; font-weight: 500; padding: 7px 0;">: {{ $anggota->nama_pengguna }}</td>
                    </tr>
                    <tr>
                        <td style="color: #64748b; padding: 7px 0;">Email</td>
                        <td style="color: #0f172a; padding: 7px 0;">: {{ $anggota->email ?? '-' }}</td>
                    </tr>
                </table>
            </div>

            <div>
                <table style="width: 100%; font-size: 13.5px; border-collapse: collapse;">
                    <tr>
                        <td style="color: #64748b; width: 140px; padding: 7px 0;">Kelas & Jurusan</td>
                        <td style="color: #0f172a; font-weight: 600; padding: 7px 0;">: {{ $anggota->kelas ?? '-' }} {{ $anggota->jurusan ? '(' . $anggota->jurusan . ')' : '' }}</td>
                    </tr>
                    <tr>
                        <td style="color: #64748b; padding: 7px 0;">Nomor HP / WhatsApp</td>
                        <td style="color: #0f172a; font-weight: 600; padding: 7px 0;">: {{ $anggota->no_telepon ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="color: #64748b; padding: 7px 0;">Alamat Tempat Tinggal</td>
                        <td style="color: #0f172a; padding: 7px 0;">: {{ $anggota->alamat ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="color: #64748b; padding: 7px 0;">Terakhir Login</td>
                        <td style="color: #0f172a; padding: 7px 0;">: {{ $anggota->terakhir_login ? $anggota->terakhir_login->format('d/m/Y H:i') : 'Belum pernah login' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    {{-- Member's Borrowing History Table --}}
    <div style="margin-bottom: 28px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
            <h2 style="font-size: 16px; font-weight: 700; color: #0f172a; margin: 0;">
                Riwayat Transaksi Peminjaman ({{ $anggota->peminjaman->count() }})
            </h2>
            @if ($anggota->status_keanggotaan === 'aktif')
                <a href="{{ route('petugas.peminjaman.create') }}" class="btn btn-secondary" style="font-size: 12.5px; padding: 5px 12px;">
                    + Transaksi Pinjam Baru
                </a>
            @endif
        </div>

        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th style="width: 45px;">No</th>
                        <th style="width: 150px;">Kode Transaksi</th>
                        <th>Buku yang Dipinjam</th>
                        <th style="width: 170px;">Jadwal Pinjam</th>
                        <th style="width: 110px;">Status</th>
                        <th style="width: 140px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($anggota->peminjaman as $idx => $p)
                        <tr>
                            <td style="color: #64748b;">{{ $idx + 1 }}</td>
                            <td>
                                <code>{{ $p->kode_peminjaman }}</code>
                            </td>
                            <td>
                                <div style="font-size: 13px; font-weight: 600; color: #0f172a;">
                                    @foreach ($p->detailPeminjaman as $d)
                                        <div>&bull; {{ $d->buku->judul ?? 'Buku Dihapus' }} ({{ $d->jumlah_pinjam }}x)</div>
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                <div style="font-size: 12px; color: #64748b;">
                                    Pinjam: {{ \Carbon\Carbon::parse($p->tanggal_pinjam)->format('d/m/Y') }}
                                </div>
                                <div style="font-size: 12.5px; font-weight: 700; color: {{ $p->status === 'terlambat' ? '#dc2626' : '#0f172a' }};">
                                    Tempo: {{ \Carbon\Carbon::parse($p->tanggal_jatuh_tempo)->format('d/m/Y') }}
                                </div>
                            </td>
                            <td>
                                @if ($p->status === 'dipinjam')
                                    <span class="badge" style="background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe;">Dipinjam</span>
                                @elseif ($p->status === 'terlambat')
                                    <span class="badge badge-danger">Terlambat</span>
                                @elseif ($p->status === 'dikembalikan')
                                    <span class="badge badge-aktif">Dikembalikan</span>
                                @else
                                    <span class="badge badge-neutral">{{ ucfirst($p->status) }}</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <div style="display: flex; gap: 6px; justify-content: center; align-items: center;">
                                    <a href="{{ route('petugas.peminjaman.show', $p->id) }}" class="act-detail" style="font-size: 11.5px; padding: 4px 8px;">
                                        Detail
                                    </a>
                                    @if (in_array($p->status, ['dipinjam', 'terlambat']))
                                        <a href="{{ route('petugas.pengembalian.create', ['peminjaman_id' => $p->id]) }}" class="btn" style="background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; font-size: 11.5px; padding: 4px 8px;">
                                            Kembali
                                        </a>
                                        @if ($waPhone)
                                            @php
                                                $bukuTitles = $p->detailPeminjaman->map(fn($d) => $d->buku->judul ?? 'Buku')->implode(', ');
                                                $waText = "Halo {$anggota->nama_lengkap}, pengingat dari Perpustakaan: Buku *{$bukuTitles}* (Kode: {$p->kode_peminjaman}) yang jatuh tempo pada " . \Carbon\Carbon::parse($p->tanggal_jatuh_tempo)->format('d/m/Y') . " mohon segera dikembalikan ya. Terima kasih.";
                                            @endphp
                                            <a href="https://wa.me/{{ $waPhone }}?text={{ urlencode($waText) }}" target="_blank" class="btn" style="background: #25D366; color: #fff; font-size: 11px; padding: 4px 7px;" title="Kirim Pengingat WhatsApp">
                                                WA
                                            </a>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: #64748b; padding: 28px 0;">
                                Anggota ini belum memiliki riwayat peminjaman buku.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Actions --}}
    <div class="form-actions" style="border-top: 1px solid #e2e8f0; padding-top: 20px; display: flex; gap: 10px; flex-wrap: wrap;">
        <a href="{{ route('petugas.anggota.cetak-kartu', $anggota->id) }}" target="_blank" class="btn btn-primary">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" style="margin-right: 2px;">
                <polyline points="6 9 6 2 18 2 18 9"/>
                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                <rect x="6" y="14" width="12" height="8"/>
            </svg>
            Cetak Kartu Anggota
        </a>
        <a href="{{ route('petugas.anggota.edit', $anggota->id) }}" class="btn btn-secondary">
            Edit Profil Anggota
        </a>
        <a href="{{ route('petugas.anggota.index') }}" class="btn btn-secondary">
            &larr; Kembali ke Data Anggota
        </a>
    </div>

</div>
@endsection
