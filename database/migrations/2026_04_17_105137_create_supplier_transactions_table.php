<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->date('tgl');
            $table->integer('total_barang')->default(0);
            $table->bigInteger('total_uang')->default(0);
            $table->bigInteger('bayar')->default(0);
            $table->bigInteger('total_tagihan')->default(0);
            $table->integer('retur')->default(0);
            $table->string('bukti_tf')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_transactions');
    }
};
