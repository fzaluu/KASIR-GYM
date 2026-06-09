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

        if ($request->has('cari') && $request->cari != '') {
            $query->where('nama_member', 'LIKE', '%' . $request->cari . '%');
        }

        $daftarMember = $query->orderBy('nama_member', 'asc')->get();
        return view('member', compact('daftarMember'));
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

        // 🛠️ LANGKAH 1: Validasi nomor HP unik terlebih dahulu (Cegah No HP sama tapi nama beda)
        $cekNomorSama = Member::where('nomor_telepon', $request->nomor_telepon)->first();
        if ($cekNomorSama) {
            // Jika nomor HP sama tapi namanya diinput berbeda, langsung blokir pendaftaran!
            if ($cekNomorSama->nama_member !== $request->nama_member) {
                return redirect()->route('member.index')->with('eror', 'Gagal! Nomor HP ' . $request->nomor_telepon . ' sudah terdaftar di sistem atas nama "' . $cekNomorSama->nama_member . '". Gunakan nomor lain!');
            }
        }

        // 🔍 Cari apakah nama DAN nomor HP ini sudah cocok & pernah terdaftar sebelumnya
        $memberLama = Member::where('nama_member', $request->nama_member)
                            ->where('nomor_telepon', $request->nomor_telepon)
                            ->first();

        if ($memberLama) {
            // Cek status apakah dia masih aktif atau sudah expired
            $apakahMasihAktif = Carbon::parse($memberLama->tanggal_kadaluarsa)->isFuture();

            // 🚫 KASUS 1: Jika masih aktif, BLOKIR pendaftaran duplikat
            if ($apakahMasihAktif) {
                return redirect()->route('member.index')->with('eror', 'Gagal! Member atas nama "' . $memberLama->nama_member . '" statusnya masih AKTIF sampai ' . date('d M Y', strtotime($memberLama->tanggal_kadaluarsa)) . '. Tidak perlu di-input ulang!!');
            }

            // 🔄 KASUS 2: Jika sudah expired, OTOMATIS perpanjang durasinya (+30 hari dari hari ini)
            $memberLama->update([
                'tanggal_kadaluarsa' => Carbon::today()->addDays(30)
            ]);

            Transaksi::create([
                'nama_pelanggan' => $memberLama->nama_member,
                'nomor_telepon'  => $memberLama->nomor_telepon,
                'tipe_transaksi' => 'Perpanjang Member',
                'nominal'        => $request->nominal,
            ]);

            return redirect()->route('member.index')->with('sukses', 'Sistem Mendeteksi Member Lama! Status keanggotaan "' . $memberLama->nama_member . '" otomatis diperpanjang +30 hari ke depan.');
        }

        // ✨ KASUS 3: Jika benar-benar member baru murni (belum ada di DB)
        Member::create([
            'nama_member'        => $request->nama_member,
            'nomor_telepon'      => $request->nomor_telepon,
            'tanggal_kadaluarsa' => Carbon::today()->addDays(30),
            'total_checkin'      => 0,
        ]);

        Transaksi::create([
            'nama_pelanggan' => $request->nama_member,
            'nomor_telepon'  => $request->nomor_telepon,
            'tipe_transaksi' => 'Pendaftaran Member',
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

        // 🛠️ LANGKAH 2: Validasi Pembatasan Check-In Maksimal 1x Sehari
        // Sistem memeriksa apakah hari ini sudah ada log transaksi check-in untuk nama member ini
        $sudahCheckinHariIni = Transaksi::where('nama_pelanggan', $member->nama_member)
                                         ->where('tipe_transaksi', 'Check-In Member')
                                         ->whereDate('created_at', Carbon::today())
                                         ->exists();

        if ($sudahCheckinHariIni) {
            return redirect()->route('member.index')->with('eror', 'Gagal! Member bernama "' . $member->nama_member . '" sudah melakukan check-in hari ini. Tidak boleh duplikat!');
        }

        // Jika belum ada check-in hari ini, maka proses dilanjutkan
        $member->increment('total_checkin'); 

        Transaksi::create([
            'nama_pelanggan' => $member->nama_member,
            'nomor_telepon'  => $member->nomor_telepon,
            'tipe_transaksi' => 'Check-In Member',
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

        // 1. Update data masa aktif member di database
        $member->update([
            'tanggal_kadaluarsa' => $expiredBaru
        ]);

        // 2. Catat iuran bulanan tersebut ke riwayat transaksi kas keuangan
        Transaksi::create([
            'nama_pelanggan' => $member->nama_member,
            'nomor_telepon'  => $member->nomor_telepon,
            'tipe_transaksi' => 'Perpanjang Member',
            'nominal'        => $request->nominal,
        ]);

        return redirect()->route('member.index')->with('sukses', 'Masa aktif member ' . $member->nama_member . ' berhasil diperpanjang +30 hari!');
    }
}