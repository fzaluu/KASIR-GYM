<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenggunaPelatih extends Model
{
    use HasFactory;

    protected $table = 'pengguna_pelatih';
    protected $fillable = ['nama_pengguna', 'nomor_telepon_pengguna', 'pelatih_id', 'tipe_jasa', 'tarif_jasa'];

    // Relasi: Pengguna ini terikat ke salah satu pelatih
    public function pelatih()
    {
        return $this->belongsTo(Pelatih::class, 'pelatih_id');
    }
}