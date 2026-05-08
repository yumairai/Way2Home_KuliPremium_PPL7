<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // 1. Ambil semua data keranjang user yang sedang login (Read) - OPTIMIZED
    public function index()
    {
        $userId = Auth::id();
        $carts = Cart::select('id', 'user_id', 'material_id', 'jumlah')  // Only needed columns
            ->with('material:id,nama_material,harga,satuan,kategori')    // Only needed material columns
            ->where('user_id', $userId)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $carts
        ]);
    }

    // 2. Tambah barang ke keranjang (Create/Update) - OPTIMIZED
    public function addToCart(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'material_id' => 'required|integer|min:1',  // Fast integer validation
            'jumlah' => 'required|integer|min:1'
        ]);

        $userId = Auth::id();
        $materialId = $request->material_id;
        $jumlah = $request->jumlah;

        // Gunakan updateOrCreate untuk 1 query saja (bukan 2-3 query)
        Cart::updateOrCreate(
            [
                'user_id' => $userId,
                'material_id' => $materialId
            ],
            [
                'jumlah' => $jumlah
            ]
        );

        return response()->json(['message' => 'Keranjang berhasil diperbarui!']);
    }

    // 3. Update jumlah barang 
    public function updateQuantity(Request $request, $id)
    {
        $request->validate([
            'jumlah' => 'required|integer|min:1'
        ]);

        $cart = Cart::where('user_id', Auth::id())->find($id);

        if (!$cart) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $cart->update(['jumlah' => $request->jumlah]);

        return response()->json(['message' => 'Jumlah berhasil diperbarui']);
    }

    // 4. Hapus berdasarkan ID Baris Keranjang
    public function removeFromCart($id)
    {
        $cart = Cart::where('user_id', Auth::id())->find($id);
        if (!$cart) return response()->json(['message' => 'Data tidak ditemukan'], 404);
        $cart->delete();
        return response()->json(['message' => 'Barang dihapus']);
    }

    // 5. Hapus berdasarkan ID Material
    public function removeByMaterial($material_id)
    {
        $cart = Cart::where('user_id', Auth::id())
            ->where('material_id', $material_id)
            ->first();

        if (!$cart) return response()->json(['message' => 'Barang tidak ditemukan'], 404);
        $cart->delete();
        return response()->json(['message' => 'Barang dihapus dari keranjang']);
    }
}
