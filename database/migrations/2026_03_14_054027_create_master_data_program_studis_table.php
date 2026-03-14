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
        Schema::create('master_data_program_studis', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode', 20)->unique();
            $table->string('nama', 100);
            $table->string('fakultas', 100)->default('Kampus 5 PSDKU');
            $table->enum('jenjang', ['D3', 'D4', 'S1', 'S2', 'S3']);
            $table->enum('status', ['aktif', 'non-aktif'])->default('aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_data_program_studis');
    }
};
