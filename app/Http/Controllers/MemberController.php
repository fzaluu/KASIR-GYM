<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\Transaksi;
use App\Models\HargaPaket;
use Carbon\Carbon;
use App\Models\AbsensiMember; // Menggunakan model AbsensiMember yang sudah kamu buat
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class MemberController extends Controller
{

    public function index(Request $request)
    {
        $query = Member::query();

        if ($request->filled('cari')) {
            $query->where('nama_member', 'LIKE', '%' . $request->cari . '%')
                  ->orWhere('nomor_telepon', 'LIKE', '%' . $request->cari . '%');
        }

        $daftarMember = $query->orderBy('nama_member', 'asc')->get();

        $hargaBulanan = HargaPaket::where('nama_paket', 'member')->first()->harga ?? 10000;

        // [DIUBAH DI SINI] Gunakan model AbsensiMember agar sinkron dengan proses check-in QR & manual
        $memberSudahCheckinHariIni = AbsensiMember::whereDate('created_at', Carbon::today())
                                                  ->pluck('member_id')
                                                  ->toArray();

        return view('member', compact('daftarMember', 'memberSudahCheckinHariIni', 'hargaBulanan'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'nama_member'   => 'required|string|max:255',
            'nomor_telepon' => 'required|string|max:20',
            'nominal'       => 'required|integer|min:0',
        ], [
            'nominal.integer' => 'Gagal! Nominal pembayaran harus berupa angka bulat murni.',
            'nominal.min'     => 'Gagal! Nominal pembayaran tidak boleh bernilai minus.',
        ]);

        $cekNomorSama = Member::where('nomor_telepon', $request->nomor_telepon)->first();
        if ($cekNomorSama) {
            if ($cekNomorSama->nama_member !== $request->nama_member) {
                return redirect()->route('member.index')->with('eror', 'Gagal! Nomor HP ' . $request->nomor_telepon . ' sudah terdaftar di sistem atas nama "' . $cekNomorSama->nama_member . '". Gunakan nomor lain!');
            }
        }

        $memberLama = Member::where('nama_member', $request->nama_member)
                            ->where('nomor_telepon', $request->nomor_telepon)
                            ->first();

        if ($memberLama) {
            $apakahMasihAktif = Carbon::parse($memberLama->tanggal_kadaluarsa)->isFuture();

            if ($apakahMasihAktif) {
                return redirect()->route('member.index')->with('eror', 'Gagal! Member atas nama "' . $memberLama->nama_member . '" statusnya masih AKTIF sampai ' . date('d M Y', strtotime($memberLama->tanggal_kadaluarsa)) . '. Tidak perlu di-input ulang!!');
            }

            $memberLama->update([
                'tanggal_kadaluarsa' => Carbon::today()->addDays(30)
            ]);

            Transaksi::create([
                'member_id'      => $memberLama->id,
                'nama_pelanggan' => $memberLama->nama_member,
                'nomor_telepon'  => $memberLama->nomor_telepon,
                'tipe_transaksi' => 'Perpanjang',
                'nominal'        => $request->nominal,
            ]);

            return redirect()->route('member.index')->with('sukses', 'Sistem Mendeteksi Member Lama! Status keanggotaan "' . $memberLama->nama_member . '" otomatis diperpanjang +30 hari ke depan.');
        }

        $memberBaru = Member::create([
            'nama_member'        => $request->nama_member,
            'nomor_telepon'      => $request->nomor_telepon,
            'tanggal_kadaluarsa' => Carbon::today()->addDays(30),
            'total_checkin'      => 0,
        ]);

        Transaksi::create([
            'member_id'      => $memberBaru->id,
            'nama_pelanggan' => $memberBaru->nama_member,
            'nomor_telepon'  => $memberBaru->nomor_telepon,
            'tipe_transaksi' => 'Baru',
            'nominal'        => $request->nominal,
        ]);

        return redirect()->route('member.index')->with('sukses', 'Member baru bernama "' . $request->nama_member . '" berhasil didaftarkan!');
    }

  
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama_member'        => 'required|string|max:255',
            'nomor_telepon'      => 'required|string|max:20',
            'tanggal_kadaluarsa' => 'required|date',
        ]);

        $member = Member::findOrFail($id);
        $member->update([
            'nama_member'        => $request->nama_member,
            'nomor_telepon'      => $request->nomor_telepon,
            'tanggal_kadaluarsa' => $request->tanggal_kadaluarsa,
        ]);

        Transaksi::where('member_id', $member->id)->update([
            'nama_pelanggan' => $request->nama_member
        ]);

        return redirect()->route('member.index')->with('sukses', 'Data profil member berhasil diperbarui!');
    }


    public function destroy(string $id)
    {
        $member = Member::findOrFail($id);
        $member->delete();

        return redirect()->route('member.index')->with('sukses', 'Data keanggotaan member berhasil dihapus!');
    }

  
    public function checkin($id)
    {
        $member = Member::find($id);
        if (!$member) {
            return redirect()->back()->with('gagal', 'Member tidak ditemukan!');
        }

        // Validasi masa aktif
        if (Carbon::parse($member->tanggal_kadaluarsa)->isPast()) {
            return redirect()->back()->with('gagal', 'Gagal! Masa aktif member sudah habis.');
        }

        // Validasi duplikasi check-in hari ini
        $sudahCheckin = Transaksi::where('member_id', $member->id)
                                ->where('tipe_transaksi', 'Checkin')
                                ->whereDate('created_at', Carbon::today())
                                ->exists();
        if ($sudahCheckin) {
            return redirect()->back()->with('gagal', 'Gagal! Member ' . $member->nama_member . ' sudah check-in hari ini!');
        }

        // 1. Ambil harga checkin otomatis dari pengaturan harga
        $paket = HargaPaket::where('nama_paket', 'checkin')->first();
        $nominalOtomatis = $paket ? $paket->harga : 0;

        // 2. CATAT KE TABEL TRANSAKSI (Agar masuk Log Aktivitas & Hilang dari Modal Dashboard)
        Transaksi::create([
            'member_id'      => $member->id,
            'nama_pelanggan' => $member->nama_member,
            'tipe_transaksi' => 'Checkin',
            'nominal'        => $nominalOtomatis,
        ]);

        // 3. Increment total checkin member
        $member->increment('total_checkin');

        return redirect()->back()->with('sukses', 'Check-in berhasil!');
    }

    public function perpanjang(Request $request, string $id)
    {
        $request->validate([
            'nominal' => 'required|integer|min:0',
        ], [
            'nominal.integer' => 'Gagal! Nominal perpanjang harus berupa angka bulat murni.',
            'nominal.min'     => 'Gagal! Nominal perpanjang tidak boleh bernilai minus.',
        ]);

        $member = Member::findOrFail($id);
        $expiredLama = Carbon::parse($member->tanggal_kadaluarsa);

        if ($expiredLama->isPast()) {
            $expiredBaru = Carbon::today()->addDays(30);
        } else {
            $expiredBaru = $expiredLama->addDays(30);
        }

        $member->update([
            'tanggal_kadaluarsa' => $expiredBaru
        ]);

        Transaksi::create([
            'member_id'      => $member->id,
            'nama_pelanggan' => $member->nama_member,
            'nomor_telepon'  => $member->nomor_telepon,
            'tipe_transaksi' => 'Perpanjang',
            'nominal'        => $request->nominal,
        ]);

        return redirect()->route('member.index')->with('sukses', 'Masa aktif member ' . $member->nama_member . ' berhasil diperpanjang +30 hari!');
    }
    

    public function prosesCheckinQr(Request $request)
{
    $tokenQr = $request->member_id;

    try {
        // [TAMBAHAN] Dekripsi token aman dari QR code menjadi ID asli member
        $memberId = Crypt::decryptString($tokenQr);
    } catch (DecryptException $e) {
        // [TAMBAHAN] Jika QR code palsu atau rusak
        return response()->json([
            'status' => 'error',
            'message' => 'QR Code tidak dikenali atau tidak valid!'
        ]);
    }

    $member = Member::find($memberId);

    // 1. Cek apakah member terdaftar
    if (!$member) {
        return response()->json([
            'status' => 'error',
            'message' => 'ID member tidak terdaftar di database!'
        ]);
    }

    // --- (SISA KODE DI BAWAHNYA TETAP SAMA SEPERTI PUNYAMU) ---
    // 2. Cek masa aktif membership
    $tanggalSekarang = Carbon::today();
    $tanggalExpired = Carbon::parse($member->tanggal_kadaluarsa);

    if ($tanggalSekarang->greaterThan($tanggalExpired)) {
        return response()->json([
            'status' => 'error',
            'message' => "Masa aktif member atas nama {$member->nama_member} sudah habis pada " . date('d M Y', strtotime($member->tanggal_kadaluarsa))
        ]);
    }

    // 3. Cek apakah sudah check-in hari ini menggunakan model AbsensiMember
    $sudahCheckin = AbsensiMember::where('member_id', $member->id)
        ->whereDate('created_at', Carbon::today())
        ->exists();

    if ($sudahCheckin) {
        return response()->json([
            'status' => 'error',
            'message' => "{$member->nama_member} sudah melakukan check-in hari ini!"
        ]);
    }

    // 4. Simpan riwayat absensi via QR Scanner
    AbsensiMember::create([
        'member_id' => $member->id,
    ]);

    // Tambah total checkin member
    $member->increment('total_checkin');

    return response()->json([
        'status' => 'success',
        'nama' => $member->nama_member,
        'message' => 'Silakan masuk, selamat berlatih!'
    ]);
}
}