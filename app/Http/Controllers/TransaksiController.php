<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Member;
use App\Models\AbsensiMember;
use Carbon\Carbon;

class TransaksiController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'tipe_kunjungan' => 'required',
            'nama' => 'required|string|max:100',
            'nominal' => 'required|numeric',
        ]);

        $namaClean = strip_tags(trim($request->nama));

        if ($request->tipe_kunjungan === 'checkin') {
            
            $memberLama = Member::where('nama_member', $namaClean)->first();
            
            if (!$memberLama) {
                return redirect()->back()
                    ->withInput() // Agar inputan form tidak hilang otomatis
                    ->with('gagal', 'Transaksi Gagal! Nama "' . $namaClean . '" tidak terdaftar sebagai member.');
            }
        }

        $tipeTransaksi = 'Harian';
        if ($request->tipe_kunjungan === 'baru') {
            $tipeTransaksi = 'Baru';
        } elseif ($request->tipe_kunjungan === 'checkin') {
            $tipeTransaksi = 'Checkin';
        }


        $transaksi = new Transaksi();
        $transaksi->tipe_transaksi = $tipeTransaksi;
        $transaksi->nama_pelanggan = $namaClean;
        $transaksi->nominal        = (int) $request->nominal;
        $transaksi->save();


        if ($request->tipe_kunjungan === 'baru') {
            Member::create([
                'nama_member'        => $namaClean,
                'nomor_telepon'      => $request->nomor_telepon ?? '08123456789',
                'tanggal_kadaluarsa' => Carbon::now()->addDays(30),
            ]);
        }
        
        if ($request->tipe_kunjungan === 'checkin') {
            AbsensiMember::create([
                'member_id' => $memberLama->id
            ]);
        }

        return redirect()->back()->with('sukses', 'Transaksi berhasil disimpan!');
    }
    // Fungsi untuk menampilkan halaman Catatan Transaksi
    public function index()
    {
        // Mengambil semua data transaksi, yang paling baru diinput ditaruh paling atas
        $semuaTransaksi = Transaksi::latest()->get();

        return view('transaksi', compact('semuaTransaksi'));
    }
}