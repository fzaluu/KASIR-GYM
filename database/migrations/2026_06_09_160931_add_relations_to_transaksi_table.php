<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            // Cek dulu apakah kolom sudah ada, kalau belum otomatis dibuatkan
            if (!Schema::hasColumn('transaksi', 'member_id')) {
                $table->unsignedBigInteger('member_id')->nullable()->after('tipe_transaksi');
            }
            if (!Schema::hasColumn('transaksi', 'pelatih_id')) {
                $table->unsignedBigInteger('pelatih_id')->nullable()->after('member_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropColumn(['member_id', 'pelatih_id']);
        });
    }
};