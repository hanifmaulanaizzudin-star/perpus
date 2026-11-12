<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\PengaturanPerpustakaan;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    /**
     * Tampilkan Halaman Pengaturan Perpustakaan
     */
    public function index()
    {
        $pengaturan = PengaturanPerpustakaan::first();

        if (!$pengaturan) {
            $pengaturan = PengaturanPerpustakaan::create([
                'nama_perpustakaan'         => 'PerpusKu Digital School',
                'alamat'                    => 'Jl. Pendidikan No. 45, Gedung Perpustakaan Pusat',
                'maksimal_buku_per_anggota' => 3,
                'lama_pinjam_hari'          => 7,
                'denda_per_hari'            => 1000,
                'gunakan_denda'             => true,
            ]);
        }

        return view('petugas.pengaturan.index', compact('pengaturan'));
    }

    /**
     * Simpan Perubahan Pengaturan Perpustakaan
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'nama_perpustakaan'         => 'required|string|max:150',
            'alamat'                    => 'nullable|string',
            'maksimal_buku_per_anggota' => 'required|integer|min:1|max:30',
            'lama_pinjam_hari'          => 'required|integer|min:1|max:90',
            'denda_per_hari'            => 'required|numeric|min:0',
            'gunakan_denda'             => 'nullable|boolean',
        ], [
            'nama_perpustakaan.required' => 'Nama perpustakaan wajib diisi.',
            'maksimal_buku_per_anggota.min' => 'Batas pinjam minimal 1 buku.',
            'lama_pinjam_hari.min' => 'Durasi pinjam minimal 1 hari.',
            'denda_per_hari.min' => 'Tarif denda tidak boleh negatif.',
        ]);

        $validated['gunakan_denda'] = $request->has('gunakan_denda');

        $pengaturan = PengaturanPerpustakaan::first();

        if ($pengaturan) {
            $pengaturan->update($validated);
        } else {
            PengaturanPerpustakaan::create($validated);
        }

        return redirect()
            ->route('petugas.pengaturan.index')
            ->with('success', 'Pengaturan operasional perpustakaan berhasil disimpan!');
    }
}

