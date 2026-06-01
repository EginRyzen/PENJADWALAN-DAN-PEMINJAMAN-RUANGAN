<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Exports\UserExport;
use Maatwebsite\Excel\Facades\Excel;

class MasterDataUserController extends Controller
{
    use ApiResponse;

    /**
     * Export the resource to Excel.
     */
    public function export(Request $request)
    {
        return Excel::download(new UserExport($request->all()), 'daftar_user.xlsx');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = User::with('roles');

            if ($request->has('search') && !empty($request->query('search'))) {
                $search = $request->query('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            if ($request->has('status') && !empty($request->query('status'))) {
                $isActive = $request->query('status') === 'Aktif' ? 1 : 0;
                $query->where('is_active', $isActive);
            }

            $sortBy = $request->query('sort_by', 'name');
            $sortDir = $request->query('sort_dir', 'asc');
            
            $allowedSorts = ['name', 'email', 'is_active'];
            if (in_array($sortBy, $allowedSorts)) {
                $query->orderBy("users.{$sortBy}", $sortDir === 'desc' ? 'desc' : 'asc');
            } else {
                $query->orderBy('users.name', 'asc');
            }

            if ($request->boolean('all')) {
                $data = $query->get();
                return $this->successResponse($data, 'Daftar user berhasil diambil');
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

            return $this->successResponse($customResponse, 'Daftar user berhasil diambil');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500, 'Internal Server Error');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|string',
            'status' => 'required|string',
        ]);

        try {
            $user = User::create([
                'name' => $validated['name'],
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'is_active' => $validated['status'] === 'Aktif' ? 1 : 0,
            ]);
            
            $role = Role::where('name_role', $validated['role'])->first();
            if ($role) {
                $user->roles()->attach($role->id);
            }

            return $this->successResponse($user->load('roles'), 'User berhasil dibuat', 201, 'Created');
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
            $user = User::with('roles')->find($id);

            if (!$user) {
                return $this->errorResponse('User tidak ditemukan', 404, 'Not Found');
            }

            return $this->successResponse($user, 'Detail user berhasil diambil');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500, 'Internal Server Error');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($id)],
            'password' => 'nullable|string|min:6',
            'role' => 'required|string',
            'status' => 'required|string',
        ]);

        try {
            $user = User::find($id);

            if (!$user) {
                return $this->errorResponse('User tidak ditemukan', 404, 'Not Found');
            }

            $updateData = [
                'name' => $validated['name'],
                'username' => $validated['username'],
                'email' => $validated['email'],
                'is_active' => $validated['status'] === 'Aktif' ? 1 : 0,
            ];
            
            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $user->update($updateData);

            $role = Role::where('name_role', $validated['role'])->first();
            if ($role) {
                $user->roles()->sync([$role->id]);
            }

            return $this->successResponse($user->load('roles'), 'User berhasil diperbarui');
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
            $user = User::find($id);

            if (!$user) {
                return $this->errorResponse('User tidak ditemukan', 404, 'Not Found');
            }

            $user->roles()->detach();
            $user->delete();

            return $this->successResponse(null, 'User berhasil dihapus');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500, 'Internal Server Error');
        }
    }
}
