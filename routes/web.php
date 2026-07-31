<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController; 
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\HarianController;
use App\Http\Controllers\PelatihController;
use App\Http\Controllers\HargaController;

// --- Rute untuk Tamu ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.proses');
});

// --- Rute Terlindungi ---
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // --- Rute Pengaturan Harga ---
    Route::get('/harga', [HargaController::class, 'index'])->name('harga.index');
    Route::post('/harga/update', [HargaController::class, 'updateAll'])->name('harga.update_all');

    // --- Rute Check-in QR Code (Pastikan di atas resource member) ---
    Route::get('/checkin-scanner', function () {
        return view('checkin');
    })->name('checkin.scanner');
    
    Route::post('/proses-checkin-qr', [MemberController::class, 'prosesCheckinQr']);

    // --- Modul Member ---
    Route::post('/member/checkin/{id}', [MemberController::class, 'checkin'])->name('member.checkin');
    Route::post('/member/perpanjang/{id}', [MemberController::class, 'perpanjang'])->name('member.perpanjang');
    Route::resource('member', MemberController::class)->parameters(['member' => 'id']);

    // --- Modul Lainnya ---
    Route::resource('harian', HarianController::class)->only(['index', 'store', 'update', 'destroy'])->parameters(['harian' => 'id']);
    Route::resource('pelatih', PelatihController::class)->only(['index', 'store', 'update', 'destroy'])->parameters(['pelatih' => 'id']);
    Route::post('/pelatih/pengguna', [PelatihController::class, 'storePengguna'])->name('pelatih.storePengguna');
    Route::delete('/pelatih/pengguna/{id}', [PelatihController::class, 'destroyPengguna'])->name('pelatih.destroyPengguna');
    Route::get('/transaksi/export', [TransaksiController::class, 'export'])->name('transaksi.export');
    Route::resource('transaksi', TransaksiController::class)->only(['index', 'store']);
});