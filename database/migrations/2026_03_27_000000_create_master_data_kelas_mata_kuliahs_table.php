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
        Schema::create('master_data_kelas_mata_kuliahs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('kelas_id')->constrained('master_data_kelas')->onDelete('cascade');
            $table->foreignUuid('mata_kuliah_id')->constrained('master_data_mata_kuliahs')->onDelete('cascade');
            $table->integer('semester'); // Semester aktif untuk kelas ini pada matkul tersebut
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_data_kelas_mata_kuliahs');
    }
};
