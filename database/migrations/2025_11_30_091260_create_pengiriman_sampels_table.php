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
            $table->string('no_resi');
            $table->integer('ongkir');

            // Kolom untuk sampel 1-5
            $table->foreignId('sampel1_id')->nullable()->constrained('sampels')->onDelete('cascade');
            $table->integer('jumlah1')->default(0);

            $table->foreignId('sampel2_id')->nullable()->constrained('sampels')->onDelete('cascade');
            $table->integer('jumlah2')->default(0);

            $table->foreignId('sampel3_id')->nullable()->constrained('sampels')->onDelete('cascade');
            $table->integer('jumlah3')->default(0);

            $table->foreignId('sampel4_id')->nullable()->constrained('sampels')->onDelete('cascade');
            $table->integer('jumlah4')->default(0);

            $table->foreignId('sampel5_id')->nullable()->constrained('sampels')->onDelete('cascade');
            $table->integer('jumlah5')->default(0);

            // Total
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
