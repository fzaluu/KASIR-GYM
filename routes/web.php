<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\HarianController;
use App\Http\Controllers\PelatihController;
use App\Models\Transaksi;
use App\Models\PenggunaPelatih;
use App\Models\Member;
use App\Models\Pelatih; // Memanggil model pelatih agar data master singkron
use Carbon\Carbon;

// 🖥️ DASHBOARD UTAMA (Pusat Kendali Kasir Gym)
Route::get('/', function () {
    // 1. Mengambil data log aktivitas khusus transaksi hari ini saja
    $transaksihariini = Transaksi::whereDate('created_at', Carbon::today())->orderBy('created_at', 'desc')->get();
    
    // 2. Menghitung total uang masuk dari kasir hari ini
    $totalpemasukan = Transaksi::whereDate('created_at', Carbon::today())->sum('nominal');
    
    // 3. Menghitung jumlah total seluruh member tetap gym
    $totalmemberaktif = Member::count();
    
    // 4. Menghitung jumlah total pelatih yang terdaftar di sistem
    $totalpelatih = Pelatih::count();

    // 5. SUNTIKAN DATA RELASI: Mengambil master data member untuk pilihan dropdown modal
    $daftarMember = Member::orderBy('nama_member', 'asc')->get();

    // 6. SUNTIKAN DATA RELASI: Mengambil master data pelatih aktif untuk pilihan dropdown sewa PT
    $daftarPelatih = Pelatih::where('status_hadir', 'hadir')->orderBy('nama_pelatih', 'asc')->get();

    // Mengirimkan seluruh variabel pendukung ke file view dashboard.blade.php
    return view('dashboard', [
        'logaktivitas'     => $transaksihariini,
        'totalpemasukan'   => $totalpemasukan,
        'totalmemberaktif' => $totalmemberaktif,
        'totalpelatih'     => $totalpelatih,
        'daftarMember'     => $daftarMember,  // Jembatan data dropdown member
        'daftarPelatih'    => $daftarPelatih, // Jembatan data dropdown pelatih
    ]);
});

// 🏋️ MEMBER (Gunakan Resource + Tambahan Custom Route untuk Check-in & Perpanjang)
Route::post('/member/checkin/{id}', [MemberController::class, 'checkin'])->name('member.checkin');
Route::post('/member/perpanjang/{id}', [MemberController::class, 'perpanjang'])->name('member.perpanjang');
Route::resource('member', MemberController::class)->parameters(['member' => 'id']);

// 📅 PENGUNJUNG HARIAN (Resource - Hanya fungsi yang dipakai Modal)
Route::resource('harian', HarianController::class)->only(['index', 'store', 'update', 'destroy'])->parameters(['harian' => 'id']);

// 👔 DATA PELATIH (Resource - Hanya fungsi yang dipakai Modal)
Route::resource('pelatih', PelatihController::class)->only(['index', 'store', 'update', 'destroy'])->parameters(['pelatih' => 'id']);
Route::post('/pelatih/pengguna', [PelatihController::class, 'storePengguna'])->name('pelatih.storePengguna');
Route::delete('/pelatih/pengguna/{id}', [PelatihController::class, 'destroyPengguna'])->name('pelatih.destroyPengguna');

// 💵 CATATAN TRANSAKSI (Resource - Hanya melihat list & simpan)
Route::resource('transaksi', TransaksiController::class)->only(['index', 'store']);