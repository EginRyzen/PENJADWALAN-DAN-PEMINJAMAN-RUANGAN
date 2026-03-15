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
        Schema::table('master_data_mahasiswas', function (Blueprint $table) {
            if (Schema::hasColumn('master_data_mahasiswas', 'prodi')) {
                $table->dropColumn('prodi');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_data_mahasiswas', function (Blueprint $table) {
            $table->string('prodi')->nullable();
        });
    }
};
