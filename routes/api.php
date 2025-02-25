<?php

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\Api\RegisterController;
// use App\Http\Controllers\Api\LoginController;
// use App\Http\Controllers\Api\LogoutController;
// use App\Http\Controllers\AuthController;
// use App\Http\Controllers\CartController;

// /**
//  * Auth Routes
//  */
// Route::post('/register', RegisterController::class)->name('register');
// Route::post('/login', LoginController::class)->name('login'); // ERORR KETIKA PAKE [] 
// Route::post('/logout', LogoutController::class)->name('logout');
// /**
//  * Protected Routes (Requires JWT Auth)
//  */
// Route::middleware(['jwt.auth'])->group(function () {
//     Route::get('/user', [AuthController::class, 'getUser']);
//     Route::delete('/user', [AuthController::class, 'deleteUser']);
//     // Route::post('/logout', [AuthController::class, 'logout']); ERORR

//     // Only ADMIN can modify cart
//     Route::middleware(['auth:api', 'check.admin'])->group(function () {
//     Route::middleware(['jwt.auth', 'admin'])->post('/cart', [CartController::class, 'addItem']);
//         // Route::post('/cart', [CartController::class, 'addItem']);
//         Route::delete('/cart/{id}', [CartController::class, 'deleteItem']);
//     });
// });

// /**
//  * Public Routes (No JWT Required)
//  */
// Route::get('/cart/{id}', [CartController::class, 'getItem']); // Lihat 1 item tanpa login


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\LogoutController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;

/**
 * AUTHENTICATION ROUTES
 */

// Register user
Route::post('/register', RegisterController::class,)->name('register');

// Login user
Route::post('/login', LoginController::class)->name('login');

// Logout user (Hanya user yang sudah login)
Route::middleware('jwt.auth')->post('/logout', LogoutController::class)->name('logout');

/**
 * USER ROUTES
 */

// Get authenticated user data
Route::middleware('jwt.auth')->get('/user', [AuthController::class, 'getUser']);

// Delete authenticated user
Route::middleware('jwt.auth')->delete('/user', [AuthController::class, 'deleteUser']);

/**
 * CART ROUTES
 */

// Menambah item ke dalam keranjang (Hanya Admin)
// Route::middleware(['jwt.auth', 'check.admin'])->post('/cart', CartController::class); ERROR KETIKA MENGGUNAKAN CHECK.ADMIN
Route::middleware('jwt.auth')->post('/cart', [CartController::class, 'addItem']);

// Melihat 1 item di dalam keranjang (Bisa diakses oleh semua user, tanpa login)
Route::get('/cart/{id}', [CartController::class, 'getItem']);

// Menghapus item dari keranjang (Hanya Admin)
Route::middleware(['jwt.auth', 'check.admin'])->delete('/cart/{id}', [CartController::class, 'deleteItem']);

/**
 * PRODUCT ROUTES
 */

 Route::apiResource('products', ProductController::class);