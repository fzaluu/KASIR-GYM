<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsensiMember extends Model
{
    protected $table = 'absensi_members';

    protected $fillable = ['member_id'];

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }
}