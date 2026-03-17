<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMasterDataMahasiswa;
use App\Models\MasterDataMahasiswa;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class MasterDataMahasiswaController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = MasterDataMahasiswa::with('programStudi');

            if ($request->has('search') && !empty($request->query('search'))) {
                $search = $request->query('search');
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                      ->orWhere('nim', 'like', "%{$search}%");
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
                return $this->successResponse($data, 'Daftar mahasiswa berhasil diambil');
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

            return $this->successResponse($customResponse, 'Daftar mahasiswa berhasil diambil');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500, 'Internal Server Error');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMasterDataMahasiswa $request)
    {
        try {
            $mahasiswa = MasterDataMahasiswa::create($request->validated());

            return $this->successResponse($mahasiswa->load('programStudi'), 'Mahasiswa berhasil dibuat', 201, 'Created');
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
            $mahasiswa = MasterDataMahasiswa::with('programStudi')->find($id);

            if (!$mahasiswa) {
                return $this->errorResponse('Mahasiswa tidak ditemukan', 404, 'Not Found');
            }

            return $this->successResponse($mahasiswa, 'Detail mahasiswa berhasil diambil');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500, 'Internal Server Error');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreMasterDataMahasiswa $request, $id)
    {
        try {
            $mahasiswa = MasterDataMahasiswa::find($id);

            if (!$mahasiswa) {
                return $this->errorResponse('Mahasiswa tidak ditemukan', 404, 'Not Found');
            }

            $mahasiswa->update($request->validated());

            return $this->successResponse($mahasiswa->load('programStudi'), 'Mahasiswa berhasil diperbarui');
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
            $mahasiswa = MasterDataMahasiswa::find($id);

            if (!$mahasiswa) {
                return $this->errorResponse('Mahasiswa tidak ditemukan', 404, 'Not Found');
            }

            $mahasiswa->delete();

            return $this->successResponse(null, 'Mahasiswa berhasil dihapus');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500, 'Internal Server Error');
        }
    }
}
