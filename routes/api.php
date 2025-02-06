<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\Api\RegisterController;

/**
 * route "/register"
 * @method "POST"
 */
Route::post('/register', App\Http\Controllers\Api\RegisterController::class)->name('register');

/**
 * route "/login"
 * @method "POST"
 */
Route::post('/login', App\Http\Controllers\Api\LoginController::class)->name('login');

/**
 * route "/user"
 * @method "GET"
 */
// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

/**
 * route "/logout"
 * @method "POST"
 */
Route::post('/logout', App\Http\Controllers\Api\LogoutController::class)->name('logout');

/**
 * route "/user"
 * @method "GET"
 */
Route::middleware('jwt.auth')->get('/user', [AuthController::class, 'getUser']);

/**
 * route "/user"
 * @method "DELETE"
 */
Route::middleware('jwt.auth')->delete('/user', [AuthController::class, 'deleteUser']);

/**
 * route "/logout"
 * @method "POST"
 */
Route::middleware('jwt.auth')->post('/logout', [AuthController::class, 'logout']);

/**
 * route "/cart"
 * @method "POST"
 */
Route::middleware('jwt.auth')->post('/cart', [CartController::class, 'addItem']); // Tambah item ke keranjang

/**
 * route "/cart/{id}"
 * @method "GET"
 */
Route::get('/cart/{id}', [CartController::class, 'getItem']); // Lihat 1 item (Tanpa login)

/**
 * route "/cart/{id}"
 * @method "DELETE"
 */
Route::middleware('jwt.auth')->delete('/cart/{id}', [CartController::class, 'deleteItem']); // Hapus item dari keranjang