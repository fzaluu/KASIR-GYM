<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HargaPaketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\HargaPaket::updateOrCreate(
            ['nama_paket' => 'harian'],
            ['harga' => 8000]
        );

        \App\Models\HargaPaket::updateOrCreate(
            ['nama_paket' => 'checkin'],
            ['harga' => 0]
        );

        \App\Models\HargaPaket::updateOrCreate(
            ['nama_paket' => 'perpanjang'],
            ['harga' => 100000]
        );

        \App\Models\HargaPaket::updateOrCreate(
            ['nama_paket' => 'member'],
            ['harga' => 10000]
        );
    }
}
