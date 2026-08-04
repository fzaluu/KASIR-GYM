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
Route::middleware('guest')->get('/login', [AuthController::class, 'showLogin'])->name('login');

Route::middleware(['guest', 'throttle:5,1'])
    ->post('/login', [AuthController::class, 'login'])
    ->name('login.proses');

// --- Rute Terlindungi ---
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // --- Rute Pengaturan Harga ---
    Route::get('/harga', [HargaController::class, 'index'])->name('harga.index');

    // --- Rute Check-in QR Code (Pastikan di atas resource member) ---
    Route::get('/checkin-scanner', function () {
        return view('checkin');
    })->name('checkin.scanner');

    Route::post('/proses-checkin-qr', [MemberController::class, 'prosesCheckinQr']);

    // --- Modul Member ---
    Route::post('/member/checkin/{id}', [MemberController::class, 'checkin'])->name('member.checkin');
    Route::post('/member/perpanjang/{id}', [MemberController::class, 'perpanjang'])->name('member.perpanjang');
    Route::resource('member', MemberController::class)->except(['destroy'])->parameters(['member' => 'id']);

    // --- Modul Lainnya ---
   // --- Modul Lainnya ---
    Route::resource('harian', HarianController::class)->only(['index', 'store', 'update', 'destroy'])->parameters(['harian' => 'id']);
    
    // Perbarui rute pelatih (hapus update dari resource publik)
    Route::resource('pelatih', PelatihController::class)->only(['index', 'store'])->parameters(['pelatih' => 'id']);
    Route::patch('/pelatih/{id}/absen', [PelatihController::class, 'absen'])->name('pelatih.absen');
    
    Route::post('/pelatih/pengguna', [PelatihController::class, 'storePengguna'])->name('pelatih.storePengguna');
    Route::resource('transaksi', TransaksiController::class)->only(['index', 'store']);

    Route::middleware('admin')->group(function () {
        Route::post('/harga/update', [HargaController::class, 'updateAll'])->name('harga.update_all');
        Route::delete('/member/{id}', [MemberController::class, 'destroy'])->name('member.destroy');
        
        // Tambahkan rute update penuh pelatih khusus admin
        Route::put('/pelatih/{id}', [PelatihController::class, 'update'])->name('pelatih.update');
        
        Route::delete('/pelatih/{id}', [PelatihController::class, 'destroy'])->name('pelatih.destroy');
        Route::delete('/pelatih/pengguna/{id}', [PelatihController::class, 'destroyPengguna'])->name('pelatih.destroyPengguna');
        Route::get('/transaksi/export', [TransaksiController::class, 'export'])->name('transaksi.export');
    });
});