<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $roleKabag = DB::table('roles')->where('name_role', 'KABAG_UMUM')->first();
        
        if (!$roleKabag) {
            return;
        }

        $types = ['PEMBELAJARAN', 'EVENT'];

        foreach ($types as $type) {
            DB::table('workflow_steps')->insert([
                'id' => Str::uuid(),
                'nama_status' => 'REJECTED',
                'tipe_pengajuan' => $type,
                'role_id' => $roleKabag->id,
                'urutan' => 99, // High number for terminal state
                'is_final' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('workflow_steps')->where('nama_status', 'REJECTED')->delete();
    }
};
