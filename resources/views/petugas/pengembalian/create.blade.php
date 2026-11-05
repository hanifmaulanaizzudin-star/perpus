@extends('layouts.app')

@section('title', 'Proses Pengembalian & Penyelesaian Buku')

@section('content')
<div class="card-box" style="max-width: 980px; margin: 0 auto;">

    <div style="margin-bottom: 24px; border-bottom: 1px solid #e2e8f0; padding-bottom: 16px;">
        <h2 style="font-size: 18px; font-weight: 700; color: #0f172a; margin-bottom: 4px;">Pemeriksaan Pengembalian & Kondisi Buku</h2>
        <p style="color: #64748b; font-size: 13.5px;">Pilih transaksi peminjaman aktif untuk memproses pengembalian buku, kalkulasi denda, atau penyelesaian buku hilang/rusak.</p>
    </div>

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- 1. PEMILIHAN TRANSAKSI PINJAMAN --}}
    <div style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 10px; padding: 18px; margin-bottom: 24px;">
        <label for="select_peminjaman" style="font-weight: 700; font-size: 13.5px; color: #0f172a; display: block; margin-bottom: 8px;">
            Pilih Transaksi Peminjaman:
        </label>
        
        <select 
            id="select_peminjaman" 
            class="form-control" 
            style="font-size: 13.5px; background: white;"
            onchange="if(this.value) window.location.href = '{{ route('petugas.pengembalian.create') }}?peminjaman_id=' + this.value"
        >
            <option value="">-- Cari Kode Peminjaman / Nama Anggota ({{ $peminjamanAktif->count() }} Transaksi Aktif) --</option>
            @foreach ($peminjamanAktif as $p)
                <option value="{{ $p->id }}" {{ (isset($selectedPeminjaman) && $selectedPeminjaman->id == $p->id) ? 'selected' : '' }}>
                    {{ $p->kode_peminjaman }} &bull; {{ $p->anggota->nama_lengkap ?? 'Anggota' }} (Tenggat: {{ \Carbon\Carbon::parse($p->tanggal_jatuh_tempo)->translatedFormat('d M Y') }})
                </option>
            @endforeach
        </select>
    </div>

    @if ($selectedPeminjaman)
        <form action="{{ route('petugas.pengembalian.store') }}" method="POST" id="form-pengembalian">
            @csrf
            <input type="hidden" name="peminjaman_id" value="{{ $selectedPeminjaman->id }}">

            {{-- 2. INFORMASI TRANSAKSI & PERHITUNGAN DENDA --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px;">
                <div>
                    <div style="font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: #64748b; margin-bottom: 6px;">
                        Data Peminjam
                    </div>
                    <div style="font-size: 15px; font-weight: 700; color: #0f172a;">
                        {{ $selectedPeminjaman->anggota->nama_lengkap ?? '-' }}
                    </div>
                    <div style="font-size: 13px; color: #64748b; margin-top: 2px;">
                        No. Induk: <code>{{ $selectedPeminjaman->anggota->nomor_induk ?? '-' }}</code>
                    </div>
                    <div style="font-size: 12.5px; color: #64748b; margin-top: 6px;">
                        Tgl Pinjam: {{ \Carbon\Carbon::parse($selectedPeminjaman->tanggal_pinjam)->translatedFormat('d M Y') }}
                    </div>
                </div>

                <div>
                    <div style="font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: #64748b; margin-bottom: 6px;">
                        Status Jatuh Tempo & Denda
                    </div>
                    <div style="font-size: 13px; color: #334155; margin-bottom: 6px;">
                        Tenggat: <strong>{{ \Carbon\Carbon::parse($selectedPeminjaman->tanggal_jatuh_tempo)->translatedFormat('d M Y') }}</strong>
                    </div>

                    @if ($hariKeterlambatan > 0)
                        <div style="background: #fef2f2; border: 1px solid #fecaca; padding: 12px 14px; border-radius: 8px;">
                            <div style="color: #991b1b; font-weight: 700; font-size: 13px;">
                                Terlambat: {{ $hariKeterlambatan }} Hari
                            </div>
                            <div style="font-size: 12.5px; color: #b91c1c; margin-top: 2px;">
                                Total Denda: <strong>Rp {{ number_format($totalDenda, 0, ',', '.') }}</strong>
                                <span style="font-size: 11px; opacity: 0.85;">(Rp {{ number_format($tarifDendaPerHari, 0, ',', '.') }} / hari / buku)</span>
                            </div>
                            <div style="margin-top: 8px;">
                                <label style="font-size: 11.5px; font-weight: 700; color: #7f1d1d; display: block; margin-bottom: 3px;">Status Pembayaran Denda:</label>
                                <select name="status_pembayaran_denda" class="form-control" style="font-size: 12.5px; background: #fff; height: 34px; padding: 4px 8px;">
                                    <option value="Lunas Dibayar Tunai">Lunas Dibayar Tunai</option>
                                    <option value="Belum Lunas (Ditagihkan)">Belum Lunas (Ditagihkan)</option>
                                    <option value="Diberi Keringanan (Bebas Denda)">Diberi Keringanan (Bebas Denda)</option>
                                </select>
                            </div>
                        </div>
                    @else
                        <div style="background: #ecfdf5; border: 1px solid #a7f3d0; padding: 10px 14px; border-radius: 8px; color: #065f46; font-size: 13px; font-weight: 600;">
                            Pengembalian tepat waktu (bebas denda keterlambatan)
                        </div>
                    @endif
                </div>
            </div>

            {{-- 3. KONDISI BUKU & RINCIAN GANTI RUGI --}}
            <div style="margin-bottom: 24px;">
                <div style="margin-bottom: 12px;">
                    <h3 style="font-size: 15px; font-weight: 700; color: #0f172a; margin: 0 0 4px;">
                        Pemeriksaan Fisik Buku
                    </h3>
                    <p style="font-size: 12.5px; color: #64748b; margin: 0;">
                        Tentukan kondisi fisik setiap buku. Jika buku berstatus <strong>Hilang</strong> atau <strong>Rusak Berat</strong>, masukkan nominal biaya ganti rugi atau metode penggantian buku.
                    </p>
                </div>

                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 45px;">No</th>
                                <th style="width: 60px;">Sampul</th>
                                <th style="width: 120px;">Kode</th>
                                <th>Judul Buku</th>
                                <th style="width: 170px;">Kondisi Buku <span style="color: #dc2626;">*</span></th>
                                <th>Penyelesaian & Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($selectedPeminjaman->detailPeminjaman as $idx => $detail)
                                <tr>
                                    <td>{{ $idx + 1 }}</td>
                                    <td>
                                        @if ($detail->buku && $detail->buku->gambar_sampul)
                                            <img src="{{ asset('storage/' . $detail->buku->gambar_sampul) }}" alt="Sampul" style="width: 32px; height: 44px; object-fit: cover; border-radius: 4px; border: 1px solid #e2e8f0;">
                                        @else
                                            <div style="width: 32px; height: 44px; background: #f1f5f9; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #94a3b8; border: 1px solid #e2e8f0;">No img</div>
                                        @endif
                                    </td>
                                    <td><code>{{ $detail->buku->kode_buku ?? '-' }}</code></td>
                                    <td>
                                        <strong style="color: #0f172a; font-size: 13.5px;">{{ $detail->buku->judul ?? 'Buku Dihapus' }}</strong>
                                        <div style="font-size: 11.5px; color: #64748b;">{{ $detail->buku->penulis ?? '-' }}</div>
                                    </td>
                                    <td>
                                        <select 
                                            name="kondisi_buku[{{ $detail->id }}]" 
                                            class="form-control select-kondisi-buku" 
                                            style="height: 38px; font-size: 13px; font-weight: 600;" 
                                            onchange="onKondisiChange(this, '{{ $detail->id }}')"
                                            required
                                        >
                                            <option value="baik">Kondisi Baik</option>
                                            <option value="rusak_ringan">Rusak Ringan</option>
                                            <option value="rusak_berat">Rusak Berat (Ganti)</option>
                                            <option value="hilang">Buku Hilang (Ganti Rugi)</option>
                                        </select>
                                    </td>
                                    <td>
                                        {{-- Sub-box ganti rugi (muncul otomatis jika hilang / rusak berat) --}}
                                        <div id="ganti-rugi-wrapper-{{ $detail->id }}" style="display: none; background: #fff1f2; border: 1px solid #fecdd3; border-radius: 8px; padding: 10px; margin-bottom: 8px;">
                                            <div style="font-size: 11.5px; font-weight: 700; color: #9f1239; margin-bottom: 6px;">
                                                Form Ganti Rugi Buku Hilang / Rusak:
                                            </div>
                                            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                                <div style="flex: 1; min-width: 140px;">
                                                    <label style="font-size: 11px; color: #64748b; display: block; margin-bottom: 2px;">Biaya Ganti Rugi (Rp):</label>
                                                    <input 
                                                        type="number" 
                                                        id="biaya-ganti-rugi-{{ $detail->id }}" 
                                                        name="biaya_ganti_rugi[{{ $detail->id }}]" 
                                                        class="form-control input-ganti-rugi" 
                                                        placeholder="Contoh: 50000"
                                                        min="0"
                                                        style="height: 32px; font-size: 12.5px;"
                                                        oninput="hitungTotalKeseluruhan()"
                                                    >
                                                </div>
                                                <div style="flex: 1; min-width: 170px;">
                                                    <label style="font-size: 11px; color: #64748b; display: block; margin-bottom: 2px;">Metode Penggantian:</label>
                                                    <select name="metode_ganti_rugi[{{ $detail->id }}]" class="form-control" style="height: 32px; font-size: 12.5px;">
                                                        <option value="Ganti Uang Tunai">Ganti Uang Tunai (Lunas)</option>
                                                        <option value="Ganti Buku Fisik">Ganti Buku Baru (Judul Sama)</option>
                                                        <option value="Tanggungan Belum Lunas">Tanggungan Belum Lunas (Ditagihkan)</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <input 
                                            type="text" 
                                            name="catatan_buku[{{ $detail->id }}]" 
                                            class="form-control" 
                                            placeholder="Catatan fisik tambahan (opsional)..."
                                            style="height: 34px; font-size: 12.5px;"
                                        >
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <small style="color: #64748b; display: block; margin-top: 8px; font-size: 12px;">
                    Catatan: Buku berkondisi <strong>Baik</strong> atau <strong>Rusak Ringan</strong> akan otomatis ditambahkan kembali ke stok rak perpustakaan. Buku <strong>Hilang</strong> atau <strong>Rusak Berat</strong> tidak akan dimasukkan kembali ke stok rak.
                </small>
            </div>

            {{-- 4. REKAPITULASI BIAYA & TOTAL PEMBAYARAN --}}
            <div id="box-rekap-biaya" style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 12px; padding: 18px 22px; margin-bottom: 24px;">
                <h4 style="font-size: 13.5px; font-weight: 700; color: #0f172a; text-transform: uppercase; letter-spacing: 0.04em; margin: 0 0 14px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                    <span>Rekapitulasi Keuangan Transaksi</span>
                    <span id="badge-kondisi-buku" class="badge badge-aktif">Semua Buku Kembali Baik</span>
                </h4>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 14px; font-size: 13.5px;">
                    <div style="background: #ffffff; padding: 14px 18px; border-radius: 10px; border: 1px solid #e2e8f0;">
                        <div style="color: #64748b; font-size: 12px; font-weight: 600;">Denda Keterlambatan</div>
                        <div style="font-size: 20px; font-weight: 800; color: #b91c1c; margin-top: 2px;">
                            Rp <span id="rekap-denda">{{ number_format($totalDenda, 0, ',', '.') }}</span>
                        </div>
                        <div style="font-size: 11px; color: #94a3b8;">{{ $hariKeterlambatan }} Hari keterlambatan</div>
                    </div>

                    <div style="background: #ffffff; padding: 14px 18px; border-radius: 10px; border: 1px solid #e2e8f0;">
                        <div style="color: #64748b; font-size: 12px; font-weight: 600;">Ganti Rugi Buku Hilang/Rusak</div>
                        <div style="font-size: 20px; font-weight: 800; color: #ea580c; margin-top: 2px;">
                            Rp <span id="rekap-ganti-rugi">0</span>
                        </div>
                        <div style="font-size: 11px; color: #94a3b8;" id="rekap-ganti-rugi-ket">Biaya taksiran buku</div>
                    </div>

                    <div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #ffffff; padding: 14px 18px; border-radius: 10px; box-shadow: 0 4px 14px rgba(15, 23, 42, 0.2);">
                        <div style="opacity: 0.8; font-size: 12px; font-weight: 600;">Total Yang Harus Dibayar</div>
                        <div style="font-size: 22px; font-weight: 800; color: #38bdf8; margin-top: 2px;">
                            Rp <span id="rekap-total-bayar">{{ number_format($totalDenda, 0, ',', '.') }}</span>
                        </div>
                        <div style="font-size: 11px; opacity: 0.8;">Denda + Ganti Rugi Buku</div>
                    </div>
                </div>
            </div>

            {{-- 5. CATATAN PENGEMBALIAN --}}
            <div class="form-group">
                <label for="catatan" style="font-weight: 700;">Catatan Tambahan Transaksi (Opsional)</label>
                <textarea 
                    id="catatan" 
                    name="catatan" 
                    class="form-control" 
                    placeholder="Keterangan serah terima, nama wali yang membayar ganti rugi, atau catatan petugas..."
                >{{ old('catatan') }}</textarea>
            </div>

            <div class="form-actions" style="border-top: 1px solid #e2e8f0; padding-top: 20px;">
                <button type="submit" id="btn-submit-pengembalian" class="btn btn-primary" style="font-size: 14px; padding: 10px 22px;">
                    Simpan Pengembalian Buku
                </button>
                <a href="{{ route('petugas.pengembalian.index') }}" class="btn btn-secondary">Batal</a>
            </div>

        </form>
    @else
        <div style="text-align: center; padding: 48px 20px; color: #94a3b8; background: #ffffff; border-radius: 8px; border: 1px dashed #cbd5e1;">
            <div style="font-size: 14px; font-weight: 600; color: #475569; margin-bottom: 4px;">Pilih Transaksi Peminjaman</div>
            <p style="font-size: 13px; color: #64748b;">Pilih salah satu nomor transaksi aktif pada dropdown di atas untuk memulai proses verifikasi buku.</p>
        </div>
    @endif

</div>

<script>
    function onKondisiChange(selectEl, detailId) {
        const val = selectEl.value;
        const gantiRugiWrapper = document.getElementById('ganti-rugi-wrapper-' + detailId);
        if (val === 'hilang' || val === 'rusak_berat') {
            gantiRugiWrapper.style.display = 'block';
        } else {
            gantiRugiWrapper.style.display = 'none';
            const inputBiaya = document.getElementById('biaya-ganti-rugi-' + detailId);
            if (inputBiaya) inputBiaya.value = '';
        }
        hitungTotalKeseluruhan();
    }

    function hitungTotalKeseluruhan() {
        const denda = {{ $totalDenda }};
        let totalGantiRugi = 0;
        let adaHilang = false;
        let adaRusak = false;

        document.querySelectorAll('.select-kondisi-buku').forEach(sel => {
            if (sel.value === 'hilang') adaHilang = true;
            if (sel.value === 'rusak_berat') adaRusak = true;
        });

        document.querySelectorAll('.input-ganti-rugi').forEach(input => {
            let val = parseFloat(input.value) || 0;
            totalGantiRugi += val;
        });

        const totalBayar = denda + totalGantiRugi;

        document.getElementById('rekap-ganti-rugi').innerText = totalGantiRugi.toLocaleString('id-ID');
        document.getElementById('rekap-total-bayar').innerText = totalBayar.toLocaleString('id-ID');

        const badgeKondisi = document.getElementById('badge-kondisi-buku');
        const submitBtn = document.getElementById('btn-submit-pengembalian');

        if (adaHilang) {
            badgeKondisi.className = 'badge badge-danger';
            badgeKondisi.innerText = 'Ada Buku Hilang (Wajib Ganti Rugi)';
            submitBtn.innerText = 'Simpan Penyelesaian Buku Hilang & Ganti Rugi';
            submitBtn.style.background = 'linear-gradient(135deg, #dc2626 0%, #ea580c 100%)';
        } else if (adaRusak) {
            badgeKondisi.className = 'badge badge-warning';
            badgeKondisi.innerText = 'Ada Buku Rusak (Penyelesaian Ganti Rugi)';
            submitBtn.innerText = 'Simpan Pengembalian (Buku Rusak)';
            submitBtn.style.background = 'linear-gradient(135deg, #d97706 0%, #ea580c 100%)';
        } else {
            badgeKondisi.className = 'badge badge-aktif';
            badgeKondisi.innerText = 'Semua Buku Kembali Baik';
            submitBtn.innerText = 'Simpan Pengembalian Buku';
            submitBtn.style.background = 'linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%)';
        }
    }
</script>
@endsection
