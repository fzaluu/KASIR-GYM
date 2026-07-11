<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin Virgo',
            'username' => 'admin',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);
    }
}