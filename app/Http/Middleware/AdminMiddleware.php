<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            // Ambil user dari token JWT
            $user = JWTAuth::parseToken()->authenticate();
            
            // Jika user tidak ada atau bukan admin, return 401 Unauthorized
            if (!$user || $user->permission !== 'admin') {
                return response()->json([
                    'status' => 401,
                    'error' => 'Unauthorized'
                ], 401);
            }

            return $next($request);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 401,
                'error' => 'Unauthorized'
            ], 401);
        }
    }
}
