<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    // Menampilkan semua data orders
    public function index()
    {
        $orders = Order::with('orderItems.product')->get();
        return response()->json($orders);
    }

    // Membuat order baru dan mengisi order_items
    public function store(Request $request)
    {
        $request->validate([
            'order_items' => 'required|array',
            'order_items.*.product_id' => 'required|exists:products,id',
            'order_items.*.quantity' => 'required|integer|min:1',
        ]);

        // Hitung total harga
        $totalPrice = 0;
        foreach ($request->order_items as $item) {
            $product = Product::find($item['product_id']);
            if (!$product) {
                return response()->json(['message' => 'Product not found'], 404);
            }
            $totalPrice += $product->price * $item['quantity'];
        }

        // Buat order baru
        $order = Order::create([
            'user_id' => Auth::user()->id,
            'status' => 'pending',
            'total_price' => $totalPrice,
        ]);

        // Buat order_items
        foreach ($request->order_items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => Product::find($item['product_id'])->price,
            ]);
        }

        return response()->json(['message' => 'Order created successfully', 'order' => $order->load('orderItems.product')], 201);
    }

    // Memperbarui status order
    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'status' => 'required|string|in:pending,completed,canceled',
        ]);

        $order->update([
            'status' => $request->status,
        ]);

        return response()->json(['message' => 'Order updated successfully', 'order' => $order]);
    }

    // Menghapus order dan order_items terkait
    public function destroy($id)
    {
        $order = Order::findOrFail($id);

        // Hapus semua order_items terkait
        $order->orderItems()->delete();

        // Hapus order
        $order->delete();

        return response()->json(['message' => 'Order deleted successfully']);
    }
}
