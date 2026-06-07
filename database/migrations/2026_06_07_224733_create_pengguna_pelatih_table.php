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
    Schema::create('pengguna_pelatih', function (Blueprint $table) {
        $table->id();
        $table->string('nama_pengguna', 100);
        $table->string('nomor_telepon_pengguna', 15);
        // Relasi foreign key ke tabel pelatih
        $table->foreignId('pelatih_id')->constrained('pelatih')->onDelete('cascade');
        $table->enum('tipe_jasa', ['perbulan', 'perhari']);
        $table->integer('tarif_jasa');
        $table->timestamps();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('pengguna_pelatih');
    }
};
