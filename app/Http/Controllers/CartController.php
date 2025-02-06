<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    // ✅ 1. Tambah 1 item ke keranjang (Login diperlukan)
    public function addItem(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $validator = Validator::make($request->all(), [
                'product_id' => 'required|string',
                'product_name' => 'required|string',
                'quantity' => 'required|integer|min:1',
                'price' => 'required|numeric|min:0'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }

            $cartItem = Cart::create([
                'user_id' => $user->_id,
                'product_id' => $request->product_id,
                'product_name' => $request->product_name,
                'quantity' => $request->quantity,
                'price' => $request->price
            ]);

            return response()->json(['message' => 'Item berhasil ditambahkan ke keranjang', 'cart' => $cartItem], 201);
        } catch (JWTException $e) {
            return response()->json(['message' => 'Token tidak valid atau kedaluwarsa'], 401);
        }
    }

    // ✅ 3. Lihat 1 item di keranjang (Tidak butuh login)
    public function getItem($id)
    {
        $cartItem = Cart::find($id);

        if (!$cartItem) {
            return response()->json(['message' => 'Item tidak ditemukan'], 404);
        }

        return response()->json(['cart' => $cartItem], 200);
    }

    // ✅ 6. Hapus 1 item dari keranjang (Login diperlukan)
    public function deleteItem($id)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $cartItem = Cart::where('_id', $id)->where('user_id', $user->_id)->first();

            if (!$cartItem) {
                return response()->json(['message' => 'Item tidak ditemukan atau bukan milik user'], 404);
            }

            $cartItem->delete();
            return response()->json(['message' => 'Item berhasil dihapus'], 200);
        } catch (JWTException $e) {
            return response()->json(['message' => 'Token tidak valid atau kedaluwarsa'], 401);
        }
    }
}
