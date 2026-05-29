<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $table = 'members';

    protected $fillable = ['nama_member', 'nomor_telepon', 'tanggal_kadaluarsa'];

    // Relasi ke model AbsensiMember
    public function absensi()
    {
        return $this->hasMany(AbsensiMember::class, 'member_id');
    }
}   