<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\KategoriBuku;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KategoriBukuController extends Controller
{
    // Menampilkan daftar kategori
    public function index(Request $request)
    {
        $kategori = KategoriBuku::query()
            ->when($request->search, function ($query, $search) {
                $query->where('nama_kategori', 'like', "%{$search}%");
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('petugas.kategori.index', compact('kategori'));
    }

    // Form tambah kategori
    public function create()
    {
        return view('petugas.kategori.create');
    }

    // Menyimpan kategori
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:100|unique:kategori_buku,nama_kategori',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        KategoriBuku::create($validated);

        return redirect()
            ->route('petugas.kategori.index')
            ->with('success', 'Kategori buku berhasil ditambahkan.');
    }

    // Detail kategori
    public function show(KategoriBuku $kategori)
    {
        return view('petugas.kategori.show', compact('kategori'));
    }

    // Form edit kategori
    public function edit(KategoriBuku $kategori)
    {
        return view('petugas.kategori.edit', compact('kategori'));
    }

    // Memperbarui kategori
    public function update(Request $request, KategoriBuku $kategori)
    {
        $validated = $request->validate([
            'nama_kategori' => [
                'required',
                'string',
                'max:100',
                Rule::unique('kategori_buku', 'nama_kategori')
                    ->ignore($kategori->id),
            ],
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $kategori->update($validated);

        return redirect()
            ->route('petugas.kategori.index')
            ->with('success', 'Kategori buku berhasil diperbarui.');
    }

    // Menghapus kategori
    public function destroy(KategoriBuku $kategori)
    {
        if ($kategori->buku()->exists()) {
            return back()->with(
                'error',
                'Kategori tidak dapat dihapus karena masih digunakan oleh buku.'
            );
        }

        $kategori->delete();

        return redirect()
            ->route('petugas.kategori.index')
            ->with('success', 'Kategori buku berhasil dihapus.');
    }
}
