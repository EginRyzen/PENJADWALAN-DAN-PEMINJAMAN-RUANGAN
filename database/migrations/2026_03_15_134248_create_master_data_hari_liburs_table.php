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
        Schema::create('master_data_hari_liburs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('periode_id')->constrained('master_data_periodes')->onDelete('cascade');
            $table->date('tanggal')->unique();
            $table->string('keterangan');
            $table->enum('tipe', ['nasional', 'kampus'])->default('nasional');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_data_hari_liburs');
    }
};
