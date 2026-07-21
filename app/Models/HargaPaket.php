<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HargaPaket extends Model
{
    // Ini memberi tahu Laravel bahwa model ini menggunakan tabel harga_pakets
    protected $table = 'harga_pakets';
    
    protected $fillable = ['nama_paket', 'harga'];
}