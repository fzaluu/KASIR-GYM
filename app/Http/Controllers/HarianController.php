<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use Carbon\Carbon;

class HarianController extends Controller
{

    public function index(Request $request)
    {
        $tanggalTerpilih = $request->input('tanggal', Carbon::today()->toDateString());

        $totalHariIni = Transaksi::where('tipe_transaksi', 'Harian')
                                 ->whereDate('created_at', Carbon::today())
                                 ->count();

        $totalKemarin = Transaksi::where('tipe_transaksi', 'Harian')
                                 ->whereDate('created_at', Carbon::yesterday())
                                 ->count();

        $query = Transaksi::where('tipe_transaksi', 'Harian');

        if ($request->filled('cari')) {
            $query->where('nama_pelanggan', 'LIKE', '%' . $request->cari . '%');
        } else {
            $query->whereDate('created_at', $tanggalTerpilih);
        }

        $daftarHarian = $query->orderBy('created_at', 'desc')->get();

        return view('harian', compact('daftarHarian', 'totalHariIni', 'totalKemarin', 'tanggalTerpilih'));
    }

    public function store(Request $request)
    {
        // 🛡️ VALIDASI ANTI-MINUS & ANTI-TEKS KETIKA TAMBAH DATA
        $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'nominal'        => 'required|integer|min:0', // Mengunci angka bulat positif
        ], [
            'nominal.integer' => 'Gagal! Nominal harus berupa angka bulat murni.',
            'nominal.min'     => 'Gagal! Nominal tidak boleh bernilai minus.',
        ]);

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
            'nominal'        => 'required|integer|min:0', // Mengunci angka bulat positif
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