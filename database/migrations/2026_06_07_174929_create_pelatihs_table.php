<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('pelatih', function (Blueprint $table) {
        $table->id();
        $table->string('nama_pelatih', 100);
        $table->string('nomor_telepon', 15);
        $table->integer('tarif_bulanan')->default(200000);
        $table->integer('tarif_harian')->default(20000);
        // Status absensi harian pelatih
        $table->enum('status_hadir', ['hadir', 'tidak_hadir'])->default('hadir');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelatihs');
    }
};
