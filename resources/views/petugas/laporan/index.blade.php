@extends('layouts.app')

@section('title', 'Laporan & Rekapitulasi Perpustakaan')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    {{-- Header Section --}}
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 style="font-size: 22px; font-weight: 800; color: #0f172a; margin-bottom: 4px; letter-spacing: -0.02em;">
                Laporan & Rekapitulasi Perpustakaan
            </h1>
            <p style="color: #64748b; font-size: 13.5px; margin: 0;">
                Pantau sirkulasi peminjaman, rekap kas masuk dari denda/ganti rugi, dan statistik literasi sekolah.
            </p>
        </div>
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            {{-- Export Excel --}}
            <a href="{{ route('petugas.laporan.export-keuangan', ['tanggal_mulai' => $tanggalMulai, 'tanggal_selesai' => $tanggalSelesai]) }}" 
               class="btn"
               style="background: #15803d; color: #ffffff; display: inline-flex; align-items: center; gap: 6px; font-weight: 700; padding: 9px 16px; border-radius: 8px; text-decoration: none; box-shadow: 0 2px 8px rgba(21, 128, 61, 0.25);">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/>
                    <line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                Export Excel (.xls)
            </a>

            {{-- Cetak LPJ Keuangan (Khusus Atasan) --}}
            <a href="{{ route('petugas.laporan.cetak-keuangan', ['tanggal_mulai' => $tanggalMulai, 'tanggal_selesai' => $tanggalSelesai]) }}" 
               target="_blank" 
               class="btn"
               style="background: #0f766e; color: #ffffff; display: inline-flex; align-items: center; gap: 6px; font-weight: 700; padding: 9px 16px; border-radius: 8px; text-decoration: none; box-shadow: 0 2px 8px rgba(15, 118, 110, 0.25);">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <line x1="12" y1="1" x2="12" y2="23"/>
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                </svg>
                Cetak LPJ Keuangan (Atasan)
            </a>

            {{-- Cetak Sirkulasi (Khusus Atasan) --}}
            <a href="{{ route('petugas.laporan.cetak-sirkulasi', ['tanggal_mulai' => $tanggalMulai, 'tanggal_selesai' => $tanggalSelesai]) }}" 
               target="_blank" 
               class="btn"
               style="background: #4338ca; color: #ffffff; display: inline-flex; align-items: center; gap: 6px; font-weight: 700; padding: 9px 16px; border-radius: 8px; text-decoration: none; box-shadow: 0 2px 8px rgba(67, 56, 202, 0.25);">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                </svg>
                Cetak Sirkulasi Buku
            </a>

            {{-- Cetak Dokumen Lengkap --}}
            <a href="{{ route('petugas.laporan.cetak', ['tanggal_mulai' => $tanggalMulai, 'tanggal_selesai' => $tanggalSelesai]) }}" 
               target="_blank" 
               class="btn btn-secondary"
               style="display: inline-flex; align-items: center; gap: 6px; font-weight: 700; padding: 9px 14px; border-radius: 8px; text-decoration: none;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <polyline points="6 9 6 2 18 2 18 9"/>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                    <rect x="6" y="14" width="12" height="8"/>
                </svg>
                Cetak Semua
            </a>
        </div>
    </div>

    {{-- Banner Informasi Integritas & Pemisahan Dokumen --}}
    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-left: 4px solid #0f766e; border-radius: 8px; padding: 14px 18px; display: flex; gap: 14px; align-items: flex-start;">
        <div style="background: #ecfdf5; color: #065f46; width: 34px; height: 34px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 16px;">
            🔒
        </div>
        <div style="font-size: 13px; line-height: 1.5; color: #334155;">
            <div style="font-weight: 800; color: #0f172a; margin-bottom: 2px;">
                Dokumen Terpisah untuk Pimpinan/Atasan & Jaminan Integritas Data
            </div>
            <div>
                <strong>Laporan Terpisah:</strong> Jika atasan membutuhkan laporan keuangan kas masuk, gunakan tombol <strong>"Cetak LPJ Keuangan (Atasan)"</strong> atau <strong>"Export Excel"</strong> — dokumen tersebut berfokus pada aliran kas masuk, bukti transaksi, dan akuntabilitas petugas tanpa membeberkan nama peminjam siswa.
            </div>
            <div style="margin-top: 4px; font-size: 12px; color: #64748b;">
                <strong>Anti-Manipulasi:</strong> Data transaksi pengembalian dan uang kas di sistem ini bersifat <em>Immutable</em> (terkunci permanen). Admin/petugas tidak memiliki akses untuk mengubah maupun menghapus riwayat kas yang sudah selesai.
            </div>
        </div>
    </div>

    {{-- Filter Toolbar --}}
    <div class="card-box" style="margin-bottom: 0; padding: 18px 22px;">
        <form action="{{ route('petugas.laporan.index') }}" method="GET" style="display: flex; gap: 16px; align-items: flex-end; flex-wrap: wrap;">
            <div>
                <label style="font-size: 12px; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 6px; letter-spacing: 0.04em;">
                    Tanggal Mulai:
                </label>
                <input type="date" name="tanggal_mulai" class="form-control" value="{{ $tanggalMulai }}" style="max-width: 170px;">
            </div>

            <div>
                <label style="font-size: 12px; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 6px; letter-spacing: 0.04em;">
                    Tanggal Selesai:
                </label>
                <input type="date" name="tanggal_selesai" class="form-control" value="{{ $tanggalSelesai }}" style="max-width: 170px;">
            </div>

            <div>
                <label style="font-size: 12px; font-weight: 700; text-transform: uppercase; color: #475569; display: block; margin-bottom: 6px; letter-spacing: 0.04em;">
                    Status Sirkulasi:
                </label>
                <select name="status" class="form-control" style="min-width: 150px;">
                    <option value="">Semua Status</option>
                    <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>Sedang Dipinjam</option>
                    <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                    <option value="dikembalikan" {{ request('status') == 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                </select>
            </div>

            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn btn-secondary" style="font-weight: 600; padding: 10px 18px;">
                    Filter Data
                </button>
                <a href="{{ route('petugas.laporan.index') }}" class="btn btn-secondary" style="padding: 10px 14px;" title="Reset Filter">
                    Reset
                </a>
            </div>

            <div style="margin-left: auto; font-size: 12.5px; color: #64748b; align-self: center;">
                Periode Terpilih: <strong>{{ \Carbon\Carbon::parse($tanggalMulai)->translatedFormat('d M Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($tanggalSelesai)->translatedFormat('d M Y') }}</strong>
            </div>
        </form>
    </div>

    {{-- Metrics Cards Grid --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 16px;">
        
        {{-- Total Pinjaman --}}
        <div class="card-box" style="margin-bottom: 0; padding: 18px 20px;">
            <div style="font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: #64748b; margin-bottom: 4px;">
                Total Peminjaman
            </div>
            <div style="font-size: 28px; font-weight: 800; color: #0f172a;">
                {{ $totalPeminjamanPeriode }} <span style="font-size: 13px; font-weight: 500; color: #64748b;">transaksi</span>
            </div>
            <div style="font-size: 12px; color: #4338ca; font-weight: 600; margin-top: 2px;">
                {{ $totalBukuDipinjam }} eksemplar buku
            </div>
        </div>

        {{-- Selesai Dikembalikan --}}
        <div class="card-box" style="margin-bottom: 0; padding: 18px 20px;">
            <div style="font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: #64748b; margin-bottom: 4px;">
                Selesai / Dikembalikan
            </div>
            <div style="font-size: 28px; font-weight: 800; color: #059669;">
                {{ $peminjamanSelesai }} <span style="font-size: 13px; font-weight: 500; color: #64748b;">transaksi</span>
            </div>
            <div style="font-size: 12px; color: #059669; font-weight: 600; margin-top: 2px;">
                Tanggungan tuntas
            </div>
        </div>

        {{-- Sedang Dipinjam & Terlambat --}}
        <div class="card-box" style="margin-bottom: 0; padding: 18px 20px;">
            <div style="font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: #64748b; margin-bottom: 4px;">
                Buku Beredar / Di Siswa
            </div>
            <div style="font-size: 28px; font-weight: 800; color: #1e40af;">
                {{ $peminjamanAktif }} <span style="font-size: 13px; font-weight: 500; color: #64748b;">aktif</span>
            </div>
            <div style="font-size: 12px; color: {{ $peminjamanTerlambat > 0 ? '#dc2626' : '#64748b' }}; font-weight: 600; margin-top: 2px;">
                {{ $peminjamanTerlambat }} transaksi terlambat
            </div>
        </div>

        {{-- Total Kas Masuk --}}
        <div class="card-box" style="margin-bottom: 0; padding: 18px 20px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #ffffff;">
            <div style="font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; opacity: 0.8; margin-bottom: 4px;">
                Total Kas Masuk (Kas Bersih)
            </div>
            <div style="font-size: 24px; font-weight: 800; color: #38bdf8;">
                Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}
            </div>
            <div style="font-size: 11.5px; opacity: 0.85; margin-top: 4px;">
                Ganti Rugi: Rp {{ number_format($totalGantiRugi, 0, ',', '.') }} | Denda: Rp {{ number_format($totalDenda, 0, ',', '.') }}
            </div>
        </div>

    </div>

    {{-- 2 Columns Analytics (Top Buku & Top Siswa) --}}
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; align-items: stretch;">
        
        {{-- Top 5 Buku Terpopuler --}}
        <div class="card-box" style="margin-bottom: 0;">
            <h2 style="font-size: 15px; font-weight: 700; color: #0f172a; margin-bottom: 14px; display: flex; align-items: center; justify-content: space-between;">
                <span>📚 5 Buku Paling Banyak Dipinjam</span>
                <span style="font-size: 12px; color: #64748b; font-weight: 500;">Periode ini</span>
            </h2>

            <table style="width: 100%; font-size: 13px;">
                <thead>
                    <tr>
                        <th style="width: 35px;">No</th>
                        <th>Judul Buku</th>
                        <th style="width: 110px;">Kategori</th>
                        <th style="width: 90px; text-align: right;">Frekuensi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($bukuPopuler as $idx => $item)
                        <tr>
                            <td style="color: #64748b;">{{ $idx + 1 }}</td>
                            <td>
                                <strong style="color: #0f172a;">{{ $item->buku->judul ?? 'Buku Dihapus' }}</strong>
                                <div style="font-size: 11.5px; color: #64748b;">{{ $item->buku->penulis ?? '-' }}</div>
                            </td>
                            <td>
                                <span style="font-size: 11px; background: #f1f5f9; padding: 2px 7px; border-radius: 4px; color: #475569;">
                                    {{ $item->buku->kategori->nama_kategori ?? 'Umum' }}
                                </span>
                            </td>
                            <td style="text-align: right; font-weight: 700; color: #4338ca;">
                                {{ $item->total_dipinjam }}x pinjam
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: #94a3b8; padding: 20px 0;">
                                Belum ada data peminjaman di periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Top 5 Siswa Teraktif --}}
        <div class="card-box" style="margin-bottom: 0;">
            <h2 style="font-size: 15px; font-weight: 700; color: #0f172a; margin-bottom: 14px; display: flex; align-items: center; justify-content: space-between;">
                <span>🏆 5 Anggota Paling Rajin Membaca</span>
                <span style="font-size: 12px; color: #64748b; font-weight: 500;">Periode ini</span>
            </h2>

            <table style="width: 100%; font-size: 13px;">
                <thead>
                    <tr>
                        <th style="width: 35px;">No</th>
                        <th>Nama Anggota</th>
                        <th style="width: 120px;">Kelas / Jurusan</th>
                        <th style="width: 90px; text-align: right;">Aktivitas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($anggotaAktif as $idx => $item)
                        <tr>
                            <td style="color: #64748b;">{{ $idx + 1 }}</td>
                            <td>
                                <strong style="color: #0f172a;">{{ $item->anggota->nama_lengkap ?? 'Anggota Dihapus' }}</strong>
                                <div style="font-size: 11.5px; color: #64748b;">NISN: {{ $item->anggota->nomor_induk ?? '-' }}</div>
                            </td>
                            <td style="color: #475569;">
                                {{ $item->anggota->kelas ?? '-' }} {{ $item->anggota->jurusan ? '(' . $item->anggota->jurusan . ')' : '' }}
                            </td>
                            <td style="text-align: right; font-weight: 700; color: #059669;">
                                {{ $item->total_transaksi }}x transaksi
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: #94a3b8; padding: 20px 0;">
                                Belum ada data aktivitas di periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

    {{-- Tabel Sirkulasi Peminjaman Detail --}}
    <div class="card-box">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 8px;">
            <div>
                <h2 style="font-size: 16px; font-weight: 800; color: #0f172a; margin: 0 0 2px;">
                    Daftar Transaksi Peminjaman ({{ $peminjaman->count() }} Data)
                </h2>
                <span style="font-size: 12.5px; color: #64748b;">Seluruh transaksi peminjaman pada rentang tanggal yang dipilih</span>
            </div>
        </div>

        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th style="width: 45px;">No</th>
                        <th style="width: 150px;">Kode Pinjam</th>
                        <th>Peminjam (Anggota)</th>
                        <th>Buku yang Dipinjam</th>
                        <th style="width: 160px;">Jadwal Pinjam</th>
                        <th style="width: 110px;">Status</th>
                        <th style="width: 130px;">Petugas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($peminjaman as $idx => $p)
                        <tr>
                            <td style="color: #64748b;">{{ $idx + 1 }}</td>
                            <td><code>{{ $p->kode_peminjaman }}</code></td>
                            <td>
                                <strong style="color: #0f172a;">{{ $p->anggota->nama_lengkap ?? 'Anggota Dihapus' }}</strong>
                                <div style="font-size: 11.5px; color: #64748b;">NISN: {{ $p->anggota->nomor_induk ?? '-' }}</div>
                            </td>
                            <td>
                                <div style="font-size: 12.5px;">
                                    @foreach ($p->detailPeminjaman as $d)
                                        <div>&bull; {{ $d->buku->judul ?? 'Buku Dihapus' }} ({{ $d->jumlah_pinjam }}x)</div>
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                <div style="font-size: 12px; color: #64748b;">
                                    Pinjam: {{ \Carbon\Carbon::parse($p->tanggal_pinjam)->format('d/m/Y') }}
                                </div>
                                <div style="font-size: 12px; font-weight: 700; color: {{ $p->status === 'terlambat' ? '#dc2626' : '#0f172a' }};">
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
                            <td style="font-size: 12.5px; color: #475569;">
                                {{ $p->petugas->nama_lengkap ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: #94a3b8; padding: 30px 0;">
                                Tidak ada transaksi peminjaman dalam rentang tanggal yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Tabel Buku Kas Pengembalian & Ganti Rugi --}}
    <div class="card-box">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 8px;">
            <div>
                <h2 style="font-size: 16px; font-weight: 800; color: #0f172a; margin: 0 0 2px;">
                    Rekapitulasi Kas Masuk & Pengembalian ({{ $pengembalian->count() }} Data)
                </h2>
                <span style="font-size: 12.5px; color: #64748b;">Rincian verifikasi buku kembali, penyelesaian buku hilang, dan penerimaan ganti rugi</span>
            </div>
            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; padding: 6px 14px; border-radius: 8px; font-size: 13px; font-weight: 700; color: #166534;">
                Total Kas Diterima: Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}
            </div>
        </div>

        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th style="width: 45px;">No</th>
                        <th style="width: 150px;">Kode Kembali</th>
                        <th style="width: 140px;">Kode Pinjam</th>
                        <th>Peminjam (Anggota)</th>
                        <th>Kondisi Buku & Ganti Rugi</th>
                        <th style="width: 140px;">Waktu Verifikasi</th>
                        <th style="width: 130px;">Petugas Penerima</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pengembalian as $idx => $k)
                        @php
                            $subGantiRugi = $k->detailPengembalian->sum('biaya_ganti_rugi');
                        @endphp
                        <tr>
                            <td style="color: #64748b;">{{ $idx + 1 }}</td>
                            <td><code>{{ $k->kode_pengembalian }}</code></td>
                            <td>
                                <a href="{{ route('petugas.peminjaman.show', $k->peminjaman_id) }}" style="color: #4338ca; text-decoration: none; font-weight: 600;">
                                    {{ $k->peminjaman->kode_peminjaman ?? '-' }}
                                </a>
                            </td>
                            <td>
                                <strong style="color: #0f172a;">{{ $k->peminjaman->anggota->nama_lengkap ?? 'Anggota Dihapus' }}</strong>
                                <div style="font-size: 11.5px; color: #64748b;">NISN: {{ $k->peminjaman->anggota->nomor_induk ?? '-' }}</div>
                            </td>
                            <td>
                                @foreach ($k->detailPengembalian as $d)
                                    <div style="font-size: 12.5px; margin-bottom: 3px;">
                                        &bull; {{ $d->detailPeminjaman->buku->judul ?? 'Buku' }}: 
                                        @if ($d->kondisi_buku === 'hilang')
                                            <span style="color: #dc2626; font-weight: 700;">HILANG</span>
                                            @if ($d->biaya_ganti_rugi > 0)
                                                <span style="background: #fef2f2; color: #991b1b; padding: 1px 6px; border-radius: 4px; font-weight: 700;">(Ganti Rugi: Rp {{ number_format($d->biaya_ganti_rugi, 0, ',', '.') }})</span>
                                            @endif
                                        @elseif ($d->kondisi_buku === 'rusak_berat')
                                            <span style="color: #d97706; font-weight: 700;">RUSAK BERAT</span>
                                            @if ($d->biaya_ganti_rugi > 0)
                                                <span style="background: #fffbeb; color: #b45309; padding: 1px 6px; border-radius: 4px; font-weight: 700;">(Ganti: Rp {{ number_format($d->biaya_ganti_rugi, 0, ',', '.') }})</span>
                                            @endif
                                        @else
                                            <span style="color: #059669; font-weight: 600;">Baik</span>
                                        @endif
                                    </div>
                                @endforeach
                            </td>
                            <td style="font-size: 12.5px; color: #475569;">
                                {{ $k->tanggal_pengembalian ? $k->tanggal_pengembalian->format('d/m/Y H:i') : '-' }} WIB
                            </td>
                            <td style="font-size: 12.5px; color: #475569;">
                                {{ $k->petugas->nama_lengkap ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: #94a3b8; padding: 30px 0;">
                                Tidak ada data pengembalian dalam rentang tanggal yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

