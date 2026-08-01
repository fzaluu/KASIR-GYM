<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Member;
use App\Models\Pelatih;
use App\Models\PenggunaPelatih;
use App\Models\HargaPaket;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $hariIni = Carbon::today()->toDateString();
        
        $totalHariIni = Transaksi::where('tipe_transaksi', 'Harian')
                                    ->whereDate('created_at', Carbon::today())
                                    ->count();

        $totalKemarin = Transaksi::where('tipe_transaksi', 'Harian')
                                    ->whereDate('created_at', Carbon::yesterday())
                                    ->count();

        // [PERBAIKAN] Ambil ID yang sudah check-in dari Transaksi (manual) DAN AbsensiMember (QR Scanner)
        $checkinManual = Transaksi::whereDate('created_at', $hariIni)
                                   ->where('tipe_transaksi', 'Checkin')
                                   ->whereNotNull('member_id')
                                   ->pluck('member_id')
                                   ->toArray();

        $checkinQr = \App\Models\AbsensiMember::whereDate('created_at', Carbon::today())
                                              ->pluck('member_id')
                                              ->toArray();

        // Gabungkan kedua sumber data dan hilangkan duplikat ID
        $idSudahCheckin = array_unique(array_merge($checkinManual, $checkinQr));

        $data = [
            'logaktivitas'      => Transaksi::whereDate('created_at', $hariIni)->orderBy('created_at', 'desc')->paginate(10),
            'totalpemasukan'    => Transaksi::whereDate('created_at', $hariIni)->sum('nominal'),
            'totalmemberaktif'  => Member::whereDate('tanggal_kadaluarsa', '>=', $hariIni)->count(),
            'totalpelatih'      => Pelatih::count(),
            
            // Daftar member aktif yang belum check-in hari ini (aman dari data manual maupun QR)
            'daftarMember'      => Member::whereDate('tanggal_kadaluarsa', '>=', $hariIni)
                                         ->whereNotIn('id', $idSudahCheckin)
                                         ->orderBy('nama_member', 'asc')
                                         ->get(),
                                         
            'daftarMemberSemua' => Member::orderBy('nama_member', 'asc')->get(),
            'daftarPelangganPT' => PenggunaPelatih::orderBy('nama_pengguna', 'asc')->get(),
            'daftarPelatih'     => Pelatih::orderBy('nama_pelatih', 'asc')->get(),
            'hargaPaket'        => HargaPaket::all(),
        ];

        return view('dashboard', compact('totalHariIni', 'totalKemarin'), $data);
    }
}