<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController; 
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\HarianController;
use App\Http\Controllers\PelatihController;
use App\Http\Controllers\HargaController;
use App\Http\Controllers\UserController; 
use App\Http\Controllers\ActivityLogController;

// --- Rute untuk Tamu ---
Route::middleware('guest')->get('/login', [AuthController::class, 'showLogin'])->name('login');

Route::middleware(['guest', 'throttle:5,1'])
    ->post('/login', [AuthController::class, 'login'])
    ->name('login.proses');

// --- Rute Terlindungi ---
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // --- Rute Check-in QR Code ---
    Route::get('/checkin-scanner', function () {
        return view('checkin');
    })->name('checkin.scanner');

    Route::post('/proses-checkin-qr', [MemberController::class, 'prosesCheckinQr']);

    // --- Modul Member ---
    Route::post('/member/checkin/{id}', [MemberController::class, 'checkin'])->name('member.checkin');
    Route::post('/member/perpanjang/{id}', [MemberController::class, 'perpanjang'])->name('member.perpanjang');
    Route::resource('member', MemberController::class)->except(['destroy'])->parameters(['member' => 'id']);

    // --- Modul Lainnya ---
    Route::resource('harian', HarianController::class)->only(['index', 'store', 'update', 'destroy'])->parameters(['harian' => 'id']);
    
    // Pelatih
    Route::resource('pelatih', PelatihController::class)->only(['index', 'store'])->parameters(['pelatih' => 'id']);
    Route::patch('/pelatih/{id}/absen', [PelatihController::class, 'absen'])->name('pelatih.absen');
    Route::post('/pelatih/pengguna', [PelatihController::class, 'storePengguna'])->name('pelatih.storePengguna');
    
    // TRANSAKSI: Hanya method store() di luar agar kasir bisa tetap input transaksi
    Route::post('/transaksi', [TransaksiController::class, 'store'])->name('transaksi.store');

    // --- RUTE KHUSUS ADMIN ---
    Route::middleware('admin')->group(function () {
        // Halaman Harga (Index & Update) khusus Admin
        Route::get('/harga', [HargaController::class, 'index'])->name('harga.index');
        Route::post('/harga/update', [HargaController::class, 'updateAll'])->name('harga.update_all');
        
        // Riwayat Transaksi (Index) khusus Admin
        Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
        
        Route::delete('/member/{id}', [MemberController::class, 'destroy'])->name('member.destroy');
        
        // Pelatih Admin
        Route::put('/pelatih/{id}', [PelatihController::class, 'update'])->name('pelatih.update');
        Route::delete('/pelatih/{id}', [PelatihController::class, 'destroy'])->name('pelatih.destroy');
        Route::delete('/pelatih/pengguna/{id}', [PelatihController::class, 'destroyPengguna'])->name('pelatih.destroyPengguna');
        
        Route::get('/transaksi/export', [TransaksiController::class, 'export'])->name('transaksi.export');

        // Kelola User & Activity Log
        Route::resource('users', UserController::class)->except(['show']);
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        Route::get('/users/transaksi-riwayat', [UserController::class, 'transaksiRiwayat'])->name('users.transaksi-riwayat');
        Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');
    });
});