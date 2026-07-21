<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman login.
     */
    public function showLogin()
    {
        // Pastikan file kamu ada di resources/views/login.blade.php
        return view('login');
    }

    /**
     * Proses autentikasi login.
     */
    public function login(Request $request)
    {
        // 1. Validasi input
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // 2. Coba proses login
        // Kita gunakan 'username' sesuai dengan kolom di database kita tadi
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Arahkan ke halaman utama/dashboard setelah berhasil login
            return redirect()->intended('/');
        }

        // 3. Jika gagal, kirim balik dengan pesan error
        return back()->with('error', 'Username atau Password salah!');
    }

    /**
     * Proses logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}