<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// 1. Tambahkan 'status' dan 'last_login_at' ke dalam attribute fillable
#[Fillable(['name', 'username', 'password', 'role_id', 'status', 'last_login_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function transaksis()
        {
            return $this->hasMany(Transaksi::class, 'user_id');
        }

    public function activity_logs()
        {
            return $this->hasMany(ActivityLog::class, 'user_id');
        }


    public function isAdmin(): bool
    {
        return $this->role?->slug === 'admin';
    }

    public function isKasir(): bool
    {
        return $this->role?->slug === 'kasir';
    }

    // 2. Tambahkan local scope untuk mengambil user yang aktif saja
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            // 3. Tambahkan casting timestamp untuk last_login_at
            'last_login_at' => 'datetime',
        ];
    }
}