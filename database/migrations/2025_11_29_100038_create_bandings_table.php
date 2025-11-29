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
        Schema::create('bandings', function (Blueprint $table) {
            $table->id();
            $table->datetime('tanggal');
            $table->enum('status_banding', ['Berhasil', 'Ditinjau', 'Ditolak']);
            $table->enum('ongkir', ['Dibebaskan', 'Ditanggung', '-']);
            $table->string('no_resi')->nullable();
            $table->string('no_pesanan');
            $table->string('no_pengajuan')->nullable();
            $table->enum('alasan', [
                'Barang Palsu',
                'Tidak Sesuai Ekspektasi Pembeli',
                'Barang Belum Diterima',
                'Cacat',
                'Jumlah Barang Retur Kurang',
                'Bukan Produk Asli Toko'
            ]);
            $table->string('username');
            $table->string('nama_pengirim');
            $table->string('no_hp')->nullable();
            $table->text('alamat');
            $table->enum('marketplace', ['Shopee', 'Tiktok']);
            $table->timestamps();

            // Indexes
            $table->index('tanggal');
            $table->index('no_pesanan');
            $table->index('no_pengajuan');
            $table->index('status_banding');
            $table->index('marketplace');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bandings');
    }
};
