<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\ActivityLogger;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('login'); 
    }
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Cek status user aktif atau nonaktif
            if ($user->status === 'nonaktif') {
                Auth::logout();
                ActivityLogger::log('login_gagal', "Percobaan login gagal: akun nonaktif untuk username {$credentials['username']}", null);
                return back()->with('error', 'Akun Anda tidak aktif. Hubungi admin untuk informasi lebih lanjut.');
            }

            // Update last login
            $user->update(['last_login_at' => now()]);
            
            $request->session()->regenerate();
            ActivityLogger::log('login', "Login berhasil ke dalam sistem", $user->id);

            return redirect()->intended('/');
        }

        ActivityLogger::log('login_gagal', "Percobaan login gagal untuk username {$credentials['username']}", null);
        return back()->with('error', 'Username atau Password salah!');
    }

    public function logout(Request $request)
    {
        $userId = auth()->id();
        ActivityLogger::log('logout', "Keluar dari sistem", $userId);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}