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
        Schema::table('data_base_building_rooms', function (Blueprint $table) {
            $table->dropUnique('data_base_building_rooms_room_code_unique');
            $table->unique(['building_id', 'room_code']);
            $table->unique(['building_id', 'room_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_base_building_rooms', function (Blueprint $table) {
            $table->dropUnique(['building_id', 'room_code']);
            $table->dropUnique(['building_id', 'room_name']);
            $table->unique('room_code');
        });
    }
};
