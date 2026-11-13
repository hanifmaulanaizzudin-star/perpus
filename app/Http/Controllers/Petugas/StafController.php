<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Petugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StafController extends Controller
{
    // 1. Daftar Akun Petugas & Hak Akses
    public function index(Request $request)
    {
        $roleFilter = $request->query('role');
        $statusFilter = $request->query('status');
        $search = trim($request->query('search', ''));

        $query = Petugas::query()->latest('id');

        if (!empty($roleFilter)) {
            $query->where('role', $roleFilter);
        }

        if (!empty($statusFilter)) {
            $query->where('status', $statusFilter);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nomor_pegawai', 'like', "%{$search}%")
                  ->orWhere('nama_pengguna', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $staf = $query->paginate(12)->withQueryString();

        $counts = [
            'total'     => Petugas::count(),
            'admin'     => Petugas::where('role', 'admin')->count(),
            'petugas'   => Petugas::where('role', 'petugas')->count(),
            'aktif'     => Petugas::where('status', 'aktif')->count(),
            'nonaktif'  => Petugas::where('status', 'nonaktif')->count(),
        ];

        return view('petugas.staf.index', compact('staf', 'counts', 'roleFilter', 'statusFilter', 'search'));
    }

    // 2. Form Tambah Akun Petugas Baru
    public function create()
    {
        // Generate nomor pegawai otomatis bila diinginkan
        $nextNum = Petugas::count() + 1;
        $nomorOtomatis = 'PTG' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

        return view('petugas.staf.create', compact('nomorOtomatis'));
    }

    // 3. Simpan Akun Petugas Baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_pegawai' => 'required|string|max:50|unique:petugas,nomor_pegawai',
            'nama_lengkap'  => 'required|string|max:100',
            'role'          => ['required', Rule::in(['admin', 'petugas'])],
            'nama_pengguna' => 'required|string|max:50|unique:petugas,nama_pengguna|alpha_dash',
            'email'         => 'required|email|max:100|unique:petugas,email',
            'kata_sandi'    => 'required|string|min:6',
            'no_telepon'    => 'nullable|string|max:20',
            'alamat'        => 'nullable|string',
            'status'        => ['required', Rule::in(['aktif', 'nonaktif'])],
        ], [
            'nomor_pegawai.unique' => 'Nomor pegawai sudah digunakan oleh staf lain.',
            'nama_pengguna.unique' => 'Username sudah digunakan.',
            'email.unique'         => 'Alamat email sudah terdaftar.',
            'kata_sandi.min'       => 'Kata sandi minimal 6 karakter.',
        ]);

        $validated['kata_sandi'] = Hash::make($validated['kata_sandi']);

        $petugas = Petugas::create($validated);

        return redirect()->route('petugas.staf.index')
            ->with('success', "Akun staf '{$petugas->nama_lengkap}' dengan hak akses {$petugas->label_role} berhasil dibuat!");
    }

    // 4. Form Edit Akun Petugas
    public function edit(Petugas $staf)
    {
        return view('petugas.staf.edit', compact('staf'));
    }

    // 5. Update Akun Petugas
    public function update(Request $request, Petugas $staf)
    {
        $validated = $request->validate([
            'nomor_pegawai' => ['required', 'string', 'max:50', Rule::unique('petugas', 'nomor_pegawai')->ignore($staf->id)],
            'nama_lengkap'  => 'required|string|max:100',
            'role'          => ['required', Rule::in(['admin', 'petugas'])],
            'nama_pengguna' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('petugas', 'nama_pengguna')->ignore($staf->id)],
            'email'         => ['required', 'email', 'max:100', Rule::unique('petugas', 'email')->ignore($staf->id)],
            'kata_sandi'    => 'nullable|string|min:6',
            'no_telepon'    => 'nullable|string|max:20',
            'alamat'        => 'nullable|string',
            'status'        => ['required', Rule::in(['aktif', 'nonaktif'])],
        ], [
            'nomor_pegawai.unique' => 'Nomor pegawai sudah digunakan oleh staf lain.',
            'nama_pengguna.unique' => 'Username sudah digunakan.',
            'email.unique'         => 'Alamat email sudah terdaftar.',
            'kata_sandi.min'       => 'Kata sandi minimal 6 karakter.',
        ]);

        // Cegah admin mengunci diri sendiri
        if ($staf->id === auth('petugas')->id()) {
            if ($validated['status'] === 'nonaktif') {
                return back()->withInput()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri saat sedang login.');
            }
            if ($validated['role'] !== 'admin') {
                return back()->withInput()->with('error', 'Anda tidak dapat mencabut hak akses Admin dari akun Anda sendiri.');
            }
        }

        if (!empty($validated['kata_sandi'])) {
            $validated['kata_sandi'] = Hash::make($validated['kata_sandi']);
        } else {
            unset($validated['kata_sandi']);
        }

        $staf->update($validated);

        return redirect()->route('petugas.staf.index')
            ->with('success', "Informasi staf '{$staf->nama_lengkap}' berhasil diperbarui!");
    }

    // 6. Hapus Akun Petugas
    public function destroy(Petugas $staf)
    {
        // Cegah menghapus diri sendiri
        if ($staf->id === auth('petugas')->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri saat sedang aktif digunakan.');
        }

        // Cek integritas riwayat transaksi (Audit Trail)
        $adaTransaksi = $staf->peminjaman()->exists() || $staf->pengembalian()->exists();
        if ($adaTransaksi) {
            return back()->with('error', "Staf '{$staf->nama_lengkap}' tidak dapat dihapus karena memiliki riwayat sirkulasi transaksi (audit trail). Anda dapat mengubah statusnya menjadi 'Nonaktif' sebagai gantinya.");
        }

        $nama = $staf->nama_lengkap;
        $staf->delete();

        return redirect()->route('petugas.staf.index')
            ->with('success', "Akun staf '{$nama}' berhasil dihapus dari sistem.");
    }
}

