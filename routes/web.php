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

Route::get('/', function () {
    $hariIni  = Carbon::today()->toDateString();
    
    $transaksihariini = Transaksi::whereDate('created_at', $hariIni)->orderBy('created_at', 'desc')->get();
    
    $totalpemasukan = Transaksi::whereDate('created_at', $hariIni)->sum('nominal');
    
    $totalmemberaktif = Member::whereDate('tanggal_kadaluarsa', '>=', $hariIni)->count();
    $totalpelatih     = Pelatih::count();

    $daftarMemberAktif = Member::whereDate('tanggal_kadaluarsa', '>=', $hariIni)
        ->orderBy('nama_member', 'asc')
        ->get();

    $daftarMemberSemua = Member::orderBy('nama_member', 'asc')->get();

    $daftarPelangganPT = PenggunaPelatih::orderBy('nama_pengguna', 'asc')->get();

    $daftarPelatih = Pelatih::orderBy('nama_pelatih', 'asc')->get();

    return view('dashboard', [
        'logaktivitas'      => $transaksihariini,
        'totalpemasukan'    => $totalpemasukan,
        'totalmemberaktif'  => $totalmemberaktif,
        'totalpelatih'      => $totalpelatih,
        'daftarMember'      => $daftarMemberAktif,
        'daftarMemberSemua' => $daftarMemberSemua,
        'daftarPelangganPT' => $daftarPelangganPT,
        'daftarPelatih'     => $daftarPelatih,
    ]);
});

Route::post('/member/checkin/{id}', [MemberController::class, 'checkin'])->name('member.checkin');
Route::post('/member/perpanjang/{id}', [MemberController::class, 'perpanjang'])->name('member.perpanjang');
Route::resource('member', MemberController::class)->parameters(['member' => 'id']);

Route::resource('harian', HarianController::class)->only(['index', 'store', 'update', 'destroy'])->parameters(['harian' => 'id']);

Route::resource('pelatih', PelatihController::class)->only(['index', 'store', 'update', 'destroy'])->parameters(['pelatih' => 'id']);
Route::post('/pelatih/pengguna', [PelatihController::class, 'storePengguna'])->name('pelatih.storePengguna');
Route::delete('/pelatih/pengguna/{id}', [PelatihController::class, 'destroyPengguna'])->name('pelatih.destroyPengguna');

Route::resource('transaksi', TransaksiController::class)->only(['index', 'store']);