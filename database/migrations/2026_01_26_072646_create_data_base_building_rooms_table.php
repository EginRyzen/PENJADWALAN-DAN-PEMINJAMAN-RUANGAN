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
        Schema::create('data_base_building_rooms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('building_id')->constrained('data_base_buildings')->onDelete('cascade');
            $table->string('room_name');
            $table->string('room_code')->unique();
            $table->string('room_location');
            $table->enum('room_status', ['active', 'inactive'])->default('inactive');
            $table->integer('room_capacity')->default(0);
            $table->string('room_purpose', 50);
            $table->boolean('can_ujian')->default(false);
            $table->boolean('can_pembelajaran')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_base_building_rooms');
    }
};
