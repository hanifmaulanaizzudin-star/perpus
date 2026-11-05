<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PetugasAuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('petugas')->check()) {
            return redirect()->route('petugas.dashboard');
        }

        return view('auth.petugas.login');
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
            'status' => 'aktif',
            'password' => $credentials['password'],
        ];

        if (Auth::guard('petugas')->attempt($loginCredentials)) {
            $request->session()->regenerate();

            return redirect()->intended(route('petugas.dashboard'));
        }

        return back()
            ->withErrors([
                'identitas' => 'Username/email atau password salah, atau akun tidak aktif.',
            ])
            ->onlyInput('identitas');
    }

    public function logout(Request $request)
    {
        Auth::guard('petugas')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('petugas.login');
    }
}
