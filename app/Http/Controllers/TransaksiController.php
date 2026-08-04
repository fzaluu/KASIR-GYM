<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransaksiRequest;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Member;
use App\Models\PenggunaPelatih;
use App\Models\Pelatih;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaksi::query();

        // Mengisi tanggal otomatis bawaan hari ini jika tidak ada filter rentang tanggal
        if (!$request->filled('tanggal_mulai') && !$request->filled('tanggal_selesai')) {
            $request->merge([
                'tanggal_mulai' => Carbon::today()->toDateString(),
                'tanggal_selesai' => Carbon::today()->toDateString()
            ]);
        }

        // Filter Rentang Tanggal (Mulai - Selesai)
        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->tanggal_mulai)->startOfDay(),
                Carbon::parse($request->tanggal_selesai)->endOfDay()
            ]);
        }

        // Filter Pencarian 6 Tipe Transaksi Baku (Jika memilih 'Semua Tipe' atau dikosongkan, lewati filter ini)
        if ($request->filled('tipe_transaksi') && $request->tipe_transaksi !== 'Semua Tipe') {
            $query->where('tipe_transaksi', $request->tipe_transaksi);
        }

        $semuaTransaksi = $query->orderBy('created_at', 'desc')->get();

        return view('transaksi', compact('semuaTransaksi'));
    }

    /**
     * Fitur Cetak Excel Eksklusif Tanpa Library Tambahan (HTML Stream)
     */
    
    public function export(Request $request)
    {
        $query = Transaksi::query();

        // Terapkan Filter Tanggal yang dikirim dari form
        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->tanggal_mulai)->startOfDay(),
                Carbon::parse($request->tanggal_selesai)->endOfDay()
            ]);
        }

        // Terapkan Filter Tipe Transaksi yang dikirim dari form
        $tipeTerpilih = $request->tipe_transaksi ?? 'Semua Tipe';
        if ($request->filled('tipe_transaksi') && $request->tipe_transaksi !== 'Semua Tipe') {
            $query->where('tipe_transaksi', $request->tipe_transaksi);
        }

        // ==========================================
        // 🛡️ PENAMBAHAN NO. 6: BATASAN MAKSIMAL DATA
        // ==========================================
        $totalData = $query->count();
        if ($totalData > 5000) {
            return back()->with('eror', 'Data export terlalu besar (' . $totalData . ' baris). Batas maksimal export adalah 5000 baris data sekaligus agar server tidak kewalahan.');
        }

        $dataTransaksi = $query->orderBy('created_at', 'asc')->get();

        // ==========================================
        // 🛡️ PENAMBAHAN NO. 6: SANITASI FORMULA INJECTION
        // ==========================================
        $dataTransaksi->each(function ($row) {
            // Jika nama pelanggan diawali karakter =, +, -, atau @, beri tanda petik satu (') di depan
            if (in_array(substr($row->nama_pelanggan, 0, 1), ['=', '+', '-', '@'])) {
                $row->nama_pelanggan = "'" . $row->nama_pelanggan;
            }
        });

        // Ambil info tanggal untuk nama file
        $tglMulai = $request->tanggal_mulai ?? Carbon::today()->toDateString();
        $tglSelesai = $request->tanggal_selesai ?? Carbon::today()->toDateString();
        $namaFile = "Laporan_Transaksi_Virgo_Gym_(" . str_replace(' ', '_', $tipeTerpilih) . ")_" . $tglMulai . "_s.d_" . $tglSelesai . ".xls";

        // Setting Header HTTP agar browser mengenalnya sebagai file Excel murni
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=$namaFile");
        header("Pragma: no-cache");
        header("Expires: 0");

        // Stream HTML Table sebagai struktur file Excel
        echo '<table border="1">';
        echo '<thead>';
        echo '<tr><th colspan="7" style="font-size:16px; font-weight:bold; text-align:center; height:30px;">LAPORAN TRANSAKSI VIRGO GYM</th></tr>';
        echo '<tr><th colspan="7" style="text-align:center;">Periode: ' . date('d M Y', strtotime($tglMulai)) . ' s.d ' . date('d M Y', strtotime($tglSelesai)) . ' | Tipe: ' . $tipeTerpilih . '</th></tr>';
        echo '<tr><th>No</th><th>Tanggal & Jam</th><th>Nama Pelanggan</th><th>Tipe Transaksi</th><th>Nama Pelatih (Jika PT)</th><th>Nominal (Rp)</th></tr>';
        echo '</thead>';
        echo '<tbody>';

        $totalBaris = count($dataTransaksi);
        
        foreach ($dataTransaksi as $index => $row) {
            $namaPelatih = '-';
            
            // Pengecekan nama pelatih menggunakan relasi atau tembak ID langsung
            if ($row->pelatih_id) {
                if (isset($row->pelatih->nama_pelatih)) {
                    $namaPelatih = $row->pelatih->nama_pelatih;
                } else {
                    $pt = Pelatih::find($row->pelatih_id);
                    if ($pt) $namaPelatih = $pt->nama_pelatih;
                }
            }

            // Kode echo TR harus berada DI DALAM foreach agar mengulang sesuai jumlah data
            echo '<tr>';
            echo '<td style="text-align:center;">' . ($index + 1) . '</td>';
            echo '<td>' . date('d-m-Y H:i', strtotime($row->created_at)) . '</td>';
            echo '<td>' . $row->nama_pelanggan . '</td>';
            echo '<td style="text-align:center;">' . $row->tipe_transaksi . '</td>';
            echo '<td>' . $namaPelatih . '</td>';
            echo '<td style="text-align:right;">' . $row->nominal . '</td>';
            echo '</tr>';
        } 

        $barisMulaiRumus = 4; 
        $barisAkhirRumus = 3 + $totalBaris; 
        
        echo '<tr>';
        echo '<td colspan="5" style="text-align:right; font-weight:bold; background-color:#f1f5f9;">TOTAL PEMASUKAN:</td>';
        if ($totalBaris > 0) {
            echo '<td style="text-align:right; font-weight:bold; background-color:#e2e8f0;">=SUM(F' . $barisMulaiRumus . ':F' . $barisAkhirRumus . ')</td>';
        } else {
            echo '<td style="text-align:right; font-weight:bold; background-color:#e2e8f0;">0</td>';
        }
        echo '</tr>';
        
        echo '</tbody>';
        echo '</table>';
        exit;
    }

   public function store(StoreTransaksiRequest $request)
{
    return DB::transaction(function () use ($request) {
        $tipe = $request->tipe_kunjungan;
        $namaPelanggan = '';
        $memberId = null;
        $pelatihId = null;
        $tipeTransaksiBaku = '';

        if ($tipe === 'harian') {
            $sudahInputHarian = Transaksi::where('nama_pelanggan', $request->nama)
                ->where('tipe_transaksi', 'Harian')
                ->whereDate('created_at', Carbon::today())
                ->exists();

            if ($sudahInputHarian) {
                return redirect()->back()->with('gagal', 'Gagal! Nama ' . $request->nama . ' sudah melakukan transaksi harian hari ini.');
            }

            $namaPelanggan = $request->nama;
            $tipeTransaksiBaku = 'Harian';
        } elseif ($tipe === 'checkin' || $tipe === 'perpanjang') {
            $member = Member::whereKey($request->member_id)->lockForUpdate()->first();
            if (!$member) {
                return redirect()->back()->with('gagal', 'Data member wajib dipilih!');
            }

            $namaPelanggan = $member->nama_member;
            $memberId = $member->id;

            if ($tipe === 'checkin') {
                $tipeTransaksiBaku = 'Checkin';

                if (Carbon::parse($member->tanggal_kadaluarsa)->isPast()) {
                    return redirect()->back()->with('gagal', 'Gagal! Masa aktif member sudah habis.');
                }

                if ($member->sudahCheckinHariIni()) {
                    return redirect()->back()->with('gagal', 'Gagal! Member ' . $namaPelanggan . ' sudah check-in hari ini!');
                }

                $member->increment('total_checkin');
            } else {
                $tipeTransaksiBaku = 'Perpanjang';

                $sudahPerpanjang = Transaksi::where('member_id', $memberId)
                    ->where('tipe_transaksi', 'Perpanjang')
                    ->whereDate('created_at', Carbon::today())
                    ->exists();

                if ($sudahPerpanjang) {
                    return redirect()->back()->with('gagal', 'Gagal! Member ' . $namaPelanggan . ' sudah melakukan perpanjang hari ini.');
                }

                $expiredLama = Carbon::parse($member->tanggal_kadaluarsa);
                $expiredBaru = $expiredLama->isPast() ? Carbon::today()->addDays(30) : $expiredLama->addDays(30);
                $member->update(['tanggal_kadaluarsa' => $expiredBaru]);
            }
        }

        Transaksi::create([
            'tipe_transaksi' => $tipeTransaksiBaku,
            'member_id' => $memberId,
            'pelatih_id' => $pelatihId,
            'nama_pelanggan' => $namaPelanggan,
            'nominal' => $request->nominal ?? 0,
            'user_id' => auth()->id(),
            'nama_paket_snapshot' => $tipeTransaksiBaku,
            'harga_snapshot' => $request->nominal ?? 0,
            'status' => 'paid',
        ]);

        return redirect()->back()->with('sukses', 'Data berhasil dicatat.');
    });
}
}