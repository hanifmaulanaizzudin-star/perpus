@extends('layouts.app')

@section('title', 'Data Anggota')

@section('content')
<div class="card-box">

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Toolbar --}}
    <div class="toolbar" style="flex-wrap: wrap; gap: 10px;">
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <a href="{{ route('petugas.anggota.create') }}" class="btn btn-primary">
                + Tambah Anggota
            </a>
            <button type="button" onclick="document.getElementById('modal-import-anggota').style.display='flex'" class="btn" style="background: #0f766e; color: #fff; font-weight: 600;">
                📥 Import Siswa (CSV)
            </button>
            <a href="{{ route('petugas.anggota.export-csv') }}" class="btn btn-secondary" style="font-weight: 600;" title="Export Seluruh Data Anggota">
                📊 Export CSV
            </a>
        </div>

        <form action="{{ route('petugas.anggota.index') }}" method="GET" class="search-box">
            <input
                type="text"
                name="search"
                placeholder="Cari nama, nomor induk, username..."
                value="{{ request('search') }}"
            >
            <button type="submit" class="btn btn-secondary">Cari</button>
            @if(request('search'))
                <a href="{{ route('petugas.anggota.index') }}" class="btn btn-secondary">Reset</a>
            @endif
        </form>
    </div>

    {{-- Modal Import Anggota --}}
    <div id="modal-import-anggota" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
        <div style="background: #ffffff; width: 100%; max-width: 520px; border-radius: 12px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); overflow: hidden;">
            <div style="padding: 20px 24px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="font-size: 16px; font-weight: 700; color: #0f172a; margin: 0;">📥 Import Data Siswa / Anggota Massal</h3>
                <button type="button" onclick="document.getElementById('modal-import-anggota').style.display='none'" style="background: none; border: none; font-size: 20px; cursor: pointer; color: #64748b;">&times;</button>
            </div>
            
            <form action="{{ route('petugas.anggota.import-csv') }}" method="POST" enctype="multipart/form-data" style="padding: 24px;">
                @csrf
                <div style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; padding: 16px; margin-bottom: 18px; font-size: 13px; color: #475569;">
                    <div style="font-weight: 700; color: #0f172a; margin-bottom: 4px;">Panduan Format File CSV:</div>
                    <p style="margin: 0 0 10px; font-size: 12.5px;">Gunakan template CSV resmi agar urutan kolom cocok (nomor_induk, nama, kelas, jurusan, telepon).</p>
                    <a href="{{ route('petugas.anggota.download-template') }}" class="btn" style="background: #eef2ff; color: #4338ca; border: 1px solid #c7d2fe; font-size: 12px; padding: 6px 12px; display: inline-flex; align-items: center; gap: 6px;">
                        📄 Unduh Template CSV Contoh
                    </a>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #0f172a; margin-bottom: 6px;">Pilih File CSV Siswa:</label>
                    <input type="file" name="file_csv" accept=".csv,text/csv,text/plain" class="form-control" required style="padding: 8px;">
                    <small style="color: #64748b; display: block; margin-top: 4px;">Format file: .csv (Maksimal 5MB)</small>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" onclick="document.getElementById('modal-import-anggota').style.display='none'" class="btn btn-secondary">Batal</button>
                    <button type="submit" class="btn btn-primary" style="background: #0f766e; border: none;">Mulai Proses Import</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th style="width: 140px;">Nomor Induk</th>
                    <th>Nama Anggota</th>
                    <th>Kelas / Jurusan</th>
                    <th>Username</th>
                    <th style="width: 120px;">Status</th>
                    <th style="width: 160px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($anggota as $index => $item)
                    <tr>
                        <td style="color: #64748b;">{{ $anggota->firstItem() + $index }}</td>
                        <td>
                            <code>{{ $item->nomor_induk }}</code>
                        </td>
                        <td>
                            <strong style="color: #0f172a;">{{ $item->nama_lengkap }}</strong>
                            <div style="font-size: 12px; color: #64748b;">
                                {{ $item->email ?? '-' }}
                            </div>
                        </td>
                        <td style="font-size: 13px; color: #334155;">
                            {{ $item->kelas ?? '-' }} {{ $item->jurusan ? '(' . $item->jurusan . ')' : '' }}
                        </td>
                        <td style="font-size: 13px; color: #475569;">
                            {{ $item->nama_pengguna }}
                        </td>
                        <td>
                            @if ($item->status_keanggotaan === 'aktif')
                                <span class="badge badge-aktif">Aktif</span>
                            @elseif ($item->status_keanggotaan === 'ditangguhkan')
                                <span class="badge badge-warning">Ditangguhkan</span>
                            @else
                                <span class="badge badge-nonaktif">Nonaktif</span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <div class="actions" style="justify-content: center;">
                                <a href="{{ route('petugas.anggota.cetak-kartu', $item->id) }}" target="_blank" class="act-barcode" title="Cetak Kartu Anggota">Kartu</a>
                                <a href="{{ route('petugas.anggota.show', $item->id) }}" class="act-detail">Detail</a>
                                <a href="{{ route('petugas.anggota.edit', $item->id) }}" class="act-edit">Edit</a>
                                <form action="{{ route('petugas.anggota.destroy', $item->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Yakin ingin menghapus data anggota ini?')"
                                      style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="act-delete">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: #64748b; padding: 35px 0;">
                            Belum ada data anggota perpustakaan yang terdaftar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="pagination">
        {{ $anggota->links() }}
    </div>

</div>
@endsection
