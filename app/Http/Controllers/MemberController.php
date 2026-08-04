<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use App\Models\Member;
use App\Models\Transaksi;
use App\Models\HargaPaket;
use Carbon\Carbon;
use Endroid\QrCode\QrCode;
use App\Models\AbsensiMember;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\DB;
use Endroid\QrCode\Writer\PngWriter;

class MemberController extends Controller
{

    public function index(Request $request)
    {
        $query = Member::query();

        if ($request->filled('cari')) {
            $query->where('nama_member', 'LIKE', '%' . $request->cari . '%')
                  ->orWhere('nomor_telepon', 'LIKE', '%' . $request->cari . '%');
        }

        $hargaBulanan = HargaPaket::where('nama_paket', 'member')->first()->harga ?? 10000;

        // Ambil data check-in hari ini dari Transaksi & AbsensiMember
        $checkinManual = Transaksi::where('tipe_transaksi', 'Checkin')
                                  ->whereDate('created_at', Carbon::today())
                                  ->pluck('member_id')
                                  ->toArray();

        $checkinQr = AbsensiMember::whereDate('created_at', Carbon::today())
                                  ->pluck('member_id')
                                  ->toArray();

        $memberSudahCheckinHariIni = array_unique(array_merge($checkinManual, $checkinQr));

        // [EKSEKUSI PENGURUTAN] 
        // 1. Member yang sudah check-in didorong ke bawah (menggunakan FIELD MySQL)
        if (!empty($memberSudahCheckinHariIni)) {
            $ids = implode(',', $memberSudahCheckinHariIni);
            $query->orderByRaw("FIELD(id, $ids) ASC");
        }
        
        // 2. Urutkan berdasarkan masa aktif paling sedikit (tanggal kadaluarsa paling dekat)
        $query->orderBy('tanggal_kadaluarsa', 'asc');

        $daftarMember = $query->paginate(10)->appends($request->all());

        return view('member', compact('daftarMember', 'memberSudahCheckinHariIni', 'hargaBulanan'));
    }


    public function store(StoreMemberRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $cekNomorSama = Member::where('nomor_telepon', $request->nomor_telepon)
                ->lockForUpdate()
                ->first();

            if ($cekNomorSama && $cekNomorSama->nama_member !== $request->nama_member) {
                return redirect()->route('member.index')->with('eror', 'Gagal! Nomor HP ' . $request->nomor_telepon . ' sudah terdaftar di sistem atas nama "' . $cekNomorSama->nama_member . '". Gunakan nomor lain!');
            }

            $memberLama = Member::where('nama_member', $request->nama_member)
                ->where('nomor_telepon', $request->nomor_telepon)
                ->lockForUpdate()
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
                    'member_id' => $memberLama->id,
                    'nama_pelanggan' => $memberLama->nama_member,
                    'tipe_transaksi' => 'Perpanjang',
                    'nominal' => $request->nominal,
                    'user_id' => auth()->id(),
                    'nama_paket_snapshot' => 'member',
                    'harga_snapshot' => $request->nominal,
                    'status' => 'paid',
                ]);

                return redirect()->route('member.index')->with('sukses', 'Sistem Mendeteksi Member Lama! Status keanggotaan "' . $memberLama->nama_member . '" otomatis diperpanjang +30 hari ke depan.');
            }

            $memberBaru = Member::create([
                'nama_member' => $request->nama_member,
                'nomor_telepon' => $request->nomor_telepon,
                'tanggal_kadaluarsa' => Carbon::today()->addDays(30),
                'total_checkin' => 0,
            ]);

            Transaksi::create([
                'member_id' => $memberBaru->id,
                'nama_pelanggan' => $memberBaru->nama_member,
                'tipe_transaksi' => 'Baru',
                'nominal' => $request->nominal,
                'user_id' => auth()->id(),
                'nama_paket_snapshot' => 'member',
                'harga_snapshot' => $request->nominal,
                'status' => 'paid',
            ]);

            return redirect()->route('member.index')->with('sukses', 'Member baru bernama "' . $request->nama_member . '" berhasil didaftarkan!');
        });
    }

 
    public function update(UpdateMemberRequest $request, string $id)
    {
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
        return DB::transaction(function () use ($id) {
            $member = Member::whereKey($id)->lockForUpdate()->first();
            if (!$member) {
                return redirect()->back()->with('gagal', 'Member tidak ditemukan!');
            }

            if (Carbon::parse($member->tanggal_kadaluarsa)->isPast()) {
                return redirect()->back()->with('gagal', 'Gagal! Masa aktif member sudah habis.');
            }

            if ($member->sudahCheckinHariIni()) {
                return redirect()->back()->with('gagal', 'Gagal! Member ' . $member->nama_member . ' sudah check-in hari ini!');
            }

            $paket = HargaPaket::where('nama_paket', 'checkin')->first();
            $nominalOtomatis = $paket ? $paket->harga : 0;

            Transaksi::create([
                'member_id' => $member->id,
                'nama_pelanggan' => $member->nama_member,
                'tipe_transaksi' => 'Checkin',
                'nominal' => $nominalOtomatis,
                'user_id' => auth()->id(),
                'nama_paket_snapshot' => 'checkin',
                'harga_snapshot' => $nominalOtomatis,
                'status' => 'paid',
            ]);

            AbsensiMember::create([
                'member_id' => $member->id,
            ]);

            $member->increment('total_checkin');

            return redirect()->back()->with('sukses', 'Check-in berhasil!');
        });
    }

    public function perpanjang(Request $request, string $id)
    {
        $request->validate([
            'nominal' => 'required|integer|min:0',
        ], [
            'nominal.integer' => 'Gagal! Nominal perpanjang harus berupa angka bulat murni.',
            'nominal.min'     => 'Gagal! Nominal perpanjang tidak boleh bernilai minus.',
        ]);

        return DB::transaction(function () use ($request, $id) {
            $member = Member::whereKey($id)->lockForUpdate()->firstOrFail();
            $expiredLama = Carbon::parse($member->tanggal_kadaluarsa);
            $expiredBaru = $expiredLama->isPast() ? Carbon::today()->addDays(30) : $expiredLama->addDays(30);

            $member->update([
                'tanggal_kadaluarsa' => $expiredBaru
            ]);

            Transaksi::create([
                'member_id' => $member->id,
                'nama_pelanggan' => $member->nama_member,
                'tipe_transaksi' => 'Perpanjang',
                'nominal' => $request->nominal,
                'user_id' => auth()->id(),
                'nama_paket_snapshot' => 'member',
                'harga_snapshot' => $request->nominal,
                'status' => 'paid',
            ]);

            return redirect()->route('member.index')->with('sukses', 'Masa aktif member ' . $member->nama_member . ' berhasil diperpanjang +30 hari!');
        });
    }
    

    

    public function prosesCheckinQr(Request $request)
    {
        $tokenQr = $request->member_id;

        try {
            $decodedToken = Crypt::decryptString($tokenQr);
            $payload = json_decode($decodedToken, true);
        } catch (DecryptException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'QR Code tidak dikenali atau tidak valid!'
            ]);
        }

        if (!is_array($payload) || empty($payload['member_id'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Format QR Code tidak valid!'
            ]);
        }

        if (!empty($payload['expires_at']) && Carbon::parse($payload['expires_at'])->isPast()) {
            return response()->json([
                'status' => 'error',
                'message' => 'QR Code sudah expired. Silakan generate ulang dari data member.'
            ]);
        }

        $memberId = (int) $payload['member_id'];

        return DB::transaction(function () use ($memberId) {
            $member = Member::whereKey($memberId)->lockForUpdate()->first();

            if (!$member) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'ID member tidak terdaftar di database!'
                ]);
            }

            $tanggalSekarang = Carbon::today();
            $tanggalExpired = Carbon::parse($member->tanggal_kadaluarsa);

            if ($tanggalSekarang->greaterThan($tanggalExpired)) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Masa aktif member atas nama {$member->nama_member} sudah habis pada " . date('d M Y', strtotime($member->tanggal_kadaluarsa))
                ]);
            }

            if ($member->sudahCheckinHariIni()) {
                return response()->json([
                    'status' => 'error',
                    'message' => "{$member->nama_member} sudah melakukan check-in hari ini!"
                ]);
            }

            $paket = HargaPaket::where('nama_paket', 'checkin')->first();
            $nominalOtomatis = $paket ? $paket->harga : 0;

            Transaksi::create([
                'member_id' => $member->id,
                'nama_pelanggan' => $member->nama_member,
                'tipe_transaksi' => 'Checkin',
                'nominal' => $nominalOtomatis,
                'user_id' => auth()->id(),
                'nama_paket_snapshot' => 'checkin',
                'harga_snapshot' => $nominalOtomatis,
                'status' => 'paid',
            ]);

            AbsensiMember::create([
                'member_id' => $member->id,
            ]);

            $member->increment('total_checkin');

            return response()->json([
                'status' => 'success',
                'nama' => $member->nama_member,
                'message' => 'Silakan masuk, selamat berlatih!'
            ]);
        });
    }
}