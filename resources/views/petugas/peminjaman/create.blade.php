@extends('layouts.app')

@section('title', 'Peminjaman Baru')

@section('content')
<div class="card-box" style="max-width: 950px; margin: 0 auto;">

    <div style="margin-bottom: 24px; border-bottom: 1px solid #e2e8f0; padding-bottom: 16px;">
        <h2 style="font-size: 18px; font-weight: 600; color: #0f172a; margin-bottom: 4px;">Form Transaksi Peminjaman</h2>
        <p style="color: #64748b; font-size: 13.5px;">Pilih data peminjam dan scan barcode buku yang akan dipinjam.</p>
    </div>

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul style="margin-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="form-peminjaman" action="{{ route('petugas.peminjaman.store') }}" method="POST">
        @csrf

        {{-- 1. IDENTITAS PEMINJAM & TANGGAL --}}
        <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 16px; margin-bottom: 24px;">
            <div class="form-group">
                <label for="anggota_id">Anggota Peminjam <span style="color: #dc2626;">*</span></label>
                <select id="anggota_id" name="anggota_id" class="form-control" required>
                    <option value="">-- Pilih Anggota Terdaftar --</option>
                    @foreach ($anggota as $agt)
                        <option value="{{ $agt->id }}" {{ old('anggota_id') == $agt->id ? 'selected' : '' }}>
                            {{ $agt->nama_lengkap }} ({{ $agt->nomor_induk }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="tanggal_pinjam">Tanggal Pinjam <span style="color: #dc2626;">*</span></label>
                <input 
                    type="date" 
                    id="tanggal_pinjam" 
                    name="tanggal_pinjam" 
                    class="form-control" 
                    value="{{ old('tanggal_pinjam', $tanggalPinjam) }}"
                    required
                >
            </div>

            <div class="form-group">
                <label for="tanggal_jatuh_tempo">Tenggat Kembali <span style="color: #dc2626;">*</span></label>
                <input 
                    type="date" 
                    id="tanggal_jatuh_tempo" 
                    name="tanggal_jatuh_tempo" 
                    class="form-control" 
                    value="{{ old('tanggal_jatuh_tempo', $tanggalJatuhTempo) }}"
                    required
                >
            </div>
        </div>

        {{-- 2. SCANNER BARCODE --}}
        <div style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; padding: 20px; margin-bottom: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; flex-wrap: wrap; gap: 10px;">
                <label style="font-weight: 700; color: #0f172a; font-size: 13.5px;">
                    Scan Barcode Buku atau Kartu Siswa
                </label>
                <button type="button" id="btn-toggle-camera" class="btn btn-secondary" style="font-size: 12.5px; padding: 6px 14px;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right: 4px;"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Buka Kamera Scanner
                </button>
            </div>

            {{-- Input Barcode --}}
            <div style="display: flex; gap: 8px;">
                <input 
                    type="text" 
                    id="barcode-input" 
                    class="form-control" 
                    placeholder="Scan kartu siswa atau buku (USB scanner / kamera / ketik kode)..."
                    style="font-size: 13.5px; height: 42px;"
                    autofocus
                    autocomplete="off"
                >
                <button type="button" id="btn-cari-barcode" class="btn btn-primary" style="white-space: nowrap; padding: 0 20px;">
                    Tambah
                </button>
            </div>

            <div id="scan-message" style="margin-top: 10px; font-size: 13px; display: none; font-weight: 500;"></div>

            {{-- Camera Viewfinder --}}
            <div id="camera-scanner-wrapper" style="display: none; margin-top: 16px; text-align: center;">
                <div id="reader" style="width: 100%; max-width: 540px; margin: 0 auto; border-radius: 8px; overflow: hidden; border: 1px solid #cbd5e1; background: #000;"></div>
                <small style="color: #64748b; display: block; margin-top: 8px;">Arahkan barcode stiker buku ke dalam area kamera.</small>
            </div>

            {{-- Manual Select Fallback --}}
            <div style="margin-top: 16px; border-top: 1px solid #e2e8f0; padding-top: 14px; display: flex; align-items: center; gap: 10px; font-size: 13px;">
                <span style="color: #64748b;">Atau cari manual:</span>
                <select id="select-manual-buku" class="form-control" style="max-width: 360px; height: 36px; font-size: 13px;">
                    <option value="">-- Pilih Buku dari Katalog --</option>
                    @foreach ($buku as $b)
                        <option value="{{ $b->kode_buku }}">
                            {{ $b->judul }} ({{ $b->kode_buku }}) - Sisa: {{ $b->stok_tersedia }}
                        </option>
                    @endforeach
                </select>
                <button type="button" id="btn-tambah-manual" class="btn btn-secondary" style="font-size: 12.5px; padding: 6px 14px;">+ Tambah</button>
            </div>
        </div>

        {{-- 3. TABEL BUKU YANG AKAN DIPINJAM --}}
        <div style="margin-bottom: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                <h3 style="font-size: 14.5px; font-weight: 600; color: #0f172a;">
                    Daftar Buku (<span id="total-buku-count">0</span>)
                </h3>
            </div>

            <table>
                <thead>
                    <tr>
                        <th style="width: 45px;">No</th>
                        <th style="width: 60px;">Sampul</th>
                        <th style="width: 120px;">Kode</th>
                        <th>Judul Buku</th>
                        <th>Penulis</th>
                        <th style="width: 110px;">Lokasi Rak</th>
                        <th style="width: 80px; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="cart-table-body">
                    <tr id="empty-row">
                        <td colspan="7" style="text-align: center; color: #94a3b8; padding: 32px 0;">
                            Belum ada buku yang dipilih. Silakan scan barcode atau gunakan pencarian manual di atas.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- 4. CATATAN --}}
        <div class="form-group">
            <label for="catatan">Catatan Peminjaman (Opsional)</label>
            <textarea 
                id="catatan" 
                name="catatan" 
                class="form-control" 
                placeholder="Catatan kondisi awal buku saat dipinjam atau keterangan lainnya..."
            >{{ old('catatan') }}</textarea>
        </div>

        {{-- SUBMIT ACTIONS --}}
        <div class="form-actions" style="border-top: 1px solid #e2e8f0; padding-top: 20px;">
            <button type="submit" class="btn btn-primary" id="btn-submit">
                Simpan Transaksi Peminjaman
            </button>
            <a href="{{ route('petugas.peminjaman.index') }}" class="btn btn-secondary">Batal</a>
        </div>

    </form>

</div>

<script src="https://unpkg.com/html5-qrcode"></script>

<script>
    const keranjangBuku = [];

    const barcodeInput = document.getElementById('barcode-input');
    const btnCariBarcode = document.getElementById('btn-cari-barcode');
    const scanMessage = document.getElementById('scan-message');
    const cartTableBody = document.getElementById('cart-table-body');
    const emptyRow = document.getElementById('empty-row');
    const totalBukuCount = document.getElementById('total-buku-count');
    const btnToggleCamera = document.getElementById('btn-toggle-camera');
    const cameraWrapper = document.getElementById('camera-scanner-wrapper');

    let html5QrCode = null;
    let isCameraRunning = false;

    barcodeInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            prosesBarcode(this.value.trim());
        }
    });

    btnCariBarcode.addEventListener('click', function() {
        prosesBarcode(barcodeInput.value.trim());
    });

    document.getElementById('btn-tambah-manual').addEventListener('click', function() {
        const select = document.getElementById('select-manual-buku');
        if (select.value) {
            prosesBarcode(select.value);
            select.value = '';
        }
    });

    function prosesBarcode(kode) {
        if (!kode) {
            tampilkanPesan('Silakan scan atau masukkan kode barcode buku.', 'danger');
            return;
        }

        const sudahAda = keranjangBuku.some(item => item.kode_buku.toLowerCase() === kode.toLowerCase());
        if (sudahAda) {
            tampilkanPesan(`Buku '${kode}' sudah ada di daftar pinjam.`, 'danger');
            barcodeInput.value = '';
            barcodeInput.focus();
            return;
        }

        tampilkanPesan('Memeriksa barcode...', 'info');

        fetch(`{{ route('petugas.peminjaman.cari-buku') }}?kode=${encodeURIComponent(kode)}`)
            .then(response => response.json().then(data => ({ status: response.status, body: data })))
            .then(res => {
                if (res.status === 200 && res.body.success) {
                    const buku = res.body.buku;
                    tambahKeKeranjang(buku);
                    tampilkanPesan(`Buku "${buku.judul}" berhasil ditambahkan ke daftar pinjam.`, 'success');
                    barcodeInput.value = '';
                    barcodeInput.focus();
                } else {
                    // Jika bukan buku, periksa apakah barcode ini milik Kartu Anggota
                    cariDataAnggotaBarcode(kode);
                }
            })
            .catch(() => {
                cariDataAnggotaBarcode(kode);
            });
    }

    function cariDataAnggotaBarcode(nomor) {
        fetch(`{{ route('petugas.peminjaman.cari-anggota') }}?nomor=${encodeURIComponent(nomor)}`)
            .then(response => response.json().then(data => ({ status: response.status, body: data })))
            .then(res => {
                if (res.status === 200 && res.body.success) {
                    const agt = res.body.anggota;
                    const selectAnggota = document.getElementById('anggota_id');
                    selectAnggota.value = agt.id;
                    selectAnggota.style.borderColor = '#059669';
                    selectAnggota.style.boxShadow = '0 0 0 3px rgba(5, 150, 105, 0.2)';
                    setTimeout(() => {
                        selectAnggota.style.borderColor = '';
                        selectAnggota.style.boxShadow = '';
                    }, 2500);

                    tampilkanPesan(`Kartu Anggota Terdeteksi: ${agt.nama_lengkap} (No: ${agt.nomor_induk})`, 'success');
                    barcodeInput.value = '';
                } else {
                    tampilkanPesan(`Barcode '${nomor}' tidak cocok dengan buku maupun kartu anggota.`, 'danger');
                }
                barcodeInput.focus();
            })
            .catch(() => {
                tampilkanPesan(`Gagal memproses kode barcode '${nomor}'.`, 'danger');
                barcodeInput.focus();
            });
    }

    function tambahKeKeranjang(buku) {
        keranjangBuku.push(buku);
        renderTabel();
    }

    function hapusDariKeranjang(index) {
        keranjangBuku.splice(index, 1);
        renderTabel();
        tampilkanPesan('Buku dihapus dari daftar pinjam.', 'info');
    }

    function renderTabel() {
        cartTableBody.innerHTML = '';
        totalBukuCount.textContent = keranjangBuku.length;

        if (keranjangBuku.length === 0) {
            cartTableBody.appendChild(emptyRow);
            return;
        }

        keranjangBuku.forEach((buku, idx) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${idx + 1}</td>
                <td>
                    ${buku.gambar_sampul 
                        ? `<img src="${buku.gambar_sampul}" style="width: 32px; height: 44px; object-fit: cover; border-radius: 4px; border: 1px solid #e2e8f0;">`
                        : `<div style="width: 32px; height: 44px; background: #f1f5f9; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #94a3b8; border: 1px solid #e2e8f0;">No img</div>`
                    }
                </td>
                <td>
                    <code style="font-size: 12px; background: #f1f5f9; padding: 2px 6px; border-radius: 4px; color: #0f172a;">${buku.kode_buku}</code>
                    <input type="hidden" name="buku_id[]" value="${buku.id}">
                </td>
                <td><strong>${buku.judul}</strong></td>
                <td style="color: #64748b;">${buku.penulis}</td>
                <td><span style="font-size: 12.5px; color: #475569;">${buku.lokasi_rak}</span></td>
                <td style="text-align: right;">
                    <button type="button" onclick="hapusDariKeranjang(${idx})" class="act-delete" style="padding: 4px 8px;">Hapus</button>
                </td>
            `;
            cartTableBody.appendChild(tr);
        });
    }

    function tampilkanPesan(pesan, tipe) {
        scanMessage.style.display = 'block';
        scanMessage.textContent = pesan;
        if (tipe === 'success') {
            scanMessage.style.color = '#059669';
        } else if (tipe === 'danger') {
            scanMessage.style.color = '#dc2626';
        } else {
            scanMessage.style.color = '#2563eb';
        }
    }

    btnToggleCamera.addEventListener('click', function() {
        if (!isCameraRunning) {
            cameraWrapper.style.display = 'block';
            this.textContent = 'Tutup Kamera';
            this.style.background = '#fef2f2';
            this.style.color = '#991b1b';
            this.style.borderColor = '#fecaca';

            html5QrCode = new Html5Qrcode("reader");
            html5QrCode.start(
                { facingMode: "environment" },
                { 
                    fps: 15, 
                    qrbox: function(viewfinderWidth, viewfinderHeight) {
                        const width = Math.floor(viewfinderWidth * 0.85);
                        const height = Math.floor(viewfinderHeight * 0.65);
                        return { width: width, height: height };
                    }
                },
                (decodedText) => {
                    prosesBarcode(decodedText);
                    html5QrCode.pause();
                    setTimeout(() => {
                        try { html5QrCode.resume(); } catch(e){}
                    }, 2000);
                },
                () => {}
            ).catch(err => {
                tampilkanPesan('Gagal mengakses kamera: ' + err, 'danger');
            });

            isCameraRunning = true;
        } else {
            if (html5QrCode) {
                html5QrCode.stop().then(() => {
                    cameraWrapper.style.display = 'none';
                    html5QrCode = null;
                });
            }
            this.textContent = 'Buka Kamera Scanner';
            this.style.background = '#ffffff';
            this.style.color = '#334155';
            this.style.borderColor = '#cbd5e1';
            isCameraRunning = false;
        }
    });

    document.getElementById('form-peminjaman').addEventListener('submit', function(e) {
        if (keranjangBuku.length === 0) {
            e.preventDefault();
            alert('Silakan scan atau tambahkan minimal 1 buku yang akan dipinjam.');
            barcodeInput.focus();
        }
    });
</script>
@endsection
