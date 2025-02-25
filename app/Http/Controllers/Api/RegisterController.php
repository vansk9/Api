<?php

namespace App\Http\Controllers\Api;

// use App\Models\User;
// use Illuminate\Http\Request;
// use App\Http\Controllers\Controller;
// use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

// class RegisterController extends Controller
// {
//     /**
//      * Handle the incoming request.
//      *
//      * @param  \Illuminate\Http\Request  $request
//      * @return \Illuminate\Http\Response
//      */
//     public function __invoke(Request $request)
//     {
//         //set validation
//         $validator = Validator::make($request->all(), [
//             'name'      => 'required',
//             'email'     => 'required|email|unique:users',
//             'password'  => 'required|min:8|confirmed'
//         ]);

//         //if validation fails
//         if ($validator->fails()) {
//             return response()->json($validator->errors(), 422);
//         }

//         //create user
//         $user = User::create([
//             'name'      => $request->name,
//             'email'     => $request->email,
//             'password'  => bcrypt($request->password)
//         ]);

//         //return response JSON user is created
//         if($user) {
//             return response()->json([
//                 'success' => true,
//                 'user'    => $user,  
//             ], 201);
//         }

//         //return JSON process insert failed 
//         return response()->json([
//             'success' => false,
//         ], 409);
//     }
// }

class RegisterController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users',
            'password'  => 'required|min:8|confirmed',
        ]);

        // Jika validasi gagal, kembalikan response error
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Buat user baru dengan permission default 'user'
        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => bcrypt($request->password),
            'permission'=> 'user', // Default user biasa
        ]);

        // Jika user berhasil dibuat
        if ($user) {
            // Generate JWT token dengan custom claims (tambahkan permission ke token)
            $token = JWTAuth::fromUser($user, [
                'permission' => $user->permission
            ]);

            return response()->json([
                'success'   => true,
                'message'   => 'User registered successfully',
                'user'      => $user,
                'token'     => $token,
            ], 201);
        }

        // Jika gagal membuat user, kembalikan response gagal
        return response()->json([
            'success' => false,
            'message' => 'User registration failed',
        ], 409);
    }
}