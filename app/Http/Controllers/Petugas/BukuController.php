<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\KategoriBuku;
use App\Models\RakBuku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class BukuController extends Controller
{
    // 1. Tampilkan Daftar Buku (+ Pencarian)
    public function index(Request $request)
    {
        $buku = Buku::with(['kategori', 'rak'])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('judul', 'like', "%{$search}%")
                      ->orWhere('kode_buku', 'like', "%{$search}%")
                      ->orWhere('penulis', 'like', "%{$search}%")
                      ->orWhere('isbn', 'like', "%{$search}%")
                      ->orWhereHas('rak', function ($qr) use ($search) {
                          $qr->where('nama_rak', 'like', "%{$search}%")
                             ->orWhere('kode_rak', 'like', "%{$search}%");
                      });
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
        $kategori = KategoriBuku::where('status', 'aktif')->orderBy('nama_kategori')->get();
        $rak = RakBuku::where('status', 'aktif')->orderBy('kode_rak')->get();

        return view('petugas.buku.create', compact('kategori', 'rak'));
    }

    // 3. Simpan Buku Baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_buku'    => 'required|string|max:50|unique:buku,kode_buku',
            'isbn'         => 'nullable|string|max:30|unique:buku,isbn',
            'judul'        => 'required|string|max:200',
            'kategori_id'  => 'required|exists:kategori_buku,id',
            'rak_id'       => 'nullable|exists:rak_buku,id',
            'penulis'      => 'nullable|string|max:150',
            'penerbit'     => 'nullable|string|max:150',
            'tahun_terbit' => 'nullable|integer|digits:4',
            'lokasi_rak'   => 'nullable|string|max:100',
            'jumlah_stok'  => 'required|integer|min:0',
            'deskripsi'    => 'nullable|string',
            'status'       => 'required|in:tersedia,nonaktif',
            'gambar_sampul'=> 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Jika rak_id dipilih, otomatis isi teks lokasi_rak dari nama rak
        if (!empty($validated['rak_id'])) {
            $rakTerpilih = RakBuku::find($validated['rak_id']);
            if ($rakTerpilih) {
                $validated['lokasi_rak'] = $rakTerpilih->label_rak;
            }
        }

        // Upload gambar jika ada
        if ($request->hasFile('gambar_sampul')) {
            $validated['gambar_sampul'] = $request->file('gambar_sampul')->store('sampul_buku', 'public');
        }

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
        $buku->load(['kategori', 'rak']);
        return view('petugas.buku.cetak_barcode', compact('buku'));
    }

    // 4. Detail Buku
    public function show(Buku $buku)
    {
        $buku->load(['kategori', 'rak']);
        return view('petugas.buku.show', compact('buku'));
    }

    // 5. Form Edit Buku
    public function edit(Buku $buku)
    {
        $kategori = KategoriBuku::where('status', 'aktif')->orderBy('nama_kategori')->get();
        $rak = RakBuku::where('status', 'aktif')->orderBy('kode_rak')->get();

        return view('petugas.buku.edit', compact('buku', 'kategori', 'rak'));
    }

    // 6. Update Buku
    public function update(Request $request, Buku $buku)
    {
        $validated = $request->validate([
            'kode_buku'    => ['required', 'string', 'max:50', Rule::unique('buku', 'kode_buku')->ignore($buku->id)],
            'isbn'         => ['nullable', 'string', 'max:30', Rule::unique('buku', 'isbn')->ignore($buku->id)],
            'judul'        => 'required|string|max:200',
            'kategori_id'  => 'required|exists:kategori_buku,id',
            'rak_id'       => 'nullable|exists:rak_buku,id',
            'penulis'      => 'nullable|string|max:150',
            'penerbit'     => 'nullable|string|max:150',
            'tahun_terbit' => 'nullable|integer|digits:4',
            'lokasi_rak'   => 'nullable|string|max:100',
            'jumlah_stok'  => 'required|integer|min:0',
            'deskripsi'    => 'nullable|string',
            'status'       => 'required|in:tersedia,nonaktif',
            'gambar_sampul'=> 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if (!empty($validated['rak_id'])) {
            $rakTerpilih = RakBuku::find($validated['rak_id']);
            if ($rakTerpilih) {
                $validated['lokasi_rak'] = $rakTerpilih->label_rak;
            }
        }

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

    // Unduh Template CSV Import Buku
    public function downloadTemplateCsv()
    {
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="Template_Import_Buku_PerpusKu.csv"',
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['kode_buku', 'isbn', 'judul', 'kategori', 'penulis', 'penerbit', 'tahun_terbit', 'lokasi_rak', 'jumlah_stok']);
            fputcsv($handle, ['BK-001', '9786020324784', 'Laskar Pelangi', 'Novel', 'Andrea Hirata', 'Bentang Pustaka', '2005', 'Rak A-1', '5']);
            fputcsv($handle, ['BK-002', '9789793062792', 'Bumi Manusia', 'Sastra', 'Pramoedya Ananta Toer', 'Hasta Mitra', '1980', 'Rak B-2', '3']);

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Export Seluruh Katalog Buku ke CSV
    public function exportCsv()
    {
        $bukuList = Buku::with('kategori')->orderBy('id', 'asc')->get();

        $filename = 'Katalog_Buku_PerpusKu_' . date('Ymd_His') . '.csv';
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($bukuList) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['No', 'Kode Buku', 'ISBN', 'Judul Buku', 'Kategori', 'Penulis', 'Penerbit', 'Tahun Terbit', 'Lokasi Rak', 'Total Stok', 'Stok Tersedia', 'Status']);

            foreach ($bukuList as $idx => $b) {
                fputcsv($handle, [
                    $idx + 1,
                    $b->kode_buku,
                    $b->isbn ?? '-',
                    $b->judul,
                    $b->kategori->nama_kategori ?? 'Umum',
                    $b->penulis ?? '-',
                    $b->penerbit ?? '-',
                    $b->tahun_terbit ?? '-',
                    $b->lokasi_rak ?? '-',
                    $b->jumlah_stok,
                    $b->stok_tersedia,
                    $b->status,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Import Data Buku dari File CSV
    public function importCsv(Request $request)
    {
        $request->validate([
            'file_csv' => 'required|file|mimes:csv,txt|max:5120',
        ], [
            'file_csv.required' => 'Silakan pilih file CSV buku yang ingin diimpor.',
            'file_csv.mimes'    => 'File harus berformat .csv atau .txt',
        ]);

        $file = $request->file('file_csv');
        $handle = fopen($file->getRealPath(), 'r');

        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $header = fgetcsv($handle, 1000, ',');
        if (!$header || count($header) < 2) {
            rewind($handle);
            $header = fgetcsv($handle, 1000, ';');
            $delimiter = ';';
        } else {
            $delimiter = ',';
        }

        $imported = 0;
        $skipped = 0;

        while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
            // Minimal harus ada kode_buku (0) dan judul (2)
            if (empty($row[0]) || empty($row[2])) {
                $skipped++;
                continue;
            }

            $kodeBuku = trim($row[0]);
            $isbn = !empty($row[1]) ? trim($row[1]) : null;
            $judul = trim($row[2]);
            $kategoriNama = !empty($row[3]) ? trim($row[3]) : 'Umum';
            $penulis = $row[4] ?? null;
            $penerbit = $row[5] ?? null;
            $tahunTerbit = !empty($row[6]) ? (int) $row[6] : null;
            $lokasiRak = $row[7] ?? null;
            $jumlahStok = !empty($row[8]) ? max(1, (int) $row[8]) : 1;

            // Lewati jika kode buku sudah terdaftar
            if (Buku::where('kode_buku', $kodeBuku)->exists()) {
                $skipped++;
                continue;
            }

            // Cari atau buat kategori buku secara otomatis
            $kategori = KategoriBuku::firstOrCreate(
                ['nama_kategori' => $kategoriNama],
                ['deskripsi' => 'Kategori dibuat otomatis dari import CSV', 'status' => 'aktif']
            );

            Buku::create([
                'kode_buku'     => $kodeBuku,
                'isbn'          => $isbn,
                'judul'         => $judul,
                'kategori_id'   => $kategori->id,
                'penulis'       => $penulis,
                'penerbit'      => $penerbit,
                'tahun_terbit'  => $tahunTerbit,
                'lokasi_rak'    => $lokasiRak,
                'jumlah_stok'   => $jumlahStok,
                'stok_tersedia' => $jumlahStok,
                'status'        => 'tersedia',
            ]);

            $imported++;
        }

        fclose($handle);

        $msg = "Import Selesai: Berhasil menambahkan {$imported} judul buku baru.";
        if ($skipped > 0) {
            $msg .= " ({$skipped} baris dilewati karena kode buku sudah terdaftar atau tidak lengkap)";
        }

        return redirect()->route('petugas.buku.index')->with('success', $msg);
    }
}