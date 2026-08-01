<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\HargaPaket;
use Carbon\Carbon;

class HarianController extends Controller
{
    public function index(Request $request)
    {
        $tanggalTerpilih = $request->input('tanggal', Carbon::today()->toDateString());
        
        // Ambil harga dari tabel harga_pakets agar selalu update
        $hargaHarian = HargaPaket::where('nama_paket', 'harian')->first()->harga ?? 8000;

        $query = Transaksi::where('tipe_transaksi', 'Harian');

        if ($request->filled('cari')) {
            $query->where('nama_pelanggan', 'LIKE', '%' . $request->cari . '%');
        } else {
            $query->whereDate('created_at', $tanggalTerpilih);
        }

        // Menggunakan paginate(10) dan withQueryString agar parameter filter tetap terbawa saat pindah halaman
        $daftarHarian = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        // Kirim $hargaHarian ke view
        return view('harian', compact('daftarHarian', 'tanggalTerpilih', 'hargaHarian'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'nominal'        => 'required|integer|min:0',
        ]);

        // 🛡️ VALIDASI ANTI DUPLIKAT (2x Input)
        $sudahAda = Transaksi::where('nama_pelanggan', $request->nama_pelanggan)
            ->where('tipe_transaksi', 'Harian')
            ->whereDate('created_at', Carbon::today())
            ->exists();

        if ($sudahAda) {
            return redirect()->route('harian.index')->with('gagal', 'Gagal! ' . $request->nama_pelanggan . ' sudah terinput hari ini.');
        }

        Transaksi::create([
            'nama_pelanggan' => $request->nama_pelanggan,
            'tipe_transaksi' => 'Harian',
            'nominal'        => $request->nominal,
        ]);

        return redirect()->route('harian.index')->with('sukses', 'Data pengunjung harian berhasil dicatat.');
    }

    public function update(Request $request, string $id)
    {
        // 🛡️ VALIDASI ANTI-MINUS & ANTI-TEKS KETIKA EDIT/UPDATE DATA
        $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'nominal'        => 'required|integer|min:0',
        ], [
            'nominal.integer' => 'Gagal! Nominal harus berupa angka bulat murni.',
            'nominal.min'     => 'Gagal! Nominal tidak boleh bernilai minus.',
        ]);

        $transaksi = Transaksi::findOrFail($id);
        $transaksi->update([
            'nama_pelanggan' => $request->nama_pelanggan,
            'nominal'        => $request->nominal,
        ]);

        return redirect()->route('harian.index')->with('sukses', 'Data pengunjung harian berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->delete();

        return redirect()->route('harian.index')->with('sukses', 'Data kunjungan harian berhasil dihapus.');
    }
}