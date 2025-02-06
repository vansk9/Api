<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // GET User dari token
    public function getUser(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json(['message' => 'User tidak ditemukan'], 404);
            }
            return response()->json(['user' => $user], 200);
        } catch (JWTException $e) {
            return response()->json(['message' => 'Token tidak valid atau kedaluwarsa'], 401);
        }
    }

    // DELETE User dengan validasi token
    public function deleteUser(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $validator = Validator::make($request->all(), [
                'id' => 'required|string|exists:users,_id'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }

            $userToDelete = User::find($request->id);
            if (!$userToDelete) {
                return response()->json(['message' => 'User tidak ditemukan'], 404);
            }

            $userToDelete->delete();
            return response()->json(['message' => 'User berhasil dihapus'], 200);
        } catch (JWTException $e) {
            return response()->json(['message' => 'Token tidak valid atau kedaluwarsa'], 401);
        }
    }

    // LOGOUT
    public function logout(Request $request)
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json(['message' => 'Logout berhasil'], 200);
        } catch (JWTException $e) {
            return response()->json(['message' => 'Gagal logout, token tidak valid'], 500);
        }
    }
}
