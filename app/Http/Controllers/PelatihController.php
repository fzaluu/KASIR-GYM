<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelatih;
use App\Models\Transaksi;
use App\Models\PenggunaPelatih;

class PelatihController extends Controller
{
    public function index(Request $request)
    {
        $cari = $request->input('cari');

        // Ambil data pelatih dengan fitur pencarian nama
        $daftarPelatih = Pelatih::when($cari, function($query) use ($cari) {
            return $query->where('nama_pelatih', 'like', '%'.$cari.'%');
        })->get();

        // Ambil semua data pengguna pelatih untuk tabel kedua
        $daftarPengguna = PenggunaPelatih::with('pelatih')->get();

        return view('pelatih', [
            'daftarPelatih' => $daftarPelatih,
            'daftarPengguna' => $daftarPengguna
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pelatih'  => 'required|string|max:255',
            'nomor_telepon' => 'required|string|max:20',
            'tarif_bulanan' => 'required|numeric',
            'tarif_harian'  => 'required|numeric',
            'status_hadir'  => 'required|in:hadir,tidak_hadir',
        ]);

        Pelatih::create($request->all());

        return redirect()->route('pelatih.index')->with('sukses', 'Data pelatih baru berhasil disimpan.');
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama_pelatih'  => 'required|string|max:255',
            'nomor_telepon' => 'required|string|max:20',
            'tarif_bulanan' => 'required|numeric',
            'tarif_harian'  => 'required|numeric',
            'status_hadir'  => 'required|in:hadir,tidak_hadir',
        ]);

        $pelatih = Pelatih::findOrFail($id);
        $pelatih->update($request->all());

        return redirect()->route('pelatih.index')->with('sukses', 'Data profil pelatih berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        Pelatih::destroy($id);
        return redirect()->route('pelatih.index')->with('sukses', 'Data pelatih berhasil dihapus.');
    }

    // --- FUNGSI KHUSUS UNTUK MENANGANI DATA SEWA PENGGUNA ---
    
    public function storePengguna(Request $request)
{
    $request->validate([
        'nama_pengguna'           => 'required|string|max:255',
        'nomor_telepon_pengguna'  => 'required|string|max:20',
        'pelatih_id'              => 'required|exists:pelatih,id',
        'tipe_jasa'               => 'required|in:perbulan,perhari',
        'tarif_jasa'              => 'required|numeric',
    ]);

    // 1. Simpan ke tabel pengguna pelatih
    $pengguna = PenggunaPelatih::create($request->all());

    // 🛠️ TENTUKAN TIPE TRANSAKSI SECARA SPESIFIK UNTUK PT
    $tipeTransaksiPT = $request->tipe_jasa == 'perbulan' ? 'SEWA PELATIH' : 'SEWA PELATIH';

    // 2. MASUK KE CATATAN TRANSAKSI DENGAN TIPE YANG BENAR
    Transaksi::create([
        'nama_pelanggan' => $request->nama_pengguna,
        'nomor_telepon'  => $request->nomor_telepon_pengguna,
        'tipe_transaksi' => $tipeTransaksiPT, // Hasilnya: 'Sewa PT Perbulan' atau 'Sewa PT Perhari'
        'nominal'        => $request->tarif_jasa,
    ]);

    return redirect()->route('pelatih.index')->with('sukses', 'Penyewaan jasa pelatih baru berhasil dicatat dan masuk laporan keuangan.');
}
    public function destroyPengguna(string $id)
    {
        PenggunaPelatih::destroy($id);
        return redirect()->route('pelatih.index')->with('sukses', 'Data sewa pengguna berhasil dihapus.');
    }
}