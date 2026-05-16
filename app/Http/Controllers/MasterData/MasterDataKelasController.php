<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\StoreMasterDataKelas;
use App\Models\MasterDataKelas;
use App\Traits\ApiResponse;
use App\Exports\KelasExport;
use Maatwebsite\Excel\Facades\Excel;

class MasterDataKelasController extends Controller
{
    use ApiResponse;

    /**
     * Export the resource to Excel.
     */
    public function export(Request $request)
    {
        return Excel::download(new KelasExport($request->all()), 'daftar_kelas.xlsx');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = MasterDataKelas::with(['programStudi', 'periode']);

            if ($request->has('search') && !empty($request->query('search'))) {
                $search = $request->query('search');
                $query->where(function ($q) use ($search) {
                    $q->where('nama_kelas', 'like', "%{$search}%");
                });
            }

            if ($request->has('program_studi_id') && !empty($request->query('program_studi_id'))) {
                $query->where('program_studi_id', $request->query('program_studi_id'));
            }

            if ($request->boolean('all')) {
                $data = $query->orderBy('nama_kelas', 'asc')->get();
                return $this->successResponse($data, 'Daftar kelas berhasil diambil');
            }

            $size = $request->query('size', 10);
            $page = $request->query('page', 0);

            $paginated = $query->orderBy('nama_kelas', 'asc')
                ->paginate($size, ['*'], 'page', $page + 1);

            $customResponse = [
                'current_page'            => (int) $page,
                'total_pages'             => $paginated->lastPage(),
                'total_elements'          => $paginated->total(),
                'offset_elements'         => ($paginated->currentPage() - 1) * $paginated->perPage(),
                'total_elements_per_page' => $paginated->perPage(),
                'content'                 => $paginated->items(),
            ];

            return $this->successResponse($customResponse, 'Daftar kelas berhasil diambil');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500, 'Internal Server Error');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMasterDataKelas $request)
    {
        try {
            $kelas = MasterDataKelas::create($request->validated());

            return $this->successResponse($kelas->load(['programStudi', 'periode']), 'Kelas berhasil dibuat', 201, 'Created');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500, 'Internal Server Error');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $kelas = MasterDataKelas::with(['programStudi', 'periode'])->find($id);

            if (!$kelas) {
                return $this->errorResponse('Kelas tidak ditemukan', 404, 'Not Found');
            }

            return $this->successResponse($kelas, 'Detail kelas berhasil diambil');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500, 'Internal Server Error');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreMasterDataKelas $request, string $id)
    {
        try {
            $kelas = MasterDataKelas::find($id);

            if (!$kelas) {
                return $this->errorResponse('Kelas tidak ditemukan', 404, 'Not Found');
            }

            $kelas->update($request->validated());

            return $this->successResponse($kelas->load(['programStudi', 'periode']), 'Kelas berhasil diperbarui');
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
            $kelas = MasterDataKelas::find($id);

            if (!$kelas) {
                return $this->errorResponse('Kelas tidak ditemukan', 404, 'Not Found');
            }

            $kelas->delete();

            return $this->successResponse(null, 'Kelas berhasil dihapus');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500, 'Internal Server Error');
        }
    }
}
