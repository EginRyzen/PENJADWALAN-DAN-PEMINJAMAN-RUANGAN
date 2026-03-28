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
        Schema::create('pengajuan_ruangans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('no_pengajuan')->unique();
            $table->enum('tipe_pengajuan', ['PEMBELAJARAN', 'EVENT']);
            $table->foreignUuid('current_status_id')->constrained('workflow_steps');
            $table->foreignUuid('user_id')->constrained('users');
            $table->date('tanggal_pengajuan');
            $table->date('tanggal_start_peminjaman');
            $table->date('tanggal_end_peminjaman');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->text('alasan')->nullable();
            $table->foreignUuid('dokumen_pendukung_id')->nullable()->constrained('data_documents');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_ruangans');
    }
};
