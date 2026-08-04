<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pelatih extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pelatih';

    protected $fillable = ['nama_pelatih', 'nomor_telepon', 'tarif_bulanan', 'tarif_harian', 'status_hadir'];

    public function pengguna()
    {
        return $this->hasMany(PenggunaPelatih::class, 'pelatih_id');
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'pelatih_id');
    }
}