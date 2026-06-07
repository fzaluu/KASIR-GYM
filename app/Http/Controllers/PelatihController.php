<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Pelatih;

class PelatihController extends Controller
{
    /**
     * Tampilkan daftar pelatih / trainer dengan fitur pencarian nama.
     */
    public function index(Request $request)
    {
        $query = Pelatih::query();

        // Fitur Pencarian berdasarkan nama pelatih
        if ($request->has('cari') && $request->cari != '') {
            $query->where('nama_pelatih', 'LIKE', '%' . $request->cari . '%');
        }

        // Ambil data pelatih dan urutkan berdasarkan abjad nama A-Z
        $daftarPelatih = $query->orderBy('nama_pelatih', 'asc')->get();

        // Lempar data ke halaman blade view pelatih
        return view('pelatih', compact('daftarPelatih'));
    }

    /**
     * Proses simpan data pelatih baru (Menembak ke route pelatih.store).
     */
    public function store(Request $request)
    {
        // Validasi input dari form modal tambah pelatih
        $request->validate([
            'nama_pelatih'    => 'required|string|max:255',
            'nomor_telepon'   => 'required|string|max:20',
            'tarif_per_bulan' => 'required|numeric',
        ]);

        // Simpan data pelatih ke database menggunakan mass assignment
        Pelatih::create([
            'nama_pelatih'    => $request->nama_pelatih,
            'nomor_telepon'   => $request->nomor_telepon,
            'tarif_per_bulan' => $request->tarif_per_bulan,
        ]);

        // Redirect kembali ke halaman index resource dengan flash message sukses
        return redirect()->route('pelatih.index')->with('sukses', 'Data pelatih baru berhasil ditambahkan!');
    }

    /**
     * Proses perbarui data pelatih (Menembak ke route pelatih.update via PUT).
     */
    public function update(Request $request, string $id)
    {
        // Validasi input data edit pelatih
        $request->validate([
            'nama_pelatih'    => 'required|string|max:255',
            'nomor_telepon'   => 'required|string|max:20',
            'tarif_per_bulan' => 'required|numeric',
        ]);

        // Cari data pelatih berdasarkan id, lalu eksekusi update data
        $pelatih = Pelatih::findOrFail($id);
        $pelatih->update([
            'nama_pelatih'    => $request->nama_pelatih,
            'nomor_telepon'   => $request->nomor_telepon,
            'tarif_per_bulan' => $request->tarif_per_bulan,
        ]);

        return redirect()->route('pelatih.index')->with('sukses', 'Data pelatih berhasil diperbarui!');
    }

    /**
     * Proses hapus data pelatih (Menembak ke route pelatih.destroy via DELETE).
     */
    public function destroy(string$id)
    {
        // Cari data pelatih berdasarkan id, lalu eksekusi penghapusan data
        $pelatih = Pelatih::findOrFail($id);
        $pelatih->delete();

        return redirect()->route('pelatih.index')->with('sukses', 'Data pelatih berhasil dihapus dari sistem!');
    }
}