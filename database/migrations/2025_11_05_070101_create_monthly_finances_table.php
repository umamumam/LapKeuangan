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
        Schema::create('monthly_finances', function (Blueprint $table) {
            $table->id();
            $table->date('periode_awal');
            $table->date('periode_akhir');
            $table->string('nama_periode', 50);
            $table->bigInteger('total_pendapatan')->default(0);
            $table->bigInteger('operasional')->default(0);
            $table->bigInteger('iklan')->default(0);
            $table->decimal('rasio_admin_layanan', 5, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->unique('nama_periode');
            $table->index(['periode_awal', 'periode_akhir']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_finances');
    }
};
