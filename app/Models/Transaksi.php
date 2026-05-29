<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = "transaksi";
    protected $fillable = [
        'tipe_transaksi',
        'nama_pelanggan',
        'nominal',
    ];
    
}
