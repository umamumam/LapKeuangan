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
        Schema::create('barangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reseller_id')->nullable()->constrained('resellers')->onDelete('cascade');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('cascade');
            $table->string('namabarang');
            $table->string('ukuran')->nullable();
            $table->bigInteger('hpp')->nullable();
            $table->bigInteger('hargabeli_perpotong')->nullable();
            $table->bigInteger('hargabeli_perlusin')->nullable();
            $table->bigInteger('hargajual_perpotong')->nullable();
            $table->bigInteger('hargajual_perlusin')->nullable();
            $table->bigInteger('keuntungan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangs');
    }
};
