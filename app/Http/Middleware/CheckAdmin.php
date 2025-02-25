<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Ambil user dari token JWT
        $user = JWTAuth::parseToken()->authenticate();

        // Periksa apakah user memiliki permission admin
        if (!$user || $user->permission !== 'admin') {
            return response()->json([
                'error'   => true,
                'message' => 'Unauthorized. Admin access only.',
            ], 403);
        }

        return $next($request);
    }
}
