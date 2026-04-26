<?php

namespace App\Http\Controllers;

use App\Exports\BuildingTemplateExport;
use App\Imports\MultiSheetBuildingImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class ExcelImportController extends Controller
{
    public function downloadTemplate()
    {
        return Excel::download(new BuildingTemplateExport, 'template_import_gedung.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            DB::beginTransaction();
            
            Excel::import(new MultiSheetBuildingImport, $request->file('file'));
            
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil diimport'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat import: ' . $e->getMessage()
            ], 500);
        }
    }
}
