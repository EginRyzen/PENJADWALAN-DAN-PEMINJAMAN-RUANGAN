<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = User::with('roles')->where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'timestamp' => Carbon::now()->format('d/m/Y H:i:s'),
                'code' => 401,
                'description' => 'Unauthorized',
                'message' => 'Username atau Password salah.',
                'result' => null
            ], 401);
        }

        if (!$user->is_active) {
            return response()->json([
                'timestamp' => Carbon::now()->format('d/m/Y H:i:s'),
                'code' => 403,
                'description' => 'Forbidden',
                'message' => 'Akun Anda tidak aktif.',
                'result' => null
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'timestamp' => Carbon::now()->format('d/m/Y H:i:s'),
            'code' => 200,
            'description' => 'OK',
            'message' => 'success',
            'result' => [
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'identity_number' => $user->identity_number,
                    'roles' => $user->roles->pluck('name_role'),
                ]
            ]
        ], 200, [], JSON_UNESCAPED_SLASHES);
    }

    public function logout(Request $request)
    {
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
        }

        return response()->json([
            'timestamp' => Carbon::now()->format('d/m/Y H:i:s'),
            'code' => 200,
            'description' => 'OK',
            'message' => 'success',
            'result' => null
        ], 200, [], JSON_UNESCAPED_SLASHES);
    }
    public function me(Request $request)
    {
        $user = $request->user()->load('roles');

        return response()->json([
            'timestamp' => \Illuminate\Support\Carbon::now()->format('d/m/Y H:i:s'),
            'code' => 200,
            'description' => 'OK',
            'message' => 'success',
            'result' => [
                'access_token' => $request->bearerToken(),
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'identity_number' => $user->identity_number,
                    'roles' => $user->roles->pluck('name_role'),
                ]
            ]
        ], 200, [], JSON_UNESCAPED_SLASHES);
    }
}
