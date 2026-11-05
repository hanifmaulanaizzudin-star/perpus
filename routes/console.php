<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Jadwal Pengiriman WhatsApp Otomatis ke Murid Terlambat: Setiap hari jam 08:00 Pagi
Schedule::command('perpus:kirim-pengingat-wa')->dailyAt('08:00');
