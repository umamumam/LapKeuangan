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
        Schema::create('pengembalian_penukarans', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->enum('jenis', ['Pengembalian', 'Penukaran', 'Pengembalian Dana']);
            $table->enum('marketplace', ['Tiktok', 'Shopee', 'Reguler']);
            $table->string('resi_penerimaan')->nullable();
            $table->string('resi_pengiriman')->nullable();
            $table->enum('pembayaran', ['Sistem', 'Tunai', 'DFOD']);
            $table->string('nama_pengirim');
            $table->string('no_hp');
            $table->text('alamat');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengembalian_penukarans');
    }
};
