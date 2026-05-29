<?php

use App\Http\Controllers\MemberController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransaksiController;
use App\Models\Transaksi;
use App\Models\Member;
use Carbon\Carbon;
Route::get('/', function () {
    $transaksihariini = Transaksi ::whereDate('created_at', Carbon::today())
                                    ->orderBy('created_at', 'desc')
                                    ->get();
    $totalpemasukan = Transaksi::whereDate('created_at', Carbon::today())->sum('nominal');
    $totalmemberaktif = Member::count();
    return view('dashboard',[
        'logaktivitas' => $transaksihariini,
        'totalpemasukan' => $totalpemasukan,
        'totalmemberaktif' => $totalmemberaktif
    ]);
});

Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
Route::post('/transaksi/simpan', [TransaksiController::class, 'store'])->name('transaksi.simpan');

Route::get('/member',[MemberController::class, 'index'])->name('member.index');
Route::post('/member/simpan', [MemberController::class, 'store'])->name('member.simpan');