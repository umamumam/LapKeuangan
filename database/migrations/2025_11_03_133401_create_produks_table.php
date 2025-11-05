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
        Schema::create('produks', function (Blueprint $table) {
            $table->id();
            $table->string('sku_induk', 50)->nullable();
            $table->string('nama_produk', 255);
            $table->string('nomor_referensi_sku', 50)->nullable();
            $table->string('nama_variasi', 50)->nullable();
            $table->integer('hpp_produk')->default(0);
            $table->timestamps();
            $table->index(['nama_produk', 'nama_variasi']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produks');
    }
};
