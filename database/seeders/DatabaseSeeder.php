<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::updateOrCreate(
            ['slug' => 'admin'],
            ['name' => 'Admin']
        );

        $kasirRole = Role::updateOrCreate(
            ['slug' => 'kasir'],
            ['name' => 'Kasir']
        );

        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin',
                'password' => Hash::make('Admin@2026!'),
                'role_id' => $adminRole->id,
            ]
        );

        User::updateOrCreate(
            ['username' => 'kasir'],
            [
                'name' => 'Kasir',
                'password' => Hash::make('Kasir@2026!'),
                'role_id' => $kasirRole->id,
            ]
        );
    }
}