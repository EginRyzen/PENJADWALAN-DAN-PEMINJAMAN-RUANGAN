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
        Schema::create('master_data_mahasiswas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nim')->unique();
            $table->string('nama');
            $table->foreignUuid('program_studi_id')->constrained('master_data_program_studis')->onDelete('cascade');
            $table->foreignUuid('kelas_id')->nullable()->constrained('master_data_kelas')->onDelete('set null');
            $table->integer('semester');
            $table->foreignUuid('periode_id')->constrained('master_data_periodes')->onDelete('cascade');
            $table->string('status'); // Aktif, Non-Aktif, Cuti, Lulus
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_data_mahasiswas');
    }
};
