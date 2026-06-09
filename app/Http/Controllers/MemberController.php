<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\Transaksi;
use Carbon\Carbon;

class MemberController extends Controller
{
    /**
     * Tampilkan data member dengan fitur cari.
     */
    public function index(Request $request)
    {
        $query = Member::query();

        if ($request->filled('cari')) {
            $query->where('nama_member', 'LIKE', '%' . $request->cari . '%')
                  ->orWhere('nomor_telepon', 'LIKE', '%' . $request->cari . '%');
        }

        $daftarMember = $query->orderBy('nama_member', 'asc')->get();

        // Ambil semua ID member yang SUDAH check-in hari ini untuk mengunci tombol di view
        $memberSudahCheckinHariIni = Transaksi::where('tipe_transaksi', 'Checkin')
                                              ->whereDate('created_at', Carbon::today())
                                              ->pluck('member_id')
                                              ->toArray();

        return view('member', compact('daftarMember', 'memberSudahCheckinHariIni'));
    }

    /**
     * Daftarkan member baru / perpanjang otomatis via form pendaftaran.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_member'   => 'required|string|max:255',
            'nomor_telepon' => 'required|string|max:20',
            'nominal'       => 'required|numeric',
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

    /**
     * Update data teks / tanggal kadaluarsa manual.
     */
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

    /**
     * Hapus member.
     */
    public function destroy(string $id)
    {
        $member = Member::findOrFail($id);
        $member->delete();

        return redirect()->route('member.index')->with('sukses', 'Data keanggotaan member berhasil dihapus!');
    }

    /**
     * 🔥 FUNGSI CHECK-IN MEMBER
     */
    public function checkin(string $id)
    {
        $member = Member::findOrFail($id);
        
        if (Carbon::parse($member->tanggal_kadaluarsa)->isPast()) {
            return redirect()->route('member.index')->with('eror', 'Gagal Check-In! Masa aktif member ini sudah habis.');
        }

        $sudahCheckinHariIni = Transaksi::where('member_id', $member->id)
                                         ->where('tipe_transaksi', 'Checkin')
                                         ->whereDate('created_at', Carbon::today())
                                         ->exists();

        if ($sudahCheckinHariIni) {
            return redirect()->route('member.index')->with('eror', 'Gagal! Member bernama "' . $member->nama_member . '" sudah melakukan check-in hari ini.');
        }

        $member->increment('total_checkin'); 

        Transaksi::create([
            'member_id'      => $member->id,
            'nama_pelanggan' => $member->nama_member,
            'nomor_telepon'  => $member->nomor_telepon,
            'tipe_transaksi' => 'Checkin',
            'nominal'        => 0,
        ]);

        return redirect()->route('member.index')->with('sukses', 'Check-In berhasil! Selamat berlatih untuk ' . $member->nama_member);
    }

    /**
     * 🔥 FUNGSI PERPANJANG MEMBER + STRUK TRANSAKSI MASUK
     */
    public function perpanjang(Request $request, string $id)
    {
        $request->validate([
            'nominal' => 'required|numeric',
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
}