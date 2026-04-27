<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

use App\Models\WorkflowStep;
use Illuminate\Support\Facades\Facade;

$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Update Dosen track for EVENT
$dosenEventSteps = [
    '790554c9-3629-11f1-9cde-e86a643af6fc' => 11,
    '79055c8b-3629-11f1-9cde-e86a643af6fc' => 12,
    '79055f33-3629-11f1-9cde-e86a643af6fc' => 13,
    '790562ac-3629-11f1-9cde-e86a643af6fc' => 14,
    '790566b5-3629-11f1-9cde-e86a643af6fc' => 15,
];

foreach ($dosenEventSteps as $id => $urutan) {
    WorkflowStep::where('id', $id)->update(['urutan' => $urutan]);
}

// Update Dosen track for PEMBELAJARAN (optional but good for consistency)
$dosenPembelajaranSteps = [
    '78fc6e0d-3629-11f1-9cde-e86a643af6fc' => 21,
    '78fc7d0a-3629-11f1-9cde-e86a643af6fc' => 22,
    '78fc803c-3629-11f1-9cde-e86a643af6fc' => 23,
];

foreach ($dosenPembelajaranSteps as $id => $urutan) {
    WorkflowStep::where('id', $id)->update(['urutan' => $urutan]);
}

echo "Workflow steps updated successfully.\n";
