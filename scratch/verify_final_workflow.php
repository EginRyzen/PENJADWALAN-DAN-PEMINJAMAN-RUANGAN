<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

use App\Models\WorkflowStep;
use Illuminate\Support\Facades\Facade;

$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$steps = WorkflowStep::with('role')->orderBy('tipe_pengajuan')->orderBy('urutan')->get();

echo "All Workflow Steps:\n";
foreach ($steps as $step) {
    echo "Tipe: {$step->tipe_pengajuan}, Urutan: {$step->urutan}, Status: {$step->nama_status}, Role: " . ($step->role ? $step->role->name_role : 'N/A') . "\n";
}
