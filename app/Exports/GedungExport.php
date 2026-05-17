<?php

namespace App\Exports;

use App\Models\DataBaseBuilding;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Sheets\GedungDetailSheet;

class GedungExport implements WithMultipleSheets
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        $query = DataBaseBuilding::with(['rooms.facilities.facility']);

        // Apply filters
        if (!empty($this->filters['ids'])) {
            $ids = is_array($this->filters['ids']) ? $this->filters['ids'] : explode(',', $this->filters['ids']);
            $query->whereIn('id', $ids);
        }

        if (isset($this->filters['active'])) {
            $status = $this->filters['active'] === 'true' ? 'active' : 'inactive';
            $query->where('building_status', $status);
        }

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('building_name', 'like', "%{$search}%")
                    ->orWhere('building_code', 'like', "%{$search}%");
            });
        }

        $buildings = $query->orderBy('building_name', 'asc')->get();

        foreach ($buildings as $building) {
            $sheets[] = new GedungDetailSheet($building);
        }

        return $sheets;
    }
}
