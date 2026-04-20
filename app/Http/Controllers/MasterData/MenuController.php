<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMenuRequest;
use App\Models\Menu;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    use ApiResponse;

    /**
     * Get menus assigned to the current user's role for app navigation.
     */
    public function appMenu(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return $this->errorResponse('User tidak terautentikasi', 401, 'Unauthorized');
            }

            // Ambil role pertama dari user (karena sistem menggunakan many-to-many)
            $role = $user->roles()->first();
            if (!$role) {
                return $this->errorResponse('User tidak memiliki role', 403, 'Forbidden');
            }

            $roleId = $role->id;

            // Ambil ID menu yang di-assign ke role ini
            $assignedMenuIds = DB::table('role_menus')
                ->where('role_id', $roleId)
                ->pluck('menu_id');

            // Ambil struktur tree hanya untuk menu yang di-assign
            $menus = Menu::whereNull('parent_id')
                ->whereIn('id', $assignedMenuIds)
                ->with(['children' => function($q) use ($assignedMenuIds) {
                    $q->whereIn('id', $assignedMenuIds)
                      ->with(['children' => function($q2) use ($assignedMenuIds) {
                          $q2->whereIn('id', $assignedMenuIds)
                             ->orderBy('sequence', 'asc');
                      }])
                      ->orderBy('sequence', 'asc');
                }])
                ->orderBy('sequence', 'asc')
                ->get();

            return $this->successResponse($menus, 'Menu navigasi berhasil diambil');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500, 'Internal Server Error');
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Menu::query()->with('children');

            if ($request->has('search') && !empty($request->query('search'))) {
                $search = $request->query('search');
                $query->where(function($q) use ($search) {
                    $q->where('menu_name', 'like', "%{$search}%")
                      ->orWhere('menu_code', 'like', "%{$search}%");
                });
            }

            // If tree view requested
            if ($request->query('tree')) {
                $menus = Menu::whereNull('parent_id')
                    ->with(['children.children' => function($q) {
                        $q->orderBy('sequence', 'asc');
                    }])
                    ->orderBy('sequence', 'asc')
                    ->get();
                return $this->successResponse($menus, 'Daftar menu (tree) berhasil diambil');
            }

            $size = $request->query('size', 100);
            $page = $request->query('page', 0);

            $paginated = $query->orderBy('sequence', 'asc')
                ->paginate($size, ['*'], 'page', $page + 1);

            $customResponse = [
                'current_page'            => (int) $page,
                'total_pages'             => $paginated->lastPage(),
                'total_elements'          => $paginated->total(),
                'offset_elements'         => ($paginated->currentPage() - 1) * $paginated->perPage(),
                'total_elements_per_page' => $paginated->perPage(),
                'content'                 => $paginated->items(),
            ];

            return $this->successResponse($customResponse, 'Daftar menu berhasil diambil');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500, 'Internal Server Error');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMenuRequest $request)
    {
        DB::beginTransaction();
        try {
            $menu = Menu::create($request->validated());
            DB::commit();
            return $this->successResponse($menu, 'Menu berhasil ditambahkan', 201, 'Created');
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
            $menu = Menu::with('children', 'parent')->find($id);

            if (!$menu) {
                return $this->errorResponse('Menu tidak ditemukan', 404, 'Not Found');
            }

            return $this->successResponse($menu, 'Detail menu berhasil diambil');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500, 'Internal Server Error');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreMenuRequest $request, string $id)
    {
        DB::beginTransaction();
        try {
            $menu = Menu::find($id);

            if (!$menu) {
                return $this->errorResponse('Menu tidak ditemukan', 404, 'Not Found');
            }

            $menu->update($request->validated());

            DB::commit();
            return $this->successResponse($menu, 'Menu berhasil diperbarui');
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
            $menu = Menu::find($id);

            if (!$menu) {
                return $this->errorResponse('Menu tidak ditemukan', 404, 'Not Found');
            }

            $menu->delete();

            return $this->successResponse(null, 'Menu berhasil dihapus');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500, 'Internal Server Error');
        }
    }
}
