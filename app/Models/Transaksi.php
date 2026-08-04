<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaksi extends Model
{
    use SoftDeletes;

    protected $table = "transaksi";

    protected $fillable = [
        'tipe_transaksi',
        'member_id',
        'pelatih_id',
        'user_id',
        'nomor_invoice',
        'nama_pelanggan',
        'nominal',
        'nama_paket_snapshot',
        'harga_snapshot',
        'status',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $transaksi) {
            if (empty($transaksi->nomor_invoice)) {
                $transaksi->nomor_invoice = 'INV-' . now()->format('Ymd') . '-' . str_pad((string) self::whereDate('created_at', now()->toDateString())->count() + 1, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function pelatih()
    {
        return $this->belongsTo(Pelatih::class, 'pelatih_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}