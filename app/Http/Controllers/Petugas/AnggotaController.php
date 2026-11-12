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

    // Unduh Template CSV Import Siswa
    public function downloadTemplateCsv()
    {
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="Template_Import_Anggota_PerpusKu.csv"',
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            // Tulis UTF-8 BOM agar rapi di Microsoft Excel
            fputs($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['nomor_induk', 'nama_lengkap', 'nama_pengguna', 'kelas', 'jurusan', 'no_telepon', 'alamat']);
            fputcsv($handle, ['12345', 'Ahmad Dahlan', 'ahmad123', '10 RPL 1', 'Rekayasa Perangkat Lunak', '081234567890', 'Jl. Merdeka No. 12']);
            fputcsv($handle, ['12346', 'Siti Rahmawati', 'siti_rahma', '11 TKJ 2', 'Teknik Komputer Jaringan', '085712345678', 'Jl. Sudirman No. 45']);

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Export Seluruh Data Anggota ke CSV
    public function exportCsv()
    {
        $anggotaList = Anggota::orderBy('id', 'asc')->get();

        $filename = 'Data_Anggota_PerpusKu_' . date('Ymd_His') . '.csv';
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($anggotaList) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['No', 'Nomor Induk', 'Nama Lengkap', 'Nama Pengguna', 'Kelas', 'Jurusan', 'No Telepon', 'Status Keanggotaan', 'Tanggal Terdaftar']);

            foreach ($anggotaList as $idx => $a) {
                fputcsv($handle, [
                    $idx + 1,
                    $a->nomor_induk,
                    $a->nama_lengkap,
                    $a->nama_pengguna,
                    $a->kelas ?? '-',
                    $a->jurusan ?? '-',
                    $a->no_telepon ?? '-',
                    $a->status_keanggotaan,
                    $a->tanggal_daftar ? $a->tanggal_daftar->format('d/m/Y') : '-',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Import Data Siswa dari File CSV
    public function importCsv(Request $request)
    {
        $request->validate([
            'file_csv' => 'required|file|mimes:csv,txt|max:5120',
        ], [
            'file_csv.required' => 'Silakan pilih file CSV yang ingin diimpor.',
            'file_csv.mimes'    => 'File harus berformat .csv atau .txt',
        ]);

        $file = $request->file('file_csv');
        $handle = fopen($file->getRealPath(), 'r');

        // Lewati UTF-8 BOM jika ada
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        // Baca Baris Header
        $header = fgetcsv($handle, 1000, ',');
        if (!$header || count($header) < 2) {
            // Coba delimiter titik koma ';'
            rewind($handle);
            $header = fgetcsv($handle, 1000, ';');
            $delimiter = ';';
        } else {
            $delimiter = ',';
        }

        $imported = 0;
        $skipped = 0;
        $now = now()->toDateString();

        while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
            // Minimal ada nomor_induk dan nama_lengkap
            if (empty($row[0]) || empty($row[1])) {
                $skipped++;
                continue;
            }

            $nomorInduk = trim($row[0]);
            $namaLengkap = trim($row[1]);
            $namaPengguna = !empty($row[2]) ? trim($row[2]) : ('user_' . $nomorInduk);
            $kelas = $row[3] ?? null;
            $jurusan = $row[4] ?? null;
            $noTelepon = $row[5] ?? null;
            $alamat = $row[6] ?? null;

            // Lewati jika nomor induk atau username sudah terdaftar
            if (Anggota::where('nomor_induk', $nomorInduk)->orWhere('nama_pengguna', $namaPengguna)->exists()) {
                $skipped++;
                continue;
            }

            Anggota::create([
                'nomor_induk'        => $nomorInduk,
                'nama_lengkap'       => $namaLengkap,
                'nama_pengguna'      => $namaPengguna,
                'kata_sandi'         => $nomorInduk, // Default password = nomor induk (otomatis ter-hash)
                'kelas'              => $kelas,
                'jurusan'            => $jurusan,
                'no_telepon'         => $noTelepon,
                'alamat'             => $alamat,
                'status_keanggotaan' => 'aktif',
                'tanggal_daftar'     => $now,
            ]);

            $imported++;
        }

        fclose($handle);

        $msg = "Import Selesai: Berhasil menambahkan {$imported} anggota baru.";
        if ($skipped > 0) {
            $msg .= " ({$skipped} baris dilewati karena sudah ada atau data tidak lengkap)";
        }

        return redirect()->route('petugas.anggota.index')->with('success', $msg);
    }
}
