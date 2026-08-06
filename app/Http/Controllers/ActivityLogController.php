<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalSelesai = $request->input('tanggal_selesai');
        $userId = $request->input('user_id');
        $aksi = $request->input('aksi');

        $query = ActivityLog::with('user');

        if ($tanggalMulai) {
            $query->whereDate('created_at', '>=', $tanggalMulai);
        }
        if ($tanggalSelesai) {
            $query->whereDate('created_at', '<=', $tanggalSelesai);
        }
        if ($userId) {
            $query->where('user_id', $userId);
        }
        if ($aksi) {
            $query->where('aksi', $aksi);
        }

        // Mengambil data dengan pagination sebanyak 5 data per halaman
        $totalAktivitas = ActivityLog::count();
        $loginHariIni = ActivityLog::where('aksi', 'LIKE', '%login%')->whereDate('created_at', today())->count();
        $userAktif = ActivityLog::distinct('user_id')->count('user_id');
        $perubahanData = ActivityLog::whereIn('aksi', ['tambah', 'edit', 'hapus', 'update', 'create'])->count();
                
        $logs = $query->latest()->paginate(5)->withQueryString();
        $users = User::all();
        $listAksi = ActivityLog::select('aksi')->distinct()->pluck('aksi');

        return view('users.activity-log', compact('logs', 'users', 'listAksi', 'tanggalMulai', 'tanggalSelesai', 'userId', 'aksi'));
    }
}