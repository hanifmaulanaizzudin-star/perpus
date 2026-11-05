<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnggotaAuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('anggota')->check()) {
            return redirect()->route('anggota.dashboard');
        }

        return view('auth.anggota.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'identitas' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $field = filter_var($credentials['identitas'], FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'nama_pengguna';

        $loginCredentials = [
            $field => $credentials['identitas'],
            'status_keanggotaan' => 'aktif',
            'password' => $credentials['password'],
        ];

        if (Auth::guard('anggota')->attempt($loginCredentials)) {
            $request->session()->regenerate();

            return redirect()->intended(route('anggota.dashboard'));
        }

        return back()
            ->withErrors([
                'identitas' => 'Username/email atau password salah, atau keanggotaan tidak aktif.',
            ])
            ->onlyInput('identitas');
    }

    public function logout(Request $request)
    {
        Auth::guard('anggota')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('anggota.login');
    }
}
