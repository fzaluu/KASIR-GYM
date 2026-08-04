<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HargaPaket extends Model
{
    protected $table = 'harga_pakets';

    protected $fillable = ['nama_paket', 'harga', 'updated_by'];

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}