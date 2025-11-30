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
        Schema::create('pengiriman_sampels', function (Blueprint $table) {
            $table->id();
            $table->datetime('tanggal');
            $table->string('username');
            $table->integer('jumlah');
            $table->string('no_resi');
            $table->integer('ongkir');
            $table->foreignId('sampel_id')->constrained('sampels')->onDelete('cascade');
            $table->integer('totalhpp');
            $table->integer('total_biaya');
            $table->string('penerima');
            $table->string('contact');
            $table->text('alamat');
            $table->timestamps();

            // Indexes
            $table->index('tanggal');
            $table->index('no_resi');
            $table->index('username');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengiriman_sampels');
    }
};
