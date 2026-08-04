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
        Schema::table('members', function (Blueprint $table) {
            $table->softDeletes();
            $table->unique('nomor_telepon');
            $table->index('tanggal_kadaluarsa');
        });

        Schema::table('pelatih', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropUnique(['nomor_telepon']);
            $table->dropIndex(['tanggal_kadaluarsa']);
        });

        Schema::table('pelatih', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
