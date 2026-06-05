<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleMenuRequest;
use App\Models\Role;
use App\Models\Menu;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\RoleMenuExport;
use Maatwebsite\Excel\Facades\Excel;

class RoleMenuController extends Controller
{
    use ApiResponse;

    /**
     * Export role menu assignments to Excel.
     */
    public function export()
    {
        return Excel::download(new RoleMenuExport(), 'role_menu_access.xlsx');
    }

    /**
     * Display a listing of menus with their assignment status for a specific role.
     */
    public function index(Request $request)
    {
        try {
            $roleId = $request->query('role_id');
            
            if (!$roleId) {
                // If no role_id, maybe return all roles to choose from
                $roles = Role::orderBy('name_role', 'asc')->get();
                return $this->successResponse($roles, 'Daftar role berhasil diambil');
            }

            $role = Role::with('users')->find($roleId);
            if (!$role) {
                return $this->errorResponse('Role tidak ditemukan', 404, 'Not Found');
            }

            // Get all menus and mark those assigned to this role
            $assignedMenuIds = DB::table('role_menus')
                ->where('role_id', $roleId)
                ->pluck('menu_id')
                ->toArray();

            $menus = Menu::orderBy('sequence', 'asc')->get();
            
            $result = [
                'role' => $role,
                'menus' => $menus->map(function($menu) use ($assignedMenuIds) {
                    $menu->is_assigned = in_array($menu->id, $assignedMenuIds);
                    return $menu;
                })
            ];

            return $this->successResponse($result, 'Daftar menu untuk role berhasil diambil');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500, 'Internal Server Error');
        }
    }

    /**
     * Store/Sync menu assignments for a role.
     */
    public function store(StoreRoleMenuRequest $request)
    {
        DB::beginTransaction();
        try {
            $roleId = $request->role_id;
            $menuIds = $request->menu_ids;

            $role = Role::find($roleId);
            if (!$role) {
                return $this->errorResponse('Role tidak ditemukan', 404, 'Not Found');
            }

            // Sync menu assignments
            // Using a simple delete and insert for role_menus since it's a UUID primary key table
            DB::table('role_menus')->where('role_id', $roleId)->delete();

            $data = [];
            foreach ($menuIds as $menuId) {
                $data[] = [
                    'id' => \Illuminate\Support\Str::uuid(),
                    'role_id' => $roleId,
                    'menu_id' => $menuId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (!empty($data)) {
                DB::table('role_menus')->insert($data);
            }

            DB::commit();
            return $this->successResponse(null, 'Hak akses menu berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage(), 500, 'Internal Server Error');
        }
    }
}
