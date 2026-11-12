<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\RakBuku;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RakBukuController extends Controller
{
    /**
     * Tampilkan Daftar Rak Buku
     */
    public function index(Request $request)
    {
        $rak = RakBuku::withCount('buku')
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('kode_rak', 'like', "%{$search}%")
                      ->orWhere('nama_rak', 'like', "%{$search}%")
                      ->orWhere('lokasi_gedung', 'like', "%{$search}%");
                });
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('petugas.rak.index', compact('rak'));
    }

    /**
     * Form Tambah Rak Buku Baru
     */
    public function create()
    {
        return view('petugas.rak.create');
    }

    /**
     * Simpan Rak Buku Baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_rak'      => 'required|string|max:30|unique:rak_buku,kode_rak',
            'nama_rak'      => 'required|string|max:100',
            'lokasi_gedung' => 'nullable|string|max:100',
            'keterangan'    => 'nullable|string',
            'status'        => 'required|in:aktif,nonaktif',
        ], [
            'kode_rak.required' => 'Kode rak wajib diisi.',
            'kode_rak.unique'   => 'Kode rak sudah digunakan, gunakan kode lain.',
            'nama_rak.required' => 'Nama rak wajib diisi.',
        ]);

        $rak = RakBuku::create($validated);

        return redirect()
            ->route('petugas.rak.index')
            ->with('success', "Rak '[{$rak->kode_rak}] {$rak->nama_rak}' berhasil ditambahkan!");
    }

    /**
     * Detail Rak & Daftar Buku yang Disimpan di Rak Ini
     */
    public function show(RakBuku $rak)
    {
        $bukuDiRak = $rak->buku()
            ->with('kategori')
            ->latest('id')
            ->paginate(10);

        return view('petugas.rak.show', compact('rak', 'bukuDiRak'));
    }

    /**
     * Form Edit Rak Buku
     */
    public function edit(RakBuku $rak)
    {
        return view('petugas.rak.edit', compact('rak'));
    }

    /**
     * Perbarui Data Rak Buku
     */
    public function update(Request $request, RakBuku $rak)
    {
        $validated = $request->validate([
            'kode_rak'      => ['required', 'string', 'max:30', Rule::unique('rak_buku', 'kode_rak')->ignore($rak->id)],
            'nama_rak'      => 'required|string|max:100',
            'lokasi_gedung' => 'nullable|string|max:100',
            'keterangan'    => 'nullable|string',
            'status'        => 'required|in:aktif,nonaktif',
        ]);

        $rak->update($validated);

        return redirect()
            ->route('petugas.rak.index')
            ->with('success', "Data rak '{$rak->nama_rak}' berhasil diperbarui.");
    }

    /**
     * Hapus Rak Buku (Proteksi Relasi)
     */
    public function destroy(RakBuku $rak)
    {
        $jumlahBuku = $rak->buku()->count();
        if ($jumlahBuku > 0) {
            return back()->with(
                'error',
                "Gagal menghapus: Rak '{$rak->nama_rak}' masih menampung {$jumlahBuku} koleksi buku fisik. Harap pindahkan lokasi buku terlebih dahulu ke rak lain."
            );
        }

        $rak->delete();

        return redirect()
            ->route('petugas.rak.index')
            ->with('success', "Rak '{$rak->nama_rak}' berhasil dihapus.");
    }
}

