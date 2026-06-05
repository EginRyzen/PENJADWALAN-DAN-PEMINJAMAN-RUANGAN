<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MasterDataKelasMataKuliah;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Validator;
use App\Exports\KelasMataKuliahExport;
use Maatwebsite\Excel\Facades\Excel;

class MasterDataKelasMataKuliahController extends Controller
{
    use ApiResponse;

    /**
     * Export the resource to Excel.
     */
    public function export(Request $request)
    {
        return Excel::download(new KelasMataKuliahExport($request->all()), 'plotting_kelas_mata_kuliah.xlsx');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = MasterDataKelasMataKuliah::with(['kelas', 'mataKuliah.programStudi']);

            if ($request->has('search') && !empty($request->query('search'))) {
                $search = $request->query('search');
                $query->where(function ($q) use ($search) {
                    $q->whereHas('kelas', function($k) use ($search) {
                        $k->where('nama_kelas', 'like', "%{$search}%");
                    })->orWhereHas('mataKuliah', function($m) use ($search) {
                        $m->where('nama', 'like', "%{$search}%");
                    });
                });
            }

            if ($request->has('kelas_id') && !empty($request->query('kelas_id'))) {
                $query->where('kelas_id', $request->query('kelas_id'));
            }

            $sortBy = $request->query('sort_by', 'created_at');
            $sortDir = $request->query('sort_dir', 'desc');

            if ($sortBy === 'kelas') {
                $query->leftJoin('master_data_kelas as k', 'master_data_kelas_mata_kuliahs.kelas_id', '=', 'k.id')
                      ->orderBy('k.nama_kelas', $sortDir === 'desc' ? 'desc' : 'asc')
                      ->select('master_data_kelas_mata_kuliahs.*');
            } elseif ($sortBy === 'matkul') {
                $query->leftJoin('master_data_mata_kuliahs as m', 'master_data_kelas_mata_kuliahs.mata_kuliah_id', '=', 'm.id')
                      ->orderBy('m.nama', $sortDir === 'desc' ? 'desc' : 'asc')
                      ->select('master_data_kelas_mata_kuliahs.*');
            } else {
                $allowedSorts = ['semester', 'created_at'];
                if (in_array($sortBy, $allowedSorts)) {
                    $query->orderBy("master_data_kelas_mata_kuliahs.{$sortBy}", $sortDir === 'desc' ? 'desc' : 'asc');
                } else {
                    $query->orderBy('master_data_kelas_mata_kuliahs.created_at', 'desc');
                }
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

            return $this->successResponse($customResponse, 'Daftar plotting kelas mata kuliah berhasil diambil');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500, 'Internal Server Error');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kelas_id' => 'required|exists:master_data_kelas,id',
            'mata_kuliah_id' => 'required|exists:master_data_mata_kuliahs,id',
            'semester' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422);
        }

        try {
            // Check for duplicate
            $exists = MasterDataKelasMataKuliah::where('kelas_id', $request->kelas_id)
                ->where('mata_kuliah_id', $request->mata_kuliah_id)
                ->exists();
            
            if ($exists) {
                return $this->errorResponse('Mata kuliah sudah terdaftar di kelas ini', 422);
            }

            $plotting = MasterDataKelasMataKuliah::create($request->all());

            return $this->successResponse($plotting->load(['kelas', 'mataKuliah']), 'Plotting berhasil dibuat', 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500, 'Internal Server Error');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'kelas_id' => 'required|exists:master_data_kelas,id',
            'mata_kuliah_id' => 'required|exists:master_data_mata_kuliahs,id',
            'semester' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422);
        }

        try {
            $plotting = MasterDataKelasMataKuliah::find($id);

            if (!$plotting) {
                return $this->errorResponse('Plotting tidak ditemukan', 404);
            }

            // Check for duplicate except current
            $exists = MasterDataKelasMataKuliah::where('kelas_id', $request->kelas_id)
                ->where('mata_kuliah_id', $request->mata_kuliah_id)
                ->where('id', '!=', $id)
                ->exists();
            
            if ($exists) {
                return $this->errorResponse('Mata kuliah sudah terdaftar di kelas ini', 422);
            }

            $plotting->update($request->all());

            return $this->successResponse($plotting->load(['kelas', 'mataKuliah']), 'Plotting berhasil diperbarui');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500, 'Internal Server Error');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $plotting = MasterDataKelasMataKuliah::find($id);

            if (!$plotting) {
                return $this->errorResponse('Plotting tidak ditemukan', 404);
            }

            $plotting->delete();

            return $this->successResponse(null, 'Plotting berhasil dihapus');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500, 'Internal Server Error');
        }
    }
}
