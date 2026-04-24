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
        // Hapus tabel lama
        Schema::dropIfExists('supplier_transaction_details');
        Schema::dropIfExists('supplier_payments');
        Schema::dropIfExists('supplier_transactions');

        // Buat tabel transaksi langsung dengan format excel
        Schema::create('supplier_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->date('tanggal');
            $table->bigInteger('sisa_nota')->nullable()->comment('Manual input sisa nota sebelumnya');
            $table->float('lusin')->default(0);
            $table->float('potong')->default(0);
            $table->string('nama_barang')->nullable();
            $table->bigInteger('harga')->default(0);
            $table->bigInteger('jumlah')->default(0);
            $table->bigInteger('tf')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_transactions');
    }
};
