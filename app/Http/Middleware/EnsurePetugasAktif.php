<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsurePetugasAktif
{
    public function handle(Request $request, Closure $next): Response
    {
        $guard = Auth::guard('petugas');

        if (!$guard->check()) {
            return redirect('/petugas/login');
        }

        if ($guard->user()->status !== 'aktif') {
            $guard->logout();

            return redirect('/petugas/login')
                ->withErrors([
                    'akun' => 'Akun petugas tidak aktif. Silakan hubungi administrator.',
                ]);
        }

        return $next($request);
    }
}
