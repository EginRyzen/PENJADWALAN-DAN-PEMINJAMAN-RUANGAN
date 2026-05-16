<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\StoreMasterDataDosen;
use App\Models\MasterDataDosen;
use App\Traits\ApiResponse;
use App\Exports\DosenExport;
use Maatwebsite\Excel\Facades\Excel;

class MasterDataDosenController extends Controller
{
    use ApiResponse;

    /**
     * Export the resource to Excel.
     */
    public function export(Request $request)
    {
        return Excel::download(new DosenExport($request->all()), 'daftar_dosen.xlsx');
    }


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = MasterDataDosen::with('programStudi');

            if ($request->has('search') && !empty($request->query('search'))) {
                $search = $request->query('search');
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                      ->orWhere('nidn', 'like', "%{$search}%")
                      ->orWhere('nip', 'like', "%{$search}%");
                });
            }

            if ($request->has('program_studi_id') && !empty($request->query('program_studi_id'))) {
                $query->where('program_studi_id', $request->query('program_studi_id'));
            }

            if ($request->has('status') && !empty($request->query('status'))) {
                $query->where('status', $request->query('status'));
            }

            // Jika ada query param 'all', kembalikan semua tanpa pagination
            if ($request->boolean('all')) {
                $data = $query->orderBy('nama', 'asc')->get();
                return $this->successResponse($data, 'Daftar dosen berhasil diambil');
            }

            $size = $request->query('size', 10);
            $page = $request->query('page', 0);

            $paginated = $query->orderBy('nama', 'asc')
                ->paginate($size, ['*'], 'page', $page + 1);

            $customResponse = [
                'current_page'            => (int) $page,
                'total_pages'             => $paginated->lastPage(),
                'total_elements'          => $paginated->total(),
                'offset_elements'         => ($paginated->currentPage() - 1) * $paginated->perPage(),
                'total_elements_per_page' => $paginated->perPage(),
                'content'                 => $paginated->items(),
            ];

            return $this->successResponse($customResponse, 'Daftar dosen berhasil diambil');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500, 'Internal Server Error');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMasterDataDosen $request)
    {
        try {
            $dosen = MasterDataDosen::create($request->validated());

            return $this->successResponse($dosen->load('programStudi'), 'Dosen berhasil dibuat', 201, 'Created');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500, 'Internal Server Error');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $dosen = MasterDataDosen::with('programStudi')->find($id);

            if (!$dosen) {
                return $this->errorResponse('Dosen tidak ditemukan', 404, 'Not Found');
            }

            return $this->successResponse($dosen, 'Detail dosen berhasil diambil');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500, 'Internal Server Error');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreMasterDataDosen $request, $id)
    {
        try {
            $dosen = MasterDataDosen::find($id);

            if (!$dosen) {
                return $this->errorResponse('Dosen tidak ditemukan', 404, 'Not Found');
            }

            $dosen->update($request->validated());

            return $this->successResponse($dosen->load('programStudi'), 'Dosen berhasil diperbarui');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500, 'Internal Server Error');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $dosen = MasterDataDosen::find($id);

            if (!$dosen) {
                return $this->errorResponse('Dosen tidak ditemukan', 404, 'Not Found');
            }

            $dosen->delete();

            return $this->successResponse(null, 'Dosen berhasil dihapus');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500, 'Internal Server Error');
        }
    }
}
