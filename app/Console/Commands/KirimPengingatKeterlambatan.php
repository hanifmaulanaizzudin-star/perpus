<?php

namespace App\Console\Commands;

use App\Models\Peminjaman;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class KirimPengingatKeterlambatan extends Command
{
    /**
     * Nama dan signature command di terminal.
     */
    protected $signature = 'perpus:kirim-pengingat-wa';

    /**
     * Deskripsi command.
     */
    protected $description = 'Otomatis cek buku yang jatuh tempo & kirim pesan WhatsApp pengingat keterlambatan ke murid';

    public function handle()
    {
        $this->info("=== MEMULAI PENGECEKAN KETERLAMBATAN & PENGIRIMAN WA OTOMATIS ===");

        $today = Carbon::today()->startOfDay();

        // 1. Otomatis perbarui status pinjaman yang sudah lewat jatuh tempo menjadi 'terlambat'
        $updatedCount = Peminjaman::where('status', 'dipinjam')
            ->whereDate('tanggal_jatuh_tempo', '<', $today)
            ->update(['status' => 'terlambat']);

        if ($updatedCount > 0) {
            $this->info("Menemukan {$updatedCount} transaksi baru yang telah melewati jatuh tempo -> Status diubah ke 'terlambat'.");
        }

        // 2. Ambil semua peminjaman yang berstatus 'terlambat'
        $pinjamanTerlambat = Peminjaman::with(['anggota', 'detailPeminjaman.buku'])
            ->where('status', 'terlambat')
            ->get();

        if ($pinjamanTerlambat->isEmpty()) {
            $this->info("Tidak ada transaksi peminjaman yang terlambat hari ini. Semua buku tertib!");
            return Command::SUCCESS;
        }

        $this->info("Total transaksi terlambat yang perlu dikirimi pengingat: " . $pinjamanTerlambat->count());

        $terkirim = 0;
        $gagal = 0;

        foreach ($pinjamanTerlambat as $item) {
            $anggota = $item->anggota;

            if (!$anggota || empty($anggota->no_telepon)) {
                $this->warn("[-] Lewati transaksi {$item->kode_peminjaman}: Anggota tidak memiliki nomor HP/WA.");
                $gagal++;
                continue;
            }

            $bukuList = $item->detailPeminjaman->map(function ($d) {
                return $d->buku->judul ?? 'Buku Perpustakaan';
            })->implode(', ');

            $jatuhTempo = Carbon::parse($item->tanggal_jatuh_tempo)->translatedFormat('d F Y');
            $hariTelat = $today->diffInDays(Carbon::parse($item->tanggal_jatuh_tempo)->startOfDay());

            // Format Pesan WhatsApp Otomatis
            $pesan = "Halo {$anggota->nama_lengkap} (NISN: {$anggota->nomor_induk}),\n\n"
                   . "Kami dari *Perpustakaan PerpusKu* ingin menginformasikan bahwa pinjaman buku:\n"
                   . "📖 *{$bukuList}*\n"
                   . "🔖 Kode Pinjam: *{$item->kode_peminjaman}*\n"
                   . "⚠️ *STATUS: TERLAMBAT ({$hariTelat} Hari)*\n"
                   . "📅 Jatuh Tempo: {$jatuhTempo}\n\n"
                   . "Mohon untuk segera mengembalikan buku tersebut ke meja perpustakaan untuk menghindari akumulasi denda harian dan agar keanggotaan kamu tetap aktif.\n\n"
                   . "Terima kasih atas kerja samanya! 🙏\n"
                   . "_Pesan otomatis dari Sistem PerpusKu Digital_";

            // Kirim pesan otomatis via WhatsAppService
            $res = WhatsAppService::kirimPesan($anggota->no_telepon, $pesan);

            if ($res['success']) {
                $this->info("[+] Berhasil kirim WA ke {$anggota->nama_lengkap} ({$anggota->no_telepon}) [{$res['mode']}]");
                $terkirim++;
            } else {
                $this->error("[-] Gagal kirim WA ke {$anggota->nama_lengkap}: {$res['message']}");
                $gagal++;
            }
        }

        $this->info("Selesai! Berhasil terkirim: {$terkirim}, Gagal/Tanpa No HP: {$gagal}.");

        return Command::SUCCESS;
    }
}

