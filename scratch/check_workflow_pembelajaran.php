<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

use App\Models\WorkflowStep;
use Illuminate\Support\Facades\Facade;

$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$steps = WorkflowStep::with('role')->where('tipe_pengajuan', 'PEMBELAJARAN')->orderBy('urutan')->get();

echo "Workflow Steps for PEMBELAJARAN:\n";
foreach ($steps as $step) {
    echo "ID: {$step->id}, Urutan: {$step->urutan}, Nama Status: {$step->nama_status}, Role: " . ($step->role ? $step->role->name_role : 'N/A') . "\n";
}
