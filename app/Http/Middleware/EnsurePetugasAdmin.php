<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsurePetugasAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $guard = Auth::guard('petugas');

        if (!$guard->check()) {
            return redirect()->route('petugas.login');
        }

        if (!$guard->user()->isAdmin()) {
            abort(403, 'Akses ditolak: Menu ini hanya dapat diakses oleh Administrator / Kepala Perpustakaan.');
        }

        return $next($request);
    }
}

