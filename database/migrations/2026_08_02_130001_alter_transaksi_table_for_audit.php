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
        Schema::table('transaksi', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('pelatih_id')->constrained('users')->nullOnDelete();
            $table->string('nomor_invoice')->nullable()->after('user_id')->unique();
            $table->string('nama_paket_snapshot')->nullable()->after('nomor_invoice');
            $table->integer('harga_snapshot')->nullable()->after('nama_paket_snapshot');
            $table->enum('status', ['pending', 'paid', 'cancelled', 'refunded'])->default('paid')->after('harga_snapshot');
            $table->softDeletes()->after('updated_at');
        });

        Schema::table('transaksi', function (Blueprint $table) {
            $table->foreign('member_id')->references('id')->on('members')->nullOnDelete();
            $table->foreign('pelatih_id')->references('id')->on('pelatih')->nullOnDelete();
        });

        Schema::table('transaksi', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('tipe_transaksi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropForeign(['member_id']);
            $table->dropForeign(['pelatih_id']);
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'nomor_invoice', 'nama_paket_snapshot', 'harga_snapshot', 'status', 'deleted_at']);
        });
    }
};
