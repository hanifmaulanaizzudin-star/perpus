@extends('layouts.app')

@section('title', 'Manajemen Reservasi & Antrean Buku')

@section('content')
<div style="display: flex; flex-direction: column; gap: 22px;">

    {{-- Alert Success / Error / Info --}}
    @if (session('success'))
        <div class="alert alert-success">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    @if (session('info'))
        <div class="alert" style="background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
            <div>{{ session('info') }}</div>
        </div>
    @endif

    {{-- Metric Stat Cards --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
        
        <a href="{{ route('petugas.reservasi.index') }}" style="text-decoration: none;" class="card-box" style="margin-bottom: 0; padding: 18px 20px;">
            <span style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Total Booking</span>
            <div style="font-size: 26px; font-weight: 800; color: #0f172a; margin: 4px 0 2px;">{{ $counts['total'] }}</div>
            <span style="font-size: 12px; color: #64748b;">Semua riwayat booking</span>
        </a>

        <a href="{{ route('petugas.reservasi.index', ['status' => 'menunggu']) }}" style="text-decoration: none;" class="card-box" style="margin-bottom: 0; padding: 18px 20px;">
            <span style="font-size: 12px; color: #d97706; font-weight: 600; text-transform: uppercase;">Menunggu Antrean</span>
            <div style="font-size: 26px; font-weight: 800; color: #d97706; margin: 4px 0 2px;">{{ $counts['menunggu'] }}</div>
            <span style="font-size: 12px; color: #b45309;">Buku belum kembali</span>
        </a>

        <a href="{{ route('petugas.reservasi.index', ['status' => 'tersedia']) }}" style="text-decoration: none;" class="card-box" style="margin-bottom: 0; padding: 18px 20px; border-left: 4px solid #10b981;">
            <span style="font-size: 12px; color: #059669; font-weight: 600; text-transform: uppercase;">Siap Diambil</span>
            <div style="font-size: 26px; font-weight: 800; color: #059669; margin: 4px 0 2px;">{{ $counts['tersedia'] }}</div>
            <span style="font-size: 12px; color: #047857; font-weight: 600;">Disisihkan di meja sirkulasi</span>
        </a>

        <a href="{{ route('petugas.reservasi.index', ['status' => 'selesai']) }}" style="text-decoration: none;" class="card-box" style="margin-bottom: 0; padding: 18px 20px;">
            <span style="font-size: 12px; color: #4f46e5; font-weight: 600; text-transform: uppercase;">Selesai Dipinjam</span>
            <div style="font-size: 26px; font-weight: 800; color: #4f46e5; margin: 4px 0 2px;">{{ $counts['selesai'] }}</div>
            <span style="font-size: 12px; color: #4338ca;">Telah terealisasi</span>
        </a>

        <a href="{{ route('petugas.reservasi.index', ['status' => 'kedaluwarsa']) }}" style="text-decoration: none;" class="card-box" style="margin-bottom: 0; padding: 18px 20px;">
            <span style="font-size: 12px; color: #dc2626; font-weight: 600; text-transform: uppercase;">Kedaluwarsa</span>
            <div style="font-size: 26px; font-weight: 800; color: #dc2626; margin: 4px 0 2px;">{{ $counts['kedaluwarsa'] }}</div>
            <span style="font-size: 12px; color: #ef4444;">Lewat batas waktu</span>
        </a>

    </div>

    {{-- Main Container Card --}}
    <div class="card-box">
        
        {{-- Toolbar --}}
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid #f1f5f9;">
            
            {{-- Status Filter Tabs --}}
            <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
                <a href="{{ route('petugas.reservasi.index') }}" style="padding: 6px 14px; border-radius: 9999px; font-size: 12.5px; font-weight: 600; text-decoration: none; {{ empty($statusFilter) ? 'background: #4f46e5; color: #ffffff;' : 'background: #f1f5f9; color: #64748b;' }}">
                    Semua ({{ $counts['total'] }})
                </a>
                <a href="{{ route('petugas.reservasi.index', ['status' => 'tersedia']) }}" style="padding: 6px 14px; border-radius: 9999px; font-size: 12.5px; font-weight: 600; text-decoration: none; {{ $statusFilter === 'tersedia' ? 'background: #10b981; color: #ffffff;' : 'background: #ecfdf5; color: #047857;' }}">
                    Siap Diambil ({{ $counts['tersedia'] }})
                </a>
                <a href="{{ route('petugas.reservasi.index', ['status' => 'menunggu']) }}" style="padding: 6px 14px; border-radius: 9999px; font-size: 12.5px; font-weight: 600; text-decoration: none; {{ $statusFilter === 'menunggu' ? 'background: #f59e0b; color: #ffffff;' : 'background: #fffbeb; color: #b45309;' }}">
                    Antrean Menunggu ({{ $counts['menunggu'] }})
                </a>
                <a href="{{ route('petugas.reservasi.index', ['status' => 'selesai']) }}" style="padding: 6px 14px; border-radius: 9999px; font-size: 12.5px; font-weight: 600; text-decoration: none; {{ $statusFilter === 'selesai' ? 'background: #6366f1; color: #ffffff;' : 'background: #eef2ff; color: #4338ca;' }}">
                    Selesai ({{ $counts['selesai'] }})
                </a>
                <a href="{{ route('petugas.reservasi.index', ['status' => 'kedaluwarsa']) }}" style="padding: 6px 14px; border-radius: 9999px; font-size: 12.5px; font-weight: 600; text-decoration: none; {{ $statusFilter === 'kedaluwarsa' ? 'background: #ef4444; color: #ffffff;' : 'background: #fef2f2; color: #b91c1c;' }}">
                    Kedaluwarsa ({{ $counts['kedaluwarsa'] }})
                </a>
            </div>

            {{-- Right: Check Expiration & Search --}}
            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                <form action="{{ route('petugas.reservasi.cek-kedaluwarsa') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn" style="background: #f8fafc; border: 1px solid #cbd5e1; color: #334155; padding: 8px 14px; border-radius: 10px; font-size: 12.5px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;" title="Cek reservasi yang belum diambil melewati batas waktu 2 hari">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        Cek Kedaluwarsa
                    </button>
                </form>

                <form action="{{ route('petugas.reservasi.index') }}" method="GET" style="margin: 0; display: flex; gap: 6px;">
                    @if (!empty($statusFilter))
                        <input type="hidden" name="status" value="{{ $statusFilter }}">
                    @endif
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari kode, siswa, buku..." style="height: 38px; padding: 6px 14px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 13px; min-width: 200px;">
                    <button type="submit" class="btn btn-primary" style="padding: 0 14px; height: 38px; border-radius: 10px; font-size: 13px;">Cari</button>
                </form>
            </div>

        </div>

        {{-- Table --}}
        @if ($reservasi->count() > 0)
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 45px;">No</th>
                            <th style="width: 140px;">Kode Booking</th>
                            <th>Peminjam (Siswa)</th>
                            <th>Buku Dipesan</th>
                            <th style="width: 130px;">Tgl Reservasi</th>
                            <th style="width: 160px;">Status / Batas</th>
                            <th style="width: 170px; text-align: center;">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reservasi as $rsv)
                            <tr>
                                <td style="color: #94a3b8; font-weight: 600;">
                                    {{ $loop->iteration + ($reservasi->currentPage() - 1) * $reservasi->perPage() }}
                                </td>
                                <td>
                                    <code style="background: #f1f5f9; padding: 4px 8px; border-radius: 6px; font-size: 12.5px; font-weight: 700; color: #4f46e5;">{{ $rsv->kode_reservasi }}</code>
                                </td>
                                <td>
                                    <div style="font-weight: 700; color: #0f172a; font-size: 13.5px;">
                                        {{ $rsv->anggota->nama_lengkap ?? 'Anggota Dihapus' }}
                                    </div>
                                    <div style="font-size: 12px; color: #64748b; display: flex; gap: 8px; align-items: center; margin-top: 2px;">
                                        <span>NISN: <strong>{{ $rsv->anggota->nomor_induk ?? '-' }}</strong></span>
                                        @if ($rsv->anggota && !empty($rsv->anggota->no_telepon))
                                            <span>&bull;</span>
                                            <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $rsv->anggota->no_telepon)) }}" target="_blank" style="color: #059669; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 3px;" title="Chat WhatsApp Siswa">
                                                <span>WA</span>
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight: 700; color: #0f172a; font-size: 13.5px;">
                                        {{ $rsv->buku->judul ?? 'Buku Dihapus' }}
                                    </div>
                                    <div style="font-size: 12px; color: #64748b; margin-top: 2px;">
                                        Kode: <strong>{{ $rsv->buku->kode_buku ?? '-' }}</strong> &bull; Rak: <span style="color: #4f46e5; font-weight: 600;">{{ $rsv->buku->lokasi_rak ?? '-' }}</span> &bull; Stok Rak: <strong>{{ $rsv->buku->stok_tersedia ?? 0 }}</strong>
                                    </div>
                                </td>
                                <td style="color: #475569; font-size: 13px;">
                                    {{ \Carbon\Carbon::parse($rsv->tanggal_reservasi)->translatedFormat('d M Y') }}
                                    <div style="font-size: 11px; color: #94a3b8;">{{ \Carbon\Carbon::parse($rsv->tanggal_reservasi)->format('H:i') }} WIB</div>
                                </td>
                                <td>
                                    @if ($rsv->status === 'tersedia')
                                        <div style="display: inline-flex; align-items: center; gap: 6px; background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; padding: 4px 10px; border-radius: 9999px; font-size: 12px; font-weight: 700;">
                                            <span style="width: 7px; height: 7px; background: #10b981; border-radius: 50%;"></span>
                                            Siap Di Meja Sirkulasi
                                        </div>
                                        @if ($rsv->batas_ambil)
                                            <div style="font-size: 11px; color: #dc2626; font-weight: 600; margin-top: 3px;">
                                                Batas: {{ \Carbon\Carbon::parse($rsv->batas_ambil)->translatedFormat('d M Y') }}
                                            </div>
                                        @endif
                                    @elseif ($rsv->status === 'menunggu')
                                        <div style="display: inline-flex; align-items: center; gap: 6px; background: #fffbeb; color: #b45309; border: 1px solid #fde68a; padding: 4px 10px; border-radius: 9999px; font-size: 12px; font-weight: 700;">
                                            <span style="width: 7px; height: 7px; background: #f59e0b; border-radius: 50%;"></span>
                                            Antrean Ke-{{ $rsv->posisi_antrean }}
                                        </div>
                                    @elseif ($rsv->status === 'selesai')
                                        <span class="badge badge-aktif">Telah Dipinjam</span>
                                    @elseif ($rsv->status === 'kedaluwarsa')
                                        <span class="badge badge-danger">Kedaluwarsa</span>
                                    @elseif ($rsv->status === 'dibatalkan')
                                        <span style="background: #f1f5f9; color: #64748b; font-size: 12px; padding: 3px 8px; border-radius: 6px; font-weight: 600;">Dibatalkan</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    <div style="display: inline-flex; gap: 6px; align-items: center;">
                                        @if (in_array($rsv->status, ['tersedia', 'menunggu']))
                                            {{-- Tombol Realisasi Pinjam (1 Klik) --}}
                                            <form action="{{ route('petugas.reservasi.ambil', $rsv->id) }}" method="POST" onsubmit="return confirm('Proses penyerahan buku dan buat transaksi peminjaman untuk siswa {{ $rsv->anggota->nama_lengkap ?? '' }}?');" style="margin: 0;">
                                                @csrf
                                                <button type="submit" class="btn" style="background: #10b981; color: #ffffff; padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 700; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;" title="Serahkan buku ke siswa dan catat transaksi peminjaman resmi">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                                    Pinjamkan
                                                </button>
                                            </form>

                                            {{-- Tombol Batal --}}
                                            <form action="{{ route('petugas.reservasi.batal', $rsv->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan reservasi ini?');" style="margin: 0;">
                                                @csrf
                                                <button type="submit" style="background: #fff1f2; color: #e11d48; border: 1px solid #fecdd3; padding: 5px 10px; border-radius: 8px; font-size: 11.5px; font-weight: 600; cursor: pointer;" title="Batalkan booking">
                                                    Batal
                                                </button>
                                            </form>
                                        @else
                                            <span style="color: #cbd5e1; font-size: 12px;">Selesai</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pagination">
                {{ $reservasi->links() }}
            </div>
        @else
            <div style="text-align: center; padding: 48px 20px;">
                <div style="width: 56px; height: 56px; border-radius: 50%; background: #f1f5f9; color: #94a3b8; display: flex; align-items: center; justify-content: center; margin: 0 auto 14px;">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </div>
                <h3 style="font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 4px;">Tidak Ada Data Reservasi</h3>
                <p style="color: #64748b; font-size: 13.5px; margin: 0;">Belum ada data reservasi atau antrean buku dengan kriteria filter saat ini.</p>
            </div>
        @endif

    </div>

</div>
@endsection

