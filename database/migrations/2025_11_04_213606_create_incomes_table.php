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
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->string('no_pesanan', 100)->unique();
            $table->string('no_pengajuan', 100)->nullable();
            $table->integer('total_penghasilan');
            $table->foreignId('toko_id')->constrained('tokos')->onDelete('cascade');
            $table->timestamps();
            $table->index('no_pesanan');
            $table->index('no_pengajuan');
            $table->index('toko_id');
            $table->enum('marketplace', ['Shopee', 'Tiktok']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incomes');
    }
};
