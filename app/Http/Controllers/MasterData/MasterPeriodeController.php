<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreMasterDataPeriode;
use App\Models\MasterDataPeriode;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\DB;

class MasterPeriodeController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = MasterDataPeriode::query();

            if ($request->has('search') && !empty($request->query('search'))) {
                $search = $request->query('search');
                $query->where('nama', 'like', "%{$search}%");
            }

            $size = $request->query('size', 10);
            $page = $request->query('page', 0);

            $paginated = $query->orderBy('start_date', 'desc')
                ->paginate($size, ['*'], 'page', $page + 1);

            $customResponse = [
                'current_page'            => (int) $page,
                'total_pages'             => $paginated->lastPage(),
                'total_elements'          => $paginated->total(),
                'offset_elements'         => ($paginated->currentPage() - 1) * $paginated->perPage(),
                'total_elements_per_page' => $paginated->perPage(),
                'content'                 => $paginated->items(),
            ];

            return $this->successResponse($customResponse, 'Daftar periode berhasil diambil');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500, 'Internal Server Error');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMasterDataPeriode $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            if ($data['status'] === 'Aktif') {
                MasterDataPeriode::where('status', 'Aktif')->update(['status' => 'Non-Aktif']);
            }

            $periode = MasterDataPeriode::create($data);

            DB::commit();
            return $this->successResponse($periode, 'Periode akademik berhasil dibuat', 201, 'Created');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage(), 500, 'Internal Server Error');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $periode = MasterDataPeriode::find($id);

            if (!$periode) {
                return $this->errorResponse('Periode tidak ditemukan', 404, 'Not Found');
            }

            return $this->successResponse($periode, 'Detail periode berhasil diambil');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500, 'Internal Server Error');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreMasterDataPeriode $request, string $id)
    {
        DB::beginTransaction();
        try {
            $periode = MasterDataPeriode::find($id);

            if (!$periode) {
                return $this->errorResponse('Periode tidak ditemukan', 404, 'Not Found');
            }

            $data = $request->validated();

            if ($data['status'] === 'Aktif') {
                MasterDataPeriode::where('status', 'Aktif')->where('id', '!=', $id)->update(['status' => 'Non-Aktif']);
            }

            $periode->update($data);

            DB::commit();
            return $this->successResponse($periode, 'Periode akademik berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage(), 500, 'Internal Server Error');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $periode = MasterDataPeriode::find($id);

            if (!$periode) {
                return $this->errorResponse('Periode tidak ditemukan', 404, 'Not Found');
            }

            $periode->delete();

            return $this->successResponse(null, 'Periode akademik berhasil dihapus');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500, 'Internal Server Error');
        }
    }
}
