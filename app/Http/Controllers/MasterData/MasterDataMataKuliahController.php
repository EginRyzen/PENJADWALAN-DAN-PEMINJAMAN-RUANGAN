<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMasterDataMataKuliah;
use App\Models\MasterDataMataKuliah;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Exports\MataKuliahExport;
use Maatwebsite\Excel\Facades\Excel;

class MasterDataMataKuliahController extends Controller
{
    use ApiResponse;

    /**
     * Export the resource to Excel with multiple sheets per prodi.
     */
    public function export(Request $request)
    {
        return Excel::download(new MataKuliahExport($request->all()), 'daftar_mata_kuliah_per_prodi.xlsx');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = MasterDataMataKuliah::with('programStudi');

            if ($request->has('search') && !empty($request->query('search'))) {
                $search = $request->query('search');
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                      ->orWhere('kode', 'like', "%{$search}%");
                });
            }

            if ($request->has('program_studi_id') && !empty($request->query('program_studi_id'))) {
                $query->where('program_studi_id', $request->query('program_studi_id'));
            }

            // Jika ada query param 'all', kembalikan semua tanpa pagination
            if ($request->boolean('all')) {
                $data = $query->orderBy('nama', 'asc')->get();
                return $this->successResponse($data, 'Daftar mata kuliah berhasil diambil');
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

            return $this->successResponse($customResponse, 'Daftar mata kuliah berhasil diambil');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500, 'Internal Server Error');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMasterDataMataKuliah $request)
    {
        try {
            $mataKuliah = MasterDataMataKuliah::create([
                'kode'             => $request->kode,
                'nama'             => $request->nama,
                'sks'              => $request->sks,
                'semester'         => $request->semester,
                'sks_ujian'        => $request->sks_ujian ?? 0,
                'program_studi_id' => $request->program_studi_id,
            ]);

            return $this->successResponse($mataKuliah->load('programStudi'), 'Mata kuliah berhasil dibuat', 201, 'Created');
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
            $mataKuliah = MasterDataMataKuliah::with('programStudi')->find($id);

            if (!$mataKuliah) {
                return $this->errorResponse('Mata kuliah tidak ditemukan', 404, 'Not Found');
            }

            return $this->successResponse($mataKuliah, 'Detail mata kuliah berhasil diambil');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500, 'Internal Server Error');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreMasterDataMataKuliah $request, $id)
    {
        try {
            $mataKuliah = MasterDataMataKuliah::find($id);

            if (!$mataKuliah) {
                return $this->errorResponse('Mata kuliah tidak ditemukan', 404, 'Not Found');
            }

            $mataKuliah->update([
                'kode'             => $request->kode,
                'nama'             => $request->nama,
                'sks'              => $request->sks,
                'semester'         => $request->semester,
                'sks_ujian'        => $request->sks_ujian ?? 0,
                'program_studi_id' => $request->program_studi_id,
            ]);

            return $this->successResponse($mataKuliah->load('programStudi'), 'Mata kuliah berhasil diperbarui');
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
            $mataKuliah = MasterDataMataKuliah::find($id);

            if (!$mataKuliah) {
                return $this->errorResponse('Mata kuliah tidak ditemukan', 404, 'Not Found');
            }

            $mataKuliah->delete();

            return $this->successResponse(null, 'Mata kuliah berhasil dihapus');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500, 'Internal Server Error');
        }
    }
}
