<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Member;
use App\Models\PenggunaPelatih;
use App\Models\Pelatih;
use Carbon\Carbon;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaksi::query();

        // Mengisi tanggal otomatis bawaan hari ini jika filter tanggal dan tipe kosong
        if (!$request->filled('tanggal') && !$request->filled('tipe_transaksi')) {
            $request->merge([
                'tanggal' => Carbon::today()->toDateString()
            ]);
        }

        // Filter Pencarian Tanggal (Filter Bulan Dihapus total sesuai request)
        if ($request->filled('tanggal')) {
            $query->whereDate('created_at', $request->tanggal);
        }

        // Filter Pencarian Tipe Transaksi Baku (Harian, Baru, Checkin, Perpanjang, Sewa_pt)
        if ($request->filled('tipe_transaksi')) {
            $query->where('tipe_transaksi', $request->tipe_transaksi);
        }

        $semuaTransaksi = $query->orderBy('created_at', 'desc')->get();

        return view('transaksi', compact('semuaTransaksi'));
    }

    public function store(Request $request)
    {
        $tipe = $request->tipe_kunjungan;
        $namaPelanggan = '';
        $memberId = null;
        $pelatihId = null;
        $tipeTransaksiBaku = ''; 

        // Validasi Alur Inputan Dashboard Terpilih (Murni Harian, Check-In, Perpanjang)
        if ($tipe === 'harian') {
            $namaPelanggan = $request->nama;
            $tipeTransaksiBaku = 'Harian';
        } elseif ($tipe === 'checkin' || $tipe === 'perpanjang') {
            $member = Member::find($request->member_id);
            if (!$member) {
                return redirect()->back()->with('gagal', 'Data member wajib dipilih!');
            }
            $namaPelanggan = $member->nama_member;
            $memberId = $member->id;

            if ($tipe === 'checkin') {
                $tipeTransaksiBaku = 'Checkin';
                
                // 🚫 Cek Validasi Expired
                if (Carbon::parse($member->tanggal_kadaluarsa)->isPast()) {
                    return redirect()->back()->with('gagal', 'Gagal Check-In! Masa aktif member ' . $namaPelanggan . ' sudah habis/expired. Silakan lakukan Perpanjang Member terlebih dahulu!');
                }

                // 🛠️ Solusi Anti-Cheat Double Check-in
                $sudahCheckin = Transaksi::where('member_id', $memberId)
                                         ->where('tipe_transaksi', 'Checkin')
                                         ->whereDate('created_at', Carbon::today())
                                         ->exists();
                if ($sudahCheckin) {
                    return redirect()->back()->with('gagal', 'Gagal! Member bernama ' . $namaPelanggan . ' sudah melakukan check-in hari ini!');
                }

                // 🔥 TRIGGER UTAMA: Tambah total gym (+1)
                $member->increment('total_checkin');

            } else {
                $tipeTransaksiBaku = 'Perpanjang';

                // 🔥 TRIGGER UTAMA: Tambah masa aktif +30 hari
                $expiredLama = Carbon::parse($member->tanggal_kadaluarsa);
                if ($expiredLama->isPast()) {
                    $expiredBaru = Carbon::today()->addDays(30);
                } else {
                    $expiredBaru = $expiredLama->addDays(30);
                }

                $member->update([
                    'tanggal_kadaluarsa' => $expiredBaru
                ]);
            }
        }

        // Simpan Transaksi Keuangan Resmi Kasir Keuangan Dashboard
        Transaksi::create([
            'tipe_transaksi' => $tipeTransaksiBaku, 
            'member_id'      => $memberId,
            'pelatih_id'     => $pelatihId,
            'nama_pelanggan' => $namaPelanggan,
            'nominal'        => $request->nominal ?? 0,
        ]);

        return redirect()->back()->with('sukses', 'Data pengunjung harian berhasil dicatat.');
    }
}