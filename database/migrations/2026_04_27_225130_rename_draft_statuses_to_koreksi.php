<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\WorkflowStep;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        WorkflowStep::where('nama_status', 'DRAFT_PENGAJUAN')->update(['nama_status' => 'Koreksi']);
        WorkflowStep::where('nama_status', 'DRAFT_PENGAJUAN_DOSEN')->update(['nama_status' => 'Koreksi']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We can't perfectly reverse it because we merged two names into one,
        // but we can try based on tipe_pengajuan if we really wanted to.
        WorkflowStep::where('nama_status', 'Koreksi')->where('tipe_pengajuan', 'PEMBELAJARAN')->update(['nama_status' => 'DRAFT_PENGAJUAN']);
        WorkflowStep::where('nama_status', 'Koreksi')->where('tipe_pengajuan', 'EVENT')->update(['nama_status' => 'DRAFT_PENGAJUAN_DOSEN']);
    }
};
