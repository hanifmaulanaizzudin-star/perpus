<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAnggotaAktif
{
    public function handle(Request $request, Closure $next): Response
    {
        $guard = Auth::guard('anggota');

        if (!$guard->check()) {
            return redirect('/anggota/login');
        }

        if ($guard->user()->status_keanggotaan !== 'aktif') {
            $guard->logout();

            return redirect('/anggota/login')
                ->withErrors([
                    'akun' => 'Keanggotaan tidak aktif. Silakan hubungi petugas perpustakaan.',
                ]);
        }

        return $next($request);
    }
}
