<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\HarianController;
use App\Http\Controllers\PelatihController;
use App\Models\Transaksi;
use App\Models\PenggunaPelatih;
use App\Models\Member;
use Carbon\Carbon;

// 🖥️ DASHBOARD (Tetap manual karena rute tunggal)
Route::get('/', function () {
    $transaksihariini = Transaksi::whereDate('created_at', Carbon::today())->orderBy('created_at', 'desc')->get();
    $totalpemasukan = Transaksi::whereDate('created_at', Carbon::today())->sum('nominal');
    $totalmemberaktif = Member::count();
    $totalpelatih = PenggunaPelatih::count();

    return view('dashboard', [
        'logaktivitas' => $transaksihariini,
        'totalpemasukan' => $totalpemasukan,
        'totalmemberaktif' => $totalmemberaktif,
        'totalpelatih' => $totalpelatih,
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

