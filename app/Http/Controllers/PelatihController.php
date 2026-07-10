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

        $daftarPelatih = Pelatih::when($cari, function($query) use ($cari) {
            return $query->where('nama_pelatih', 'like', '%'.$cari.'%');
        })->get();

        $daftarPengguna = PenggunaPelatih::with('pelatih')
            ->when($cari, function($query) use ($cari) {
                return $query->where('nama_pengguna', 'like', '%'.$cari.'%');
            })->get();

        return view('pelatih', [
            'daftarPelatih' => $daftarPelatih,
            'daftarPengguna' => $daftarPengguna
        ]);
    }

    public function store(Request $request)
    {
        // 🛡️ VALIDASI ANTI-MINUS TAMBAH PELATIH
        $request->validate([
            'nama_pelatih'  => 'required|unique:pelatih,nama_pelatih',
            'nomor_telepon' => 'required|string|max:20',
            'tarif_bulanan' => 'required|integer|min:0',
            'tarif_harian'  => 'required|integer|min:0', // Wajib angka bulat positif
            'status_hadir'  => 'required|in:hadir,tidak_hadir',
        ], [
            'nama_pelatih.unique' => 'Nama pelatih sudah terdaftar. Gunakan nama lain.',
            'tarif_harian.integer'=> 'Gagal! Tarif harian harus berupa angka bulat.',
            'tarif_harian.min'    => 'Gagal! Tarif harian tidak boleh bernilai minus.',
        ]);

        Pelatih::create($request->all());

        return redirect()->route('pelatih.index')->with('sukses', 'Data pelatih baru berhasil disimpan.');
    }

    public function update(Request $request, string $id)
    {
        // 🛡️ VALIDASI ANTI-MINUS UPDATE PELATIH
        $request->validate([
            'nama_pelatih'  => 'required|string|max:255',
            'nomor_telepon' => 'required|string|max:20',
            'tarif_bulanan' => 'required|integer|min:0',
            'tarif_harian'  => 'required|integer|min:0', // Wajib angka bulat positif
            'status_hadir'  => 'required|in:hadir,tidak_hadir',
        ], [
            'tarif_harian.integer'=> 'Gagal! Tarif harian harus berupa angka bulat.',
            'tarif_harian.min'    => 'Gagal! Tarif harian tidak boleh bernilai minus.',
        ]);

        $pelatih = Pelatih::findOrFail($id);
        $pelatih->update($request->all());

        Transaksi::where('pelatih_id', $pelatih->id)->update([
            'nama_pelanggan' => $request->nama_pelatih
        ]);

        return redirect()->route('pelatih.index')->with('sukses', 'Data profil pelatih berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        Pelatih::destroy($id);
        return redirect()->route('pelatih.index')->with('sukses', 'Data pelatih berhasil dihapus.');
    }
    
    public function storePengguna(Request $request)
    {
        // 🛡️ VALIDASI ANTI-MINUS TRANSAKSI SEWA PT
        $request->validate([
            'nama_pengguna'           => 'required|string|max:255',
            'nomor_telepon_pengguna'  => 'required|string|max:20',
            'pelatih_id'              => 'required|exists:pelatih,id',
            'tipe_jasa'               => 'required|in:perbulan,perhari',
            'tarif_jasa'              => 'required|integer|min:0', // Mengunci nominal sewa
        ], [
            'tarif_jasa.integer'      => 'Gagal! Tarif sewa harus berupa angka bulat murni.',
            'tarif_jasa.min'          => 'Gagal! Tarif sewa tidak boleh bernilai minus.',
        ]);

        $pengguna = PenggunaPelatih::create($request->all());

        Transaksi::create([
            'pelatih_id'     => $request->pelatih_id,
            'nama_pelanggan' => $request->nama_pengguna,
            'nomor_telepon'  => $request->nomor_telepon_pengguna,
            'tipe_transaksi' => 'Sewa_pt',
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