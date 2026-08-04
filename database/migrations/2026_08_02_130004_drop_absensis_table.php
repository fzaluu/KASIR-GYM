<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('absensis');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('absensis', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->onDelete('cascade');
            $table->timestamp('tanggal_checkin');
            $table->enum('status', ['berhasil', 'gagal']);
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }
};
