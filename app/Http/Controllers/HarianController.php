<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use Carbon\Carbon;

class HarianController extends Controller
{
    /**
     * Tampilkan halaman utama pengunjung harian beserta filter & statistik.
     */
    public function index(Request $request)
    {
        // 1. Ambil tanggal dari input filter kalender, default-nya adalah hari ini jika kosong
        $tanggalTerpilih = $request->input('tanggal', Carbon::today()->toDateString());

        // 2. Hitung Statistik Pengunjung Harian untuk Counter Box di Atas
        $totalHariIni = Transaksi::where('tipe_transaksi', 'Harian')
                                 ->whereDate('created_at', Carbon::today())
                                 ->count();

        $totalKemarin = Transaksi::where('tipe_transaksi', 'Harian')
                                 ->whereDate('created_at', Carbon::yesterday())
                                 ->count();

        // 3. Bangun Query Utama untuk Tipe Transaksi 'Harian'
        $query = Transaksi::where('tipe_transaksi', 'Harian');

        // Fitur Pencarian berdasarkan nama pelanggan
        if ($request->has('cari') && $request->cari != '') {
            $query->where('nama_pelanggan', 'LIKE', '%' . $request->cari . '%');
        }

        // Fitur Filter Kalender Tanggal
        $query->whereDate('created_at', $tanggalTerpilih);

        // Ambil data pengunjung harian diurutkan dari yang paling baru masuk
        $daftarHarian = $query->orderBy('created_at', 'desc')->get();

        // 4. Lempar data ke halaman blade view harian
        return view('harian', compact('daftarHarian', 'totalHariIni', 'totalKemarin'));
    }

    /**
     * Proses simpan data pengunjung harian baru (Menembak ke route harian.store).
     */
    public function store(Request $request)
    {
        // Validasi input data dari form modal
        $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'nominal'        => 'required|numeric',
        ]);

        // Simpan data langsung ke dalam tabel transaksis
        Transaksi::create([
            'nama_pelanggan' => $request->nama_pelanggan,
            'nomor_telepon'  => $request->nomor_telepon,
            'tipe_transaksi' => 'Harian',
            'nominal'        => $request->nominal,
        ]);

        // Redirect kembali ke halaman index resource dengan membawa flash message sukses
        return redirect()->route('harian.index')->with('sukses', 'Pengunjung harian berhasil dicatat, jirr!');
    }

    /**
     * Proses perbarui data pengunjung harian (Menembak ke route harian.update via PUT).
     */
    public function update(Request $request, string $id)
    {
        // Validasi input data edit
        $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'nominal'        => 'required|numeric',
        ]);

        // Cari data transaksi berdasarkan id, lalu update datanya
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->update([
            'nama_pelanggan' => $request->nama_pelanggan,
            'nomor_telepon'  => $request->nomor_telepon,
            'nominal'        => $request->nominal,
        ]);

        return redirect()->route('harian.index')->with('sukses', 'Data pengunjung harian berhasil diperbarui!');
    }

    /**
     * Proses hapus data pengunjung harian (Menembak ke route harian.destroy via DELETE).
     */
    public function destroy(string $id)
    {
        // Cari data transaksi berdasarkan id, lalu eksekusi penghapusan data
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->delete();

        return redirect()->route('harian.index')->with('sukses', 'Data kunjungan harian berhasil dihapus!');
    }
}