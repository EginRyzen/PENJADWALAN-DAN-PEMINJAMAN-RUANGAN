<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_ujians', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Konteks jadwal
            $table->foreignUuid('periode_id')->constrained('master_data_periodes')->onDelete('cascade');
            $table->enum('tipe', ['uts', 'uas', 'pembelajaran']);

            // Entitas jadwal
            $table->foreignUuid('mata_kuliah_id')->constrained('master_data_mata_kuliahs')->onDelete('cascade');
            $table->foreignUuid('kelas_id')->constrained('master_data_kelas')->onDelete('cascade');
            $table->foreignUuid('dosen_id')->nullable()->constrained('master_data_dosens')->onDelete('set null');
            $table->foreignUuid('ruangan_id')->nullable()->constrained('data_base_building_rooms')->onDelete('set null');

            // Waktu pelaksanaan
            $table->date('tanggal');
            $table->enum('hari', ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu']);
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->integer('durasi_menit');

            // Status data & konflik
            $table->enum('status_data', ['draft', 'permanen'])->default('draft');
            $table->enum('status_konflik', ['ok', 'conflict', 'edited'])->default('ok');
            $table->text('conflict_reason')->nullable();

            // Metadata
            $table->foreignUuid('generated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignUuid('saved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('saved_at')->nullable();
            $table->timestamp('notified_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_ujians');
    }
};
