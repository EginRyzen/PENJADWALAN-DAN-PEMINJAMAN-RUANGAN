<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->uuid('notifiable_id');
            $table->string('notifiable_type');

            // Data konten notifikasi dalam format JSON agar fleksibel
            $table->json('data');

            // Waktu kapan notifikasi dibaca oleh user (null jika belum dibaca)
            $table->timestamp('read_at')->nullable();
            
            $table->timestamps();

            // Index untuk mempercepat pencarian notifikasi per user
            $table->index(['notifiable_id', 'notifiable_type']);
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};