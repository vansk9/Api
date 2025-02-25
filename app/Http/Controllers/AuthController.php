<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // LOGIN
    public function login(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'email'     => 'required|email',
            'password'  => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Ambil credentials dari request
        $credentials = $request->only('email', 'password');

        // Coba melakukan autentikasi
        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Ambil user yang berhasil login
        $user = auth()->user();

        // Tambahkan permission ke token
        $token = JWTAuth::fromUser($user, [
            'permission' => $user->permission
        ]);

        return response()->json([
            'success'   => true,
            'message'   => 'Login successful',
            'user'      => $user,
            'token'     => $token,
        ]);
    }

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
                'id' => 'required|string|exists:users,id'
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
