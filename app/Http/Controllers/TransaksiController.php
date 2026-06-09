<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Member;
use App\Models\Pelatih;
use App\Models\AbsensiMember;
use Carbon\Carbon;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaksi::query();

        // Fitur Filter Hari / Tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('created_at', $request->tanggal);
        }

        // Fitur Filter Bulan dan Tahun
        if ($request->filled('bulan')) {
            $tahunBulan = explode('-', $request->bulan);
            $query->whereYear('created_at', $tahunBulan[0])
                  ->whereMonth('created_at', $tahunBulan[1]);
        }

        // Fitur Filter Kategori Tipe Transaksi
        if ($request->filled('tipe_transaksi')) {
            $query->where('tipe_transaksi', $request->tipe_transaksi);
        }

        // Mengambil data transaksi terbaru
        $semuaTransaksi = $query->latest()->get();

        // Mengambil data master member untuk pilihan di dropdown form
        $daftarMember = Member::orderBy('nama_member', 'asc')->get();

        // Mengambil data master pelatih yang statusnya hadir untuk pilihan dropdown sewa PT
        $daftarPelatih = Pelatih::where('status_hadir', 'hadir')->orderBy('nama_pelatih', 'asc')->get();

        // Mengirimkan semua data ke file view transaksi.blade.php
        return view('transaksi', compact('semuaTransaksi', 'daftarMember', 'daftarPelatih'));
    }

    public function store(Request $request)
    {
        // Validasi inputan form kasir
        $request->validate([
            'tipe_kunjungan' => 'required',
            'nominal'        => 'required|numeric',
            'nama'           => 'required_if:tipe_kunjungan,harian,baru|nullable|string|max:100',
            'member_id'      => 'required_if:tipe_kunjungan,checkin,perpanjang,sewa_pt|nullable|integer',
            'pelatih_id'     => 'required_if:tipe_kunjungan,sewa_pt|nullable|integer',
        ]);

        $hariIni = Carbon::today();
        $transaksi = new Transaksi();

        // 1. PROSES JIKA KUNJUNGAN HARIAN (NON-MEMBER)
        if ($request->tipe_kunjungan === 'harian') {
            $namaClean = strip_tags(trim($request->nama));

            // Proteksi double click/spam dalam waktu 10 detik
            $doubleClick = Transaksi::where('nama_pelanggan', $namaClean)
                                     ->where('tipe_transaksi', 'Harian')
                                     ->where('created_at', '>=', Carbon::now()->subSeconds(10))
                                     ->exists();

            if ($doubleClick) {
                return redirect()->back()->withInput()->with('gagal', 'Transaksi diabaikan! Terdeteksi input harian ganda dalam waktu singkat.');
            }

            $transaksi->tipe_transaksi = 'Harian';
            $transaksi->nama_pelanggan = $namaClean;
            $transaksi->member_id      = null;
            $transaksi->pelatih_id     = null;
        }

        // 2. PROSES JIKA PENDAFTARAN MEMBER BARU
        elseif ($request->tipe_kunjungan === 'baru') {
            $namaClean = strip_tags(trim($request->nama));

            // Cek apakah nama ini sudah jadi member yang masih aktif
            $memberExist = Member::where('nama_member', $namaClean)
                                 ->where('tanggal_kadaluarsa', '>=', $hariIni)
                                 ->first();

            if ($memberExist) {
                return redirect()->back()->withInput()->with('gagal', 'Registrasi Gagal! "' . $namaClean . '" masih terdaftar sebagai member aktif hingga ' . date('d M Y', strtotime($memberExist->tanggal_kadaluarsa)));
            }

            // Buat data master member baru murni di database
            $memberBaru = Member::create([
                'nama_member'        => $namaClean,
                'nomor_telepon'      => $request->nomor_telepon ?? '08123456789',
                'tanggal_kadaluarsa' => Carbon::now()->addDays(30),
            ]);

            $transaksi->tipe_transaksi = 'Baru';
            $transaksi->nama_pelanggan = $memberBaru->nama_member;
            $transaksi->member_id      = $memberBaru->id; // Mengunci ID Relasi
            $transaksi->pelatih_id     = null;
        }

        // 3. PROSES JIKA MEMBER MELAKUKAN CHECK-IN KEDATANGAN
        elseif ($request->tipe_kunjungan === 'checkin') {
            $member = Member::findOrFail($request->member_id);

            // Cek apakah member sudah expired masa aktifnya
            if (Carbon::parse($member->tanggal_kadaluarsa)->isPast()) {
                return redirect()->back()->withInput()->with('gagal', 'Check-in Gagal! Masa aktif member "' . $member->nama_member . '" sudah HABIS.');
            }

            // Cek agar tidak bisa double absen/check-in di hari yang sama
            $sudahAbsen = AbsensiMember::where('member_id', $member->id)
                                        ->whereDate('created_at', $hariIni)
                                        ->exists();

            if ($sudahAbsen) {
                return redirect()->back()->withInput()->with('gagal', 'Check-in Gagal! Member "' . $member->nama_member . '" sudah check-in hari ini.');
            }

            // Catat data ke tabel absensi pendukung
            AbsensiMember::create([
                'member_id' => $member->id
            ]);

            $transaksi->tipe_transaksi = 'Checkin';
            $transaksi->nama_pelanggan = $member->nama_member;
            $transaksi->member_id      = $member->id;
            $transaksi->pelatih_id     = null;
        }

        // 4. PROSES JIKA MEMBER MEMBAYAR IURAN PERPANJANG
        elseif ($request->tipe_kunjungan === 'perpanjang') {
            $member = Member::findOrFail($request->member_id);

            // Hitung tanggal kedaluwarsa baru (+30 hari)
            $baseDate = Carbon::parse($member->tanggal_kadaluarsa)->isPast() ? Carbon::now() : Carbon::parse($member->tanggal_kadaluarsa);
            $member->tanggal_kadaluarsa = $baseDate->addDays(30);
            $member->save();

            $transaksi->tipe_transaksi = 'Perpanjang';
            $transaksi->nama_pelanggan = $member->nama_member;
            $transaksi->member_id      = $member->id;
            $transaksi->pelatih_id     = null;
        }

        // 5. PROSES JIKA MEMBER SEWA JASA PELATIH (PERSONAL TRAINER)
        elseif ($request->tipe_kunjungan === 'sewa_pt') {
            $member = Member::findOrFail($request->member_id);
            $pelatih = Pelatih::findOrFail($request->pelatih_id);

            $transaksi->tipe_transaksi = 'Sewa PT';
            $transaksi->nama_pelanggan = $member->nama_member;
            $transaksi->member_id      = $member->id; // Terelasi ke Member
            $transaksi->pelatih_id     = $pelatih->id; // Terelasi ke Pelatih
        }

        // Simpan data transaksi keuangan ke database kas pusat
        $transaksi->nominal = (int) $request->nominal;
        $transaksi->save();

        return redirect()->back()->with('sukses', 'Transaksi ' . $transaksi->tipe_transaksi . ' berhasil diproses!');
    }
}