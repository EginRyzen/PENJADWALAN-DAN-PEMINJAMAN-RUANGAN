<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMasterDataProgramStudi;
use App\Models\MasterDataProgramStudi;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Exports\ProgramStudiExport;
use Maatwebsite\Excel\Facades\Excel;

class MasterDataProgramStudiController extends Controller
{
    use ApiResponse;

    /**
     * Export the resource to Excel.
     */
    public function export(Request $request)
    {
        return Excel::download(new ProgramStudiExport($request->all()), 'daftar_program_studi.xlsx');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = MasterDataProgramStudi::query();

            if ($request->has('search') && !empty($request->query('search'))) {
                $search = $request->query('search');
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                      ->orWhere('kode', 'like', "%{$search}%")
                      ->orWhere('jenjang', 'like', "%{$search}%");
                });
            }

            if ($request->has('status') && !empty($request->query('status'))) {
                $query->where('status', $request->query('status'));
            }

            if ($request->has('jenjang') && !empty($request->query('jenjang'))) {
                $query->where('jenjang', $request->query('jenjang'));
            }

            $sortBy = $request->query('sort_by', 'nama');
            $sortDir = $request->query('sort_dir', 'asc');
            $allowedSorts = ['kode', 'nama', 'fakultas', 'jenjang', 'status'];

            if (in_array($sortBy, $allowedSorts)) {
                $query->orderBy($sortBy, $sortDir === 'desc' ? 'desc' : 'asc');
            } else {
                $query->orderBy('nama', 'asc');
            }

            // Jika ada query param 'all', kembalikan semua tanpa pagination
            if ($request->boolean('all')) {
                $data = $query->get();
                return $this->successResponse($data, 'Daftar program studi berhasil diambil');
            }

            $size = $request->query('size', 10);
            $page = $request->query('page', 0);

            $paginated = $query->paginate($size, ['*'], 'page', $page + 1);

            $customResponse = [
                'current_page'            => (int) $page,
                'total_pages'             => $paginated->lastPage(),
                'total_elements'          => $paginated->total(),
                'offset_elements'         => ($paginated->currentPage() - 1) * $paginated->perPage(),
                'total_elements_per_page' => $paginated->perPage(),
                'content'                 => $paginated->items(),
            ];

            return $this->successResponse($customResponse, 'Daftar program studi berhasil diambil');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500, 'Internal Server Error');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMasterDataProgramStudi $request)
    {
        try {
            $programStudi = MasterDataProgramStudi::create([
                'kode'     => $request->kode,
                'nama'     => $request->nama,
                'fakultas' => $request->fakultas ?? 'Kampus 5 PSDKU',
                'jenjang'  => $request->jenjang,
                'status'   => $request->status,
            ]);

            return $this->successResponse($programStudi, 'Program studi berhasil dibuat', 201, 'Created');
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
            $programStudi = MasterDataProgramStudi::find($id);

            if (!$programStudi) {
                return $this->errorResponse('Program studi tidak ditemukan', 404, 'Not Found');
            }

            return $this->successResponse($programStudi, 'Detail program studi berhasil diambil');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500, 'Internal Server Error');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreMasterDataProgramStudi $request, $id)
    {
        try {
            $programStudi = MasterDataProgramStudi::find($id);

            if (!$programStudi) {
                return $this->errorResponse('Program studi tidak ditemukan', 404, 'Not Found');
            }

            $programStudi->update([
                'kode'     => $request->kode,
                'nama'     => $request->nama,
                'fakultas' => $request->fakultas ?? 'Kampus 5 PSDKU',
                'jenjang'  => $request->jenjang,
                'status'   => $request->status,
            ]);

            return $this->successResponse($programStudi, 'Program studi berhasil diperbarui');
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
            $programStudi = MasterDataProgramStudi::find($id);

            if (!$programStudi) {
                return $this->errorResponse('Program studi tidak ditemukan', 404, 'Not Found');
            }

            $programStudi->delete();

            return $this->successResponse(null, 'Program studi berhasil dihapus');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500, 'Internal Server Error');
        }
    }
}
