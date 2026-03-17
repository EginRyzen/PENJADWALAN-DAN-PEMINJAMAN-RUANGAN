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
        Schema::create('pengajuan_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('pengajuan_id')->constrained('pengajuan_ruangans');
            $table->foreignUuid('status_id')->constrained('workflow_steps');
            $table->foreignUuid('user_id')->constrained('users');
            $table->enum('aksi', ['APPROVE', 'REJECT', 'KOREKSI', 'CREATED']);
            $table->text('catatan')->nullable();
            $table->integer('sequence');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_histories');
    }
};
