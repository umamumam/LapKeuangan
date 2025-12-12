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
            $table->string('no_pesanan', 100);
            $table->string('no_pengajuan', 100)->nullable();
            $table->integer('total_penghasilan');
            $table->foreignId('periode_id')->nullable()->constrained('periodes')->nullOnDelete();
            $table->timestamps();
            $table->index('no_pesanan');
            $table->index('no_pengajuan');
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
