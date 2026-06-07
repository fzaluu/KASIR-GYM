<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelatih extends Model
{
    use HasFactory;

    protected $fillable = ['nama_pelatih', 'nomor_telepon', 'tarif_per_bulan'];
}