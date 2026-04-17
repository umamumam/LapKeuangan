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
        Schema::table('resellers', function (Blueprint $table) {
            $table->decimal('hutang_awal', 15, 2)->default(0)->after('nama');
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->decimal('hutang_awal', 15, 2)->default(0)->after('nama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resellers', function (Blueprint $table) {
            $table->dropColumn('hutang_awal');
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn('hutang_awal');
        });
    }
};
