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
        Schema::create('master_data_dosens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nidn')->unique();
            $table->string('nip')->unique()->nullable();
            $table->string('nama');
            $table->foreignUuid('program_studi_id')->constrained('master_data_program_studis')->onDelete('cascade');
            $table->string('jabatan');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_data_dosens');
    }
};
