<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Kirim pesan WhatsApp otomatis ke nomor tujuan.
     * Mendukung Gateway Fonnte / WhatsApp REST API resmi,
     * serta mode Simulasi & Logging jika API token belum dikonfigurasi.
     *
     * @param string $nomorHp
     * @param string $pesan
     * @return array
     */
    public static function kirimPesan(string $nomorHp, string $pesan): array
    {
        $nomorTarget = self::formatNomorIndonesia($nomorHp);

        if (empty($nomorTarget)) {
            return [
                'success' => false,
                'message' => 'Nomor telepon tidak valid atau kosong.',
            ];
        }

        $apiToken = env('FONNTE_TOKEN', env('WA_GATEWAY_TOKEN', null));

        // JIKA TOKEN GATEWAY TERPASANG: Tembak API Fonnte / REST Gateway
        if (!empty($apiToken)) {
            try {
                $response = Http::timeout(12)
                    ->withHeaders([
                        'Authorization' => $apiToken,
                    ])
                    ->post('https://api.fonnte.com/send', [
                        'target'  => $nomorTarget,
                        'message' => $pesan,
                        'countryCode' => '62',
                    ]);

                $resJson = $response->json();

                if ($response->successful() && ($resJson['status'] ?? false)) {
                    Log::info("[WA GATEWAY] Pesan otomatis berhasil dikirim ke {$nomorTarget}");
                    return [
                        'success' => true,
                        'mode'    => 'gateway_live',
                        'message' => "Pesan berhasil dikirim langsung ke WhatsApp {$nomorTarget} via Gateway.",
                    ];
                }

                Log::warning("[WA GATEWAY FAIL] Gagal mengirim ke {$nomorTarget}: " . $response->body());
                return [
                    'success' => false,
                    'mode'    => 'gateway_live',
                    'message' => 'Gagal mengirim pesan via Gateway: ' . ($resJson['reason'] ?? 'Respons tidak berhasil'),
                ];
            } catch (\Exception $e) {
                Log::error("[WA GATEWAY ERROR] " . $e->getMessage());
                return [
                    'success' => false,
                    'mode'    => 'gateway_live',
                    'message' => 'Koneksi ke server gateway WhatsApp bermasalah: ' . $e->getMessage(),
                ];
            }
        }

        // JIKA BELUM ADA TOKEN GATEWAY: Jalankan Mode Simulasi & Logging Otomatis
        Log::info("=== [SIMULASI WHATSAPP OTOMATIS PERPUSKU] ===");
        Log::info("Tujuan : {$nomorTarget}");
        Log::info("Pesan  : {$pesan}");
        Log::info("============================================");

        return [
            'success' => true,
            'mode'    => 'simulasi',
            'message' => "Pesan WhatsApp otomatis berhasil diproses (Mode Simulasi Server).",
        ];
    }

    /**
     * Sanitasi format nomor HP Indonesia ke awalan 628...
     */
    public static function formatNomorIndonesia(?string $nomor): string
    {
        if (empty($nomor)) {
            return '';
        }

        // Hapus karakter selain angka
        $nomor = preg_replace('/[^0-9]/', '', $nomor);

        // Jika diawali '08...', ganti jadi '628...'
        if (str_starts_with($nomor, '0')) {
            $nomor = '62' . substr($nomor, 1);
        }

        // Jika diawali '8...', tambahkan '62'
        if (str_starts_with($nomor, '8')) {
            $nomor = '62' . $nomor;
        }

        return $nomor;
    }
}

