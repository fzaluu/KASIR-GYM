<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Member;
use App\Models\AbsensiMember;
use Carbon\Carbon;

class TransaksiController extends Controller
{

    public function index()
    {
        // Mengambil semua data transaksi, yang paling baru diinput ditaruh paling atas
        $semuaTransaksi = Transaksi::latest()->get();

        // Mengirimkan data ke file view bernama transaksi.blade.php
        return view('transaksi', compact('semuaTransaksi'));
    }

    public function store(Request $request)
{
    // 1. VALIDASI DASAR: Memastikan inputan dari form tidak kosong
    $request->validate([
        'tipe_kunjungan' => 'required',
        'nama'           => 'required|string|max:100',
        'nominal'        => 'required|numeric',
    ]);

    // Membersihkan inputan nama dari spasi liar dan tag HTML berbahaya
    $namaClean = strip_tags(trim($request->nama));
    $hariIni   = Carbon::today(); // Mengambil tanggal hari ini (jam 00:00:00) untuk validasi tanggal

    // ===================================================================
    // PENCEGATAN 1: LOGIKA MEMBER BARU (Mencegah Duplikasi Member Aktif)
    // ===================================================================
    if ($request->tipe_kunjungan === 'baru') {
        // Cari apakah ada member dengan nama ini yang statusnya MASIH AKTIF
        $memberExist = Member::where('nama_member', $namaClean)
                             ->where('tanggal_kadaluarsa', '>=', $hariIni)
                             ->first();

        // JIKA KETEMU: Usir balik, karena dia tidak boleh daftar baru lagi
        if ($memberExist) {
            return redirect()->back()
                ->withInput()
                ->with('gagal', 'Registrasi Gagal! "' . $namaClean . '" masih terdaftar sebagai member aktif hingga ' . date('d M Y', strtotime($memberExist->tanggal_kadaluarsa)));
        }
    }

    // ===================================================================
    // PENCEGATAN 2: LOGIKA CHECK-IN MEMBER (Mencegah Double Absen & Member Expired)
    // ===================================================================
    if ($request->tipe_kunjungan === 'checkin') {
        $memberLama = Member::where('nama_member', $namaClean)->first();
        
        // Cek A: Apakah namanya terdaftar?
        if (!$memberLama) {
            return redirect()->back()->withInput()->with('gagal', 'Check-in Gagal! Nama "' . $namaClean . '" tidak terdaftar.');
        }

        // Cek B: Apakah masa aktifnya sudah habis?
        if (Carbon::parse($memberLama->tanggal_kadaluarsa)->isPast()) {
            return redirect()->back()->withInput()->with('gagal', 'Check-in Gagal! Masa aktif member "' . $namaClean . '" sudah HABIS. Silakan lakukan Perpanjang Member.');
        }

        // Cek C: Mencegah Double Absen di hari yang sama
        // Kita cek ke tabel absensi_members, apakah id member ini sudah absen hari ini
        $sudahAbsen = AbsensiMember::where('member_id', $memberLama->id)
                                    ->whereDate('created_at', $hariIni)
                                    ->exists(); // Bernilai true jika sudah ada datanya

        if ($sudahAbsen) {
            return redirect()->back()->withInput()->with('gagal', 'Check-in Gagal! Member "' . $namaClean . '" sudah melakukan check-in hari ini.');
        }
    }

    // ===================================================================
    // PENCEGATAN 3: LOGIKA PERPANJANG MEMBER (RENEW)
    // ===================================================================
    if ($request->tipe_kunjungan === 'perpanjang') {
        $memberLama = Member::where('nama_member', $namaClean)->first();

        // Cek apakah data membernya ada?
        if (!$memberLama) {
            return redirect()->back()->withInput()->with('gagal', 'Perpanjang Gagal! Nama "' . $namaClean . '" tidak terdaftar sebagai member lama.');
        }
    }

    // ===================================================================
    // PENCEGATAN 4: KUNJUNGAN HARIAN (Mencegah Spasming / Double Click Button)
    // ===================================================================
    if ($request->tipe_kunjungan === 'harian') {
        // Cek apakah ada transaksi harian dengan nama & nominal sama dalam waktu 10 detik terakhir
        $doubleClick = Transaksi::where('nama_pelanggan', $namaClean)
                                 ->where('nominal', (int)$request->nominal)
                                 ->where('created_at', '>=', Carbon::now()->subSeconds(10))
                                 ->exists();

        if ($doubleClick) {
            return redirect()->back()->withInput()->with('gagal', 'Transaksi diabaikan! Terdeteksi input garian ganda dalam waktu singkat.');
        }
    }


    // ===================================================================
    // EKSEKUSI DATABASE (JIKA LOLOS SEMUA PENCEGATAN DI ATAS)
    // ===================================================================
    
    // Tentukan label tipe transaksi untuk tabel keuangan
    $tipeTransaksi = 'Harian';
    if ($request->tipe_kunjungan === 'baru') $tipeTransaksi = 'Baru';
    if ($request->tipe_kunjungan === 'checkin') $tipeTransaksi = 'Checkin';
    if ($request->tipe_kunjungan === 'perpanjang') $tipeTransaksi = 'Perpanjang';

    // 1. Catat Uang Masuk ke Tabel Transaksi
    $transaksi = new Transaksi();
    $transaksi->tipe_transaksi = $tipeTransaksi;
    $transaksi->nama_pelanggan = $namaClean;
    $transaksi->nominal        = (int) $request->nominal;
    $transaksi->save();

    // 2. Aksi ke tabel pendukung berdasarkan tipenya
    if ($request->tipe_kunjungan === 'baru') {
        // Buat data member baru gres
        Member::create([
            'nama_member'        => $namaClean,
            'nomor_telepon'      => $request->nomor_telepon ?? '08123456789',
            'tanggal_kadaluarsa' => Carbon::now()->addDays(30),
        ]);
    } 
    
    elseif ($request->tipe_kunjungan === 'checkin') {
        // Tambah log absensi datang
        AbsensiMember::create([
            'member_id' => $memberLama->id
        ]);
    } 
    
    elseif ($request->tipe_kunjungan === 'perpanjang') {
        // PEMBERSIHAN LOGIKA: Jika dia sudah expired, perpanjang dari HARI INI. 
        // Jika dia belum expired tapi mau perpanjang duluan, tambahkan dari TANGGAL KADALUARSA LAMANYA.
        $baseDate = Carbon::parse($memberLama->tanggal_kadaluarsa)->isPast() ? Carbon::now() : Carbon::parse($memberLama->tanggal_kadaluarsa);
        
        $memberLama->tanggal_kadaluarsa = $baseDate->addDays(30);
        $memberLama->save(); // Update data tanggal di database
    }

    return redirect()->back()->with('sukses', 'Transaksi ' . $tipeTransaksi . ' berhasil diproses!');
}
}