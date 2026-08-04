<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use SoftDeletes;

    protected $table = 'members';

    protected $fillable = ['nama_member', 'nomor_telepon', 'tanggal_kadaluarsa','total_checkin'];

    public function absensi()
    {
        return $this->hasMany(AbsensiMember::class, 'member_id');
    }

    public function sudahCheckinHariIni(): bool
    {
        $today = now()->toDateString();

        $checkinTransaksi = Transaksi::where('member_id', $this->id)
            ->where('tipe_transaksi', 'Checkin')
            ->whereDate('created_at', $today)
            ->exists();

        $checkinAbsensi = AbsensiMember::where('member_id', $this->id)
            ->whereDate('created_at', $today)
            ->exists();

        return $checkinTransaksi || $checkinAbsensi;
    }
}