<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
   ->withMiddleware(function (Middleware $middleware): void {

    $middleware->alias([
        'petugas.aktif' => \App\Http\Middleware\EnsurePetugasAktif::class,
        'petugas.admin' => \App\Http\Middleware\EnsurePetugasAdmin::class,
        'anggota.aktif' => \App\Http\Middleware\EnsureAnggotaAktif::class,
    ]);

    $middleware->redirectGuestsTo(function ($request) {
        if ($request->is('anggota/*')) {
            return route('anggota.login');
        }

        if ($request->is('petugas/*')) {
            return route('petugas.login');
        }

        return route('anggota.login');
    });

})

    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
