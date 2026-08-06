<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserController extends Controller
{
 public function index(Request $request)
{
    $search = $request->input('search');

    $query = \App\Models\User::with(['role', 'activity_logs' => function($q) {
        $q->latest()->limit(5);
    }])
    ->withCount('transaksis')
    ->withSum('transaksis', 'nominal');

    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('username', 'LIKE', "%{$search}%");
        });
    }

    // Perbaikan sorting Admin di atas, Kasir di bawah tanpa merusak withCount/withSum
    $query->addSelect(['users.*'])
          ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
          ->orderByRaw("FIELD(roles.slug, 'admin') DESC");

    $users = $query->paginate(5)->withQueryString();
    $roles = \App\Models\Role::all();

    if ($request->ajax()) {
        return view('users.index', compact('users', 'search', 'roles'))->render();
    }

    return view('users.index', compact('users', 'search', 'roles'));
}

    public function store(StoreUserRequest $request)
    {
        $user = User::create([
            'name'     => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role_id'  => $request->role_id,
            'status'   => $request->status,
        ]);

        ActivityLogger::log('tambah_user', "Menambah user baru: {$user->username}");

        return redirect()->route('users.index')->with('sukses', 'User baru berhasil ditambahkan.');
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        // Proteksi 1: Admin tidak boleh mengubah data diri sendiri menjadi nonaktif atau bukan admin
        if (auth()->id() === $user->id) {
            $adminRole = Role::where('slug', 'admin')->first();
            if ($request->status === 'nonaktif' || $request->role_id != $adminRole->id) {
                return back()->with('eror', 'Anda tidak diizinkan mengubah status aktif atau role dari akun Anda sendiri.');
            }
        }

        // Proteksi 2: Proteksi Admin Terakhir Aktif
        $adminRole = Role::where('slug', 'admin')->first();
        if ($user->role_id == $adminRole->id && $user->status === 'aktif') {
            $jumlahAdminAktif = User::where('role_id', $adminRole->id)->where('status', 'aktif')->count();
            if ($jumlahAdminAktif <= 1 && ($request->status === 'nonaktif' || $request->role_id != $adminRole->id)) {
                return back()->with('eror', 'Tidak dapat menonaktifkan atau mengubah role admin terakhir yang aktif!');
            }
        }

        $data = [
            'name'     => $request->name,
            'username' => $request->username,
            'role_id'  => $request->role_id,
            'status'   => $request->status,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        ActivityLogger::log('ubah_user', "Mengubah data user: {$user->username}");

        return redirect()->route('users.index')->with('sukses', 'Data user berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        // Proteksi Hapus Akun Sendiri
        if (auth()->id() === $user->id) {
            return back()->with('eror', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // Proteksi Hapus Admin Terakhir
        $adminRole = Role::where('slug', 'admin')->first();
        if ($user->role_id == $adminRole->id && $user->status === 'aktif') {
            $jumlahAdminAktif = User::where('role_id', $adminRole->id)->where('status', 'aktif')->count();
            if ($jumlahAdminAktif <= 1) {
                return back()->with('eror', 'Tidak dapat menghapus admin terakhir yang aktif di dalam sistem!');
            }
        }

        $username = $user->username;
        $user->delete();

        ActivityLogger::log('hapus_user', "Menghapus user: {$username}");

        return redirect()->route('users.index')->with('sukses', 'User berhasil dihapus.');
    }

    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8']
        ]);

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        ActivityLogger::log('reset_password', "Reset password untuk user {$user->username}");

        return back()->with('sukses', "Password untuk user {$user->username} berhasil direset.");
    }
    public function transaksiRiwayat(Request $request)
    {
        $userId = $request->input('user_id');
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalSelesai = $request->input('tanggal_selesai');
        $tipeTransaksi = $request->input('tipe_transaksi');
        $quick = $request->input('quick');

        $query = \App\Models\Transaksi::with(['user', 'member']);

        // Logika Quick Filter
        if ($quick) {
            switch ($quick) {
                case 'hari_ini':
                    $tanggalMulai = Carbon::today()->toDateString();
                    $tanggalSelesai = Carbon::today()->toDateString();
                    break;
                case '7_hari':
                    $tanggalMulai = Carbon::today()->subDays(6)->toDateString();
                    $tanggalSelesai = Carbon::today()->toDateString();
                    break;
                case '30_hari':
                    $tanggalMulai = Carbon::today()->subDays(29)->toDateString();
                    $tanggalSelesai = Carbon::today()->toDateString();
                    break;
                case 'bulan_ini':
                    $tanggalMulai = Carbon::today()->startOfMonth()->toDateString();
                    $tanggalSelesai = Carbon::today()->endOfMonth()->toDateString();
                    break;
            }
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }
        if ($tanggalMulai) {
            $query->whereDate('created_at', '>=', $tanggalMulai);
        }
        if ($tanggalSelesai) {
            $query->whereDate('created_at', '<=', $tanggalSelesai);
        }
        if ($tipeTransaksi) {
            $query->where('tipe_transaksi', $tipeTransaksi);
        }

        // Hitung statistik sesuai filter aktif
        $totalTransaksiCount = (clone $query)->count();
        $totalPendapatanSum = (clone $query)->sum('nominal');

        $transaksi = $query->latest()->paginate(10)->withQueryString();
        $users = User::all();

        return view('users.transaksi-riwayat', compact('transaksi', 'users', 'totalTransaksiCount', 'totalPendapatanSum', 'userId', 'tanggalMulai', 'tanggalSelesai', 'tipeTransaksi', 'quick'));
    }
}