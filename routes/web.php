<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\HarianController;
use App\Http\Controllers\PelatihController;
use App\Models\Transaksi;
use App\Models\PenggunaPelatih;
use App\Models\Member;
use App\Models\Pelatih;
use Carbon\Carbon;

// 🖥️ DASHBOARD UTAMA (Pusat Kendali Kasir Gym)
Route::get('/', function () {
    // Logika Otomatis Filter Hari Ini jika belum ada inputan pencarian
    $hariIni  = Carbon::today()->toDateString();
    
    // 1. Log aktivitas khusus transaksi yang masuk pada hari ini saja
    $transaksihariini = Transaksi::whereDate('created_at', $hariIni)->orderBy('created_at', 'desc')->get();
    
    // 2. Total uang masuk hari ini
    $totalpemasukan = Transaksi::whereDate('created_at', $hariIni)->sum('nominal');
    
    // 3. Counter statistik dashboard
    $totalmemberaktif = Member::count();
    $totalpelatih     = Pelatih::count();

    // 4. DROPDOWN SELEKTIF (Solusi Revisi Poin 6 & 11)
    // Dropdown Check-in & Perpanjang: Hanya memunculkan data dari master tabel members
    $daftarMember = Member::orderBy('nama_member', 'asc')->get();

    // Dropdown Sewa PT: Hanya memunculkan nama pelanggan yang sudah terdaftar menyewa PT sebelumnya
    $daftarPelangganPT = PenggunaPelatih::orderBy('nama_pengguna', 'asc')->get();

    // Dropdown Pelatih: Memunculkan semua pelatih tanpa filter status_hadir (Solusi Revisi Poin 3 & 7)
    $daftarPelatih = Pelatih::orderBy('nama_pelatih', 'asc')->get();

    return view('dashboard', [
        'logaktivitas'      => $transaksihariini,
        'totalpemasukan'    => $totalpemasukan,
        'totalmemberaktif'  => $totalmemberaktif,
        'totalpelatih'      => $totalpelatih,
        'daftarMember'      => $daftarMember,
        'daftarPelangganPT' => $daftarPelangganPT, // Data khusus dropdown pelanggan sewa PT
        'daftarPelatih'     => $daftarPelatih,
    ]);
});

// 🏋️ MEMBER (Resource + Custom Route)
Route::post('/member/checkin/{id}', [MemberController::class, 'checkin'])->name('member.checkin');
Route::post('/member/perpanjang/{id}', [MemberController::class, 'perpanjang'])->name('member.perpanjang');
Route::resource('member', MemberController::class)->parameters(['member' => 'id']);

// 📅 PENGUNJUNG HARIAN
Route::resource('harian', HarianController::class)->only(['index', 'store', 'update', 'destroy'])->parameters(['harian' => 'id']);

// 👔 DATA PELATIH
Route::resource('pelatih', PelatihController::class)->only(['index', 'store', 'update', 'destroy'])->parameters(['pelatih' => 'id']);
Route::post('/pelatih/pengguna', [PelatihController::class, 'storePengguna'])->name('pelatih.storePengguna');
Route::delete('/pelatih/pengguna/{id}', [PelatihController::class, 'destroyPengguna'])->name('pelatih.destroyPengguna');

// 💵 CATATAN TRANSAKSI
Route::resource('transaksi', TransaksiController::class)->only(['index', 'store']);