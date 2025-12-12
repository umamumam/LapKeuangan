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
        Schema::create('periodes', function (Blueprint $table) {
            $table->id();
            $table->string('nama_periode', 100);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->foreignId('toko_id')->constrained('tokos')->onDelete('cascade');
            $table->enum('marketplace', ['Shopee', 'Tiktok']);

            // Jumlah data dari orders
            $table->integer('total_harga_produk')->default(0);
            $table->integer('jumlah_order')->default(0);
            $table->integer('returned_quantity')->default(0);
            $table->integer('total_hpp_produk')->default(0);

            // Data dari incomes
            $table->integer('total_penghasilan')->default(0);
            $table->integer('jumlah_income')->default(0);

            // Data per marketplace (untuk laporan detail)
            $table->integer('total_penghasilan_shopee')->default(0);
            $table->integer('total_income_count_shopee')->default(0);
            $table->integer('total_hpp_shopee')->default(0);

            $table->integer('total_penghasilan_tiktok')->default(0);
            $table->integer('total_income_count_tiktok')->default(0);
            $table->integer('total_hpp_tiktok')->default(0);

            // Status untuk tombol generate
            $table->boolean('is_generated')->default(false);
            $table->datetime('generated_at')->nullable();

            $table->timestamps();

            $table->unique(['nama_periode', 'toko_id', 'marketplace']);
            $table->index(['tanggal_mulai', 'tanggal_selesai']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('periodes');
    }
};
