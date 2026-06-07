<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelatih extends Model
{
    use HasFactory;

    protected $table = 'pelatih';
    protected $fillable = ['nama_pelatih', 'nomor_telepon', 'tarif_bulanan', 'tarif_harian', 'status_hadir'];

    // Relasi: Satu pelatih bisa memiliki banyak pengguna
    public function pengguna()
    {
        return $this->hasMany(PenggunaPelatih::class, 'pelatih_id');
    }
}