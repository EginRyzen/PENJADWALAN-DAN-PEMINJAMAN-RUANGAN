<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMasterDataHariLibur;
use App\Models\MasterDataHariLibur;
use App\Models\MasterDataPeriode;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MasterDataHariLiburController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = MasterDataHariLibur::query()->with('periode');

            if ($request->has('search') && !empty($request->query('search'))) {
                $search = $request->query('search');
                $query->where('keterangan', 'like', "%{$search}%");
            }

            if ($request->has('year') && !empty($request->query('year'))) {
                $year = $request->query('year');
                $query->whereYear('tanggal', $year);
            }

            $size = $request->query('size', 100);
            $page = $request->query('page', 0);

            $paginated = $query->orderBy('tanggal', 'asc')
                ->paginate($size, ['*'], 'page', $page + 1);

            $customResponse = [
                'current_page'            => (int) $page,
                'total_pages'             => $paginated->lastPage(),
                'total_elements'          => $paginated->total(),
                'offset_elements'         => ($paginated->currentPage() - 1) * $paginated->perPage(),
                'total_elements_per_page' => $paginated->perPage(),
                'content'                 => $paginated->items(),
            ];

            return $this->successResponse($customResponse, 'Daftar hari libur berhasil diambil');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500, 'Internal Server Error');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMasterDataHariLibur $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            if (empty($data['periode_id'])) {
                $activePeriode = MasterDataPeriode::where('status', 'Aktif')->first();
                if (!$activePeriode) {
                    return $this->errorResponse('Tidak ada periode aktif yang ditemukan. Harap tentukan periode_id atau aktifkan satu periode.', 422, 'Unprocessable Entity');
                }
                $data['periode_id'] = $activePeriode->id;
            }

            $hariLibur = MasterDataHariLibur::create($data);

            DB::commit();
            return $this->successResponse($hariLibur, 'Hari libur berhasil ditambahkan', 201, 'Created');
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
            $hariLibur = MasterDataHariLibur::with('periode')->find($id);

            if (!$hariLibur) {
                return $this->errorResponse('Hari libur tidak ditemukan', 404, 'Not Found');
            }

            return $this->successResponse($hariLibur, 'Detail hari libur berhasil diambil');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500, 'Internal Server Error');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreMasterDataHariLibur $request, string $id)
    {
        DB::beginTransaction();
        try {
            $hariLibur = MasterDataHariLibur::find($id);

            if (!$hariLibur) {
                return $this->errorResponse('Hari libur tidak ditemukan', 404, 'Not Found');
            }

            $data = $request->validated();

            if (empty($data['periode_id'])) {
                $activePeriode = MasterDataPeriode::where('status', 'Aktif')->first();
                if ($activePeriode) {
                    $data['periode_id'] = $activePeriode->id;
                }
            }

            $hariLibur->update($data);

            DB::commit();
            return $this->successResponse($hariLibur, 'Hari libur berhasil diperbarui');
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
            $hariLibur = MasterDataHariLibur::find($id);

            if (!$hariLibur) {
                return $this->errorResponse('Hari libur tidak ditemukan', 404, 'Not Found');
            }

            $hariLibur->delete();

            return $this->successResponse(null, 'Hari libur berhasil dihapus');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500, 'Internal Server Error');
        }
    }
}
