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
            $table->foreignId('periode_id')->constrained('periodes')->onDelete('cascade');
            $table->bigInteger('total_pendapatan')->default(0);
            $table->bigInteger('operasional')->default(0);
            $table->bigInteger('iklan')->default(0);
            $table->decimal('rasio_admin_layanan', 5, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->unique('periode_id');
            $table->index('periode_id');
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
