<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePelatihRequest;
use App\Http\Requests\UpdatePelatihRequest;
use Illuminate\Http\Request;
use App\Models\Pelatih;
use App\Models\Transaksi;
use App\Models\PenggunaPelatih;

class PelatihController extends Controller
{
    public function index(Request $request)
    {
        $cariPelatih = $request->input('cari_pelatih');
        $cariSewa = $request->input('cari_sewa');

        // Ubah DESC menjadi ASC agar 'hadir' ada di urutan teratas
        $daftarPelatih = Pelatih::when($cariPelatih, function($query) use ($cariPelatih) {
            return $query->where('nama_pelatih', 'like', '%'.$cariPelatih.'%');
        })->orderByRaw("FIELD(status_hadir, 'hadir', 'tidak_hadir') ASC")
          ->orderBy('nama_pelatih', 'asc')
          ->paginate(5, ['*'], 'page_pelatih')
          ->appends($request->query());

        $daftarPengguna = PenggunaPelatih::with('pelatih')
            ->when($cariSewa, function($query) use ($cariSewa) {
                return $query->where('nama_pengguna', 'like', '%'.$cariSewa.'%');
            })->paginate(5, ['*'], 'page_sewa')
            ->appends($request->query());

        return view('pelatih', [
            'daftarPelatih' => $daftarPelatih,
            'daftarPengguna' => $daftarPengguna
        ]);
    }    

    public function store(StorePelatihRequest $request)
    {
        $data = $request->validated();

        Pelatih::create($data);

        return redirect()->route('pelatih.index')->with('sukses', 'Data pelatih baru berhasil disimpan.');
    }

    public function update(UpdatePelatihRequest $request, string $id)
    {
        $data = $request->validated();

        $pelatih = Pelatih::findOrFail($id);
        $pelatih->update($data);

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
    
    public function storePengguna(StorePenggunaPelatihRequest $request)
{
    $pengguna = PenggunaPelatih::create($request->validated());

    Transaksi::create([
        'pelatih_id' => $request->pelatih_id,
        'nama_pelanggan' => $request->nama_pengguna,
        'nomor_telepon' => $request->nomor_telepon_pengguna,
        'tipe_transaksi' => 'Sewa_pt',
        'nominal' => $request->tarif_jasa,
        'user_id' => auth()->id(),
        'nama_paket_snapshot' => 'pelatih_pt',
        'harga_snapshot' => $request->tarif_jasa,
        'status' => 'paid',
    ]);

    return redirect()->route('pelatih.index')->with('sukses', 'Penyewaan jasa pelatih baru berhasil dicatat dan masuk laporan keuangan.');
}

    public function destroyPengguna(string $id)
    {
        PenggunaPelatih::destroy($id);
        return redirect()->route('pelatih.index')->with('sukses', 'Data sewa pengguna berhasil dihapus.');
    }
    public function absen(Request $request, string $id)
    {
        $request->validate([
            'status_hadir' => 'required|in:hadir,tidak_hadir',
        ]);

        $pelatih = Pelatih::findOrFail($id);
        $pelatih->update([
            'status_hadir' => $request->status_hadir,
        ]);

        return redirect()->route('pelatih.index')->with('sukses', 'Status kehadiran pelatih ' . $pelatih->nama_pelatih . ' berhasil diperbarui.');
    }
}