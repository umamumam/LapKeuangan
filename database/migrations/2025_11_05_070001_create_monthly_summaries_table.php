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
        Schema::create('monthly_summaries', function (Blueprint $table) {
            $table->id();
            $table->datetime('periode_awal'); // Tanggal 1 bulan
            $table->datetime('periode_akhir'); // Tanggal akhir bulan
            $table->string('nama_periode', 50); // Format: "Januari 2024"

            // Data dari orders (berdasarkan pesananselesai)
            $table->bigInteger('total_harga_produk')->default(0); // Sum total_harga_produk
            $table->integer('total_order_qty')->default(0); // Sum jumlah
            $table->integer('total_return_qty')->default(0); // Sum returned_quantity

            // Data dari incomes (berdasarkan created_at) - Shopee
            $table->bigInteger('total_penghasilan_shopee')->default(0); // Sum total_penghasilan for Shopee
            $table->integer('total_income_count_shopee')->default(0); // Count incomes for Shopee

            // Data dari incomes (berdasarkan created_at) - Tiktok
            $table->bigInteger('total_penghasilan_tiktok')->default(0); // Sum total_penghasilan for Tiktok
            $table->integer('total_income_count_tiktok')->default(0); // Count incomes for Tiktok

            // Total keseluruhan (untuk backward compatibility)
            $table->bigInteger('total_penghasilan')->default(0); // Sum total_penghasilan all marketplace
            $table->integer('total_income_count')->default(0); // Count incomes all marketplace

            // Data HPP (dihitung dari orders) - dipisah per marketplace
            $table->bigInteger('total_hpp_shopee')->default(0);
            $table->bigInteger('total_hpp_tiktok')->default(0);
            $table->bigInteger('total_hpp')->default(0); // Total keseluruhan

            // Laba/Rugi per marketplace
            $table->bigInteger('laba_rugi_shopee')->default(0);
            $table->bigInteger('laba_rugi_tiktok')->default(0);
            $table->bigInteger('laba_rugi')->default(0); // Total keseluruhan

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->unique('nama_periode');
            $table->index(['periode_awal', 'periode_akhir']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_summaries');
    }
};
