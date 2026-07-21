<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('harga_pakets', function (Blueprint $table) {
            $table->id();
            $table->string('nama_paket'); // Contoh: 'Harian', 'Perpanjang', 'Member Baru'
            $table->integer('harga');      // Contoh: 8000, 100000
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('harga_pakets');
    }
};
