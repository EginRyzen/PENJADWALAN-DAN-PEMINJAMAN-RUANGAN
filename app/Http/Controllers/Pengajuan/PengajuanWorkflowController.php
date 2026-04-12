<?php

namespace App\Http\Controllers\Pengajuan;

use App\Http\Controllers\Controller;
use App\Models\PengajuanHistory;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class PengajuanWorkflowController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the workflow history for a specific pengajuan.
     */
    public function index($id)
    {
        try {
            $data = PengajuanHistory::with(['status.role.users', 'user'])
                ->where('pengajuan_id', $id)
                ->orderBy('sequence', 'asc')
                ->get();

            return $this->successResponse($data, 'Berhasil mengambil data workflow history');
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal mengambil data workflow history: ' . $e->getMessage(), 500, 'Internal Server Error');
        }
    }
}

