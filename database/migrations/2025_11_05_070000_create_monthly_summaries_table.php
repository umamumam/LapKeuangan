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

            // Data dari incomes (berdasarkan created_at)
            $table->bigInteger('total_penghasilan')->default(0); // Sum total_penghasilan
            $table->integer('total_income_count')->default(0); // Count incomes

            // Data HPP (dihitung dari orders)
            $table->bigInteger('total_hpp')->default(0);

            // Laba/Rugi
            $table->bigInteger('laba_rugi')->default(0);

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
