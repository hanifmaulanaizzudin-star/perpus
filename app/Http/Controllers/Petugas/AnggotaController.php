<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AnggotaController extends Controller
{
    public function index(Request $request)
    {
        $anggota = Anggota::query()
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama_lengkap', 'like', "%{$search}%")
                      ->orWhere('nomor_induk', 'like', "%{$search}%")
                      ->orWhere('nama_pengguna', 'like', "%{$search}%");
                });
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('petugas.anggota.index', compact('anggota'));
    }

    public function create()
    {
        return view('petugas.anggota.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_induk' => 'required|string|max:30|unique:anggota,nomor_induk',
            'nama_lengkap' => 'required|string|max:150',
            'nama_pengguna' => 'required|string|max:50|unique:anggota,nama_pengguna',
            'email' => 'nullable|email|max:150|unique:anggota,email',
            'kata_sandi' => 'required|string|min:8',
            'kelas' => 'nullable|string|max:30',
            'jurusan' => 'nullable|string|max:100',
            'no_telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'tanggal_daftar' => 'required|date',
            'status_keanggotaan' => 'required|in:aktif,nonaktif,ditangguhkan',
        ]);

        Anggota::create($validated);

        return redirect()
            ->route('petugas.anggota.index')
            ->with('success', 'Anggota berhasil ditambahkan.');
    }

    public function show(Anggota $anggota)
    {
        $anggota->load([
            'peminjaman' => function ($query) {
                $query->with(['detailPeminjaman.buku', 'petugas'])->latest('id');
            }
        ]);

        $totalPinjaman = $anggota->peminjaman->count();
        $pinjamanAktif = $anggota->peminjaman->whereIn('status', ['dipinjam', 'terlambat']);
        $totalBukuDipinjam = $pinjamanAktif->sum(function ($p) {
            return $p->detailPeminjaman->sum('jumlah_pinjam');
        });
        $pinjamanTerlambat = $anggota->peminjaman->where('status', 'terlambat');
        $bebasPustaka = ($pinjamanAktif->count() === 0);

        return view('petugas.anggota.show', compact(
            'anggota',
            'totalPinjaman',
            'totalBukuDipinjam',
            'pinjamanTerlambat',
            'bebasPustaka'
        ));
    }

    public function cetakKartu(Anggota $anggota)
    {
        return view('petugas.anggota.cetak_kartu', compact('anggota'));
    }

    public function edit(Anggota $anggota)
    {
        return view('petugas.anggota.edit', compact('anggota'));
    }

    public function update(Request $request, Anggota $anggota)
    {
        $validated = $request->validate([
            'nomor_induk' => [
                'required', 'string', 'max:30',
                Rule::unique('anggota', 'nomor_induk')->ignore($anggota->id),
            ],
            'nama_lengkap' => 'required|string|max:150',
            'nama_pengguna' => [
                'required', 'string', 'max:50',
                Rule::unique('anggota', 'nama_pengguna')->ignore($anggota->id),
            ],
            'email' => [
                'nullable', 'email', 'max:150',
                Rule::unique('anggota', 'email')->ignore($anggota->id),
            ],
            'kelas' => 'nullable|string|max:30',
            'jurusan' => 'nullable|string|max:100',
            'no_telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'status_keanggotaan' => 'required|in:aktif,nonaktif,ditangguhkan',
        ]);

        if ($request->filled('kata_sandi')) {
            $request->validate([
                'kata_sandi' => 'string|min:8',
            ]);

            $validated['kata_sandi'] = Hash::make($request->kata_sandi);
        }

        // Proteksi Bebas Pustaka: Cek apakah anggota masih meminjam buku sebelum dinonaktifkan / ditangguhkan
        if ($request->status_keanggotaan !== 'aktif') {
            $pinjamanAktif = $anggota->peminjaman()
                ->whereIn('status', ['dipinjam', 'terlambat'])
                ->count();

            if ($pinjamanAktif > 0) {
                return back()
                    ->withInput()
                    ->with('error', "Gagal mengubah status: Anggota '{$anggota->nama_lengkap}' masih memiliki {$pinjamanAktif} transaksi peminjaman buku yang belum dikembalikan! Selesaikan pengembalian buku terlebih dahulu sebelum mengubah status menjadi nonaktif atau ditangguhkan.");
            }
        }

        $anggota->update($validated);

        return redirect()
            ->route('petugas.anggota.index')
            ->with('success', 'Data anggota berhasil diperbarui.');
    }

    public function destroy(Anggota $anggota)
    {
        if ($anggota->peminjaman()->exists()) {
            return back()->with(
                'error',
                'Anggota tidak dapat dihapus karena memiliki riwayat peminjaman.'
            );
        }

        $anggota->delete();

        return redirect()
            ->route('petugas.anggota.index')
            ->with('success', 'Anggota berhasil dihapus.');
    }
}
