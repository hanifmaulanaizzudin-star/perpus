<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\KategoriBuku;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BukuController extends Controller
{
    // 1. Tampilkan Daftar Buku (+ Pencarian)
    public function index(Request $request)
    {
        $buku = Buku::with('kategori')
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('judul', 'like', "%{$search}%")
                      ->orWhere('kode_buku', 'like', "%{$search}%")
                      ->orWhere('penulis', 'like', "%{$search}%")
                      ->orWhere('isbn', 'like', "%{$search}%");
                });
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('petugas.buku.index', compact('buku'));
    }

    // 2. Form Tambah Buku
    public function create()
    {
        // Ambil kategori yang aktif untuk dropdown
        $kategori = KategoriBuku::where('status', 'aktif')->orderBy('nama_kategori')->get();

        return view('petugas.buku.create', compact('kategori'));
    }

    // 3. Simpan Buku Baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_buku'    => 'required|string|max:50|unique:buku,kode_buku',
            'isbn'         => 'nullable|string|max:30|unique:buku,isbn',
            'judul'        => 'required|string|max:200',
            'kategori_id'  => 'required|exists:kategori_buku,id',
            'penulis'      => 'nullable|string|max:150',
            'penerbit'     => 'nullable|string|max:150',
            'tahun_terbit' => 'nullable|integer|digits:4',
            'lokasi_rak'   => 'nullable|string|max:50',
            'jumlah_stok'  => 'required|integer|min:0',
            'deskripsi'    => 'nullable|string',
            'status'       => 'required|in:tersedia,nonaktif',
        ]);

        // Logika perpus: Saat buku baru dibuat, stok_tersedia = jumlah_stok
        $validated['stok_tersedia'] = $validated['jumlah_stok'];

        $buku = Buku::create($validated);

        return redirect()
            ->route('petugas.buku.show', $buku->id)
            ->with('success', 'Buku baru berhasil ditambahkan! Anda dapat mencetak barcode stikernya sekarang.');
    }

    // Cetak Stiker Barcode Buku
    public function cetakBarcode(Buku $buku)
    {
        $buku->load('kategori');
        return view('petugas.buku.cetak_barcode', compact('buku'));
    }

    // 4. Detail Buku
    public function show(Buku $buku)
    {
        $buku->load('kategori');
        return view('petugas.buku.show', compact('buku'));
    }

    // 5. Form Edit Buku
    public function edit(Buku $buku)
    {
        $kategori = KategoriBuku::where('status', 'aktif')->orderBy('nama_kategori')->get();
        return view('petugas.buku.edit', compact('buku', 'kategori'));
    }

    // 6. Update Buku
    public function update(Request $request, Buku $buku)
    {
        $validated = $request->validate([
            'kode_buku' => [
                'required', 'string', 'max:50',
                Rule::unique('buku', 'kode_buku')->ignore($buku->id),
            ],
            'isbn' => [
                'nullable', 'string', 'max:30',
                Rule::unique('buku', 'isbn')->ignore($buku->id),
            ],
            'judul'         => 'required|string|max:200',
            'kategori_id'   => 'required|exists:kategori_buku,id',
            'penulis'       => 'nullable|string|max:150',
            'penerbit'      => 'nullable|string|max:150',
            'tahun_terbit'  => 'nullable|integer|digits:4',
            'lokasi_rak'    => 'nullable|string|max:50',
            'jumlah_stok'   => 'required|integer|min:0',
            'deskripsi'     => 'nullable|string',
            'status'        => 'required|in:tersedia,nonaktif',
            'gambar_sampul' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Jika upload cover baru, hapus cover lama dari storage
        if ($request->hasFile('gambar_sampul')) {
            if ($buku->gambar_sampul && Storage::disk('public')->exists($buku->gambar_sampul)) {
                Storage::disk('public')->delete($buku->gambar_sampul);
            }
            $validated['gambar_sampul'] = $request->file('gambar_sampul')->store('sampul_buku', 'public');
        }

        // Sesuaikan stok tersedia jika jumlah stok berubah
        $selisih = $validated['jumlah_stok'] - $buku->jumlah_stok;
        $validated['stok_tersedia'] = max(0, $buku->stok_tersedia + $selisih);

        $buku->update($validated);

        return redirect()
            ->route('petugas.buku.index')
            ->with('success', 'Data buku berhasil diperbarui.');
    }

    // Hapus Buku
    public function destroy(Buku $buku)
    {
        if ($buku->detailPeminjaman()->exists()) {
            return back()->with('error', 'Buku tidak dapat dihapus karena memiliki riwayat peminjaman.');
        }

        // Hapus foto jika ada
        if ($buku->gambar_sampul && Storage::disk('public')->exists($buku->gambar_sampul)) {
            Storage::disk('public')->delete($buku->gambar_sampul);
        }

        $buku->delete();

        return redirect()
            ->route('petugas.buku.index')
            ->with('success', 'Buku berhasil dihapus.');
    }
}