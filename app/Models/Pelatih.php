<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelatih extends Model
{
    use HasFactory;

    // Mengunci nama tabel pelatih sesuai dengan database MySQL kamu
    protected $table = 'pelatih';

    // Kolom-kolom murni master pelatih yang diizinkan untuk diisi massal
    protected $fillable = ['nama_pelatih', 'nomor_telepon', 'tarif_bulanan', 'tarif_harian', 'status_hadir'];

    // Relasi bawaan kamu: Hubungan ke tabel pengguna pelatih
    public function pengguna()
    {
        return $this->hasMany(PenggunaPelatih::class, 'pelatih_id');
    }

    // Hubungan Relasi Baru: Satu pelatih bisa memiliki banyak catatan riwayat transaksi sewa
    public function transaksi()
    {
        // hasMany artinya tabel pelatih bertindak sebagai tabel induk (Parent Table) bagi tabel transaksi
        return $this->hasMany(Transaksi::class, 'pelatih_id');
    }
}