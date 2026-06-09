<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    // Mengunci nama tabel yang ada di database MySQL kamu
    protected $table = "transaksi";

    // Mendaftarkan kolom yang boleh diisi secara massal (Mass Assignment)
    // Di sini kita tambahkan member_id dan pelatih_id sebagai kolom relasi baru
    protected $fillable = [
        'tipe_transaksi',
        'member_id',
        'pelatih_id',
        'nama_pelanggan',
        'nominal',
    ];

    // Hubungan Relasi: Setiap transaksi ini dimiliki oleh satu Member (jika ada)
    public function member()
    {
        // belongsTo artinya tabel transaksi bertindak sebagai tabel anak yang menyimpan member_id
        return $this->belongsTo(Member::class, 'member_id');
    }

    // Hubungan Relasi: Setiap transaksi ini juga bisa terikat ke satu Pelatih (jika ada sewa PT)
    public function pelatih()
    {
        // belongsTo artinya tabel transaksi terhubung ke master data di tabel pelatih
        return $this->belongsTo(Pelatih::class, 'pelatih_id');
    }
}