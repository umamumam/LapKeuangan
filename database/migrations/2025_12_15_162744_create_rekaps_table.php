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
        Schema::create('rekaps', function (Blueprint $table) {
            $table->id();
            $table->enum('nama_periode', ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']);
            $table->year('tahun');
            $table->foreignId('toko_id')->constrained('tokos')->onDelete('cascade');
            $table->bigInteger('total_penghasilan_shopee')->default(0);
            $table->bigInteger('total_penghasilan_tiktok')->default(0);
            $table->bigInteger('total_hpp_shopee')->default(0);
            $table->bigInteger('total_hpp_tiktok')->default(0);
            $table->bigInteger('total_iklan_shopee')->default(0);
            $table->bigInteger('total_iklan_tiktok')->default(0);
            $table->bigInteger('operasional')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekaps');
    }
};
