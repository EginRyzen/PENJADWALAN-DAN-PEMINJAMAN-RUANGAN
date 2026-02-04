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
        Schema::create('data_base_buildings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('building_name')->unique();
            $table->string('building_code')->unique();
            $table->string('building_location');
            $table->enum('building_status', ['active', 'inactive'])->default('inactive');
            $table->foreignUuid('building_image_id')->nullable()->constrained('data_documents')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_base_buildings');
    }
};
