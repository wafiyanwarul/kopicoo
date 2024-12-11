<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

class OrderItemController extends Controller
{
    /**
     * Menampilkan semua order items beserta relasinya.
     */
    public function index()
    {
        $orderItems = OrderItem::with(['order', 'product'])->get();

        return response()->json([
            'message' => 'Order items retrieved successfully',
            'data' => $orderItems,
        ]);
    }

    /**
     * Membuat order baru beserta detail item (produk yang dipesan).
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',       // Validasi user ID
            'items' => 'required|array',                 // Validasi array item
            'items.*.product_id' => 'required|exists:products,id', // Validasi product ID
            'items.*.quantity' => 'required|integer|min:1',        // Validasi quantity
        ]);

        // Membuat order baru
        $order = Order::create([
            'user_id' => $request->user_id,
            'status' => 'pending',
            'total_price' => 0, // Akan dihitung berdasarkan items
        ]);

        $totalPrice = 0;

        // Memproses setiap item yang dipesan
        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);
            $price = $product->price * $item['quantity'];

            // Menambahkan item ke tabel order_items
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $price,
            ]);

            // Menambahkan total harga
            $totalPrice += $price;
        }

        // Update total harga di tabel orders
        $order->update(['total_price' => $totalPrice]);

        return response()->json([
            'message' => 'Order created successfully',
            'data' => $order->load('orderItems.product'),
        ]);
    }

    /**
     * Menampilkan detail order item berdasarkan ID.
     */
    public function show($id)
    {
        $orderItem = OrderItem::with(['order', 'product'])->find($id);

        if (!$orderItem) {
            return response()->json(['message' => 'Order item not found'], 404);
        }

        return response()->json([
            'message' => 'Order item retrieved successfully',
            'data' => $orderItem,
        ]);
    }

    /**
     * Mengupdate order item tertentu.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'nullable|integer|min:1',
        ]);

        $orderItem = OrderItem::find($id);

        if (!$orderItem) {
            return response()->json(['message' => 'Order item not found'], 404);
        }

        // Update quantity jika diberikan
        if ($request->has('quantity')) {
            $orderItem->quantity = $request->quantity;
            $orderItem->price = $request->quantity * $orderItem->product->price;
        }

        $orderItem->save();

        // Update total harga di tabel orders
        $order = $orderItem->order;
        $order->total_price = $order->orderItems->sum('price');
        $order->save();

        return response()->json([
            'message' => 'Order item updated successfully',
            'data' => $orderItem->load('product'),
        ]);
    }

    /**
     * Menghapus order item berdasarkan ID.
     */
    public function destroy($id)
    {
        $orderItem = OrderItem::find($id);

        if (!$orderItem) {
            return response()->json(['message' => 'Order item not found'], 404);
        }

        // Hapus order item
        $orderItem->delete();

        // Update total harga di tabel orders
        $order = $orderItem->order;
        $order->total_price = $order->orderItems->sum('price');
        $order->save();

        return response()->json(['message' => 'Order item deleted successfully']);
    }
}
