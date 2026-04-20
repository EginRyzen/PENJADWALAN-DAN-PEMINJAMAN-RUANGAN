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
        Schema::create('menus', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('menu_code', 10)->unique();
            $table->string('menu_name', 100);
            $table->string('menu_id_alias', 50)->nullable();
            $table->text('menu_desc')->nullable();
            $table->integer('sequence')->default(0);
            $table->uuid('parent_id')->nullable();
            $table->boolean('is_desktop')->default(false);
            $table->boolean('is_mobile')->default(false);
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('menus')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};