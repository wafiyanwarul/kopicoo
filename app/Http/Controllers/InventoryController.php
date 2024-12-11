<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class InventoryController extends Controller
{
    // Menampilkan semua data inventory (semua role bisa mengakses)
    public function index()
    {
        $inventory = Inventory::with('product')->get();
        return response()->json($inventory);
    }

    // Menambahkan data inventory (hanya admin/staff)
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'staff') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'stock' => 'required|integer|min:0',
            'status' => 'required|string|in:available,warning,crisis',
        ]);

        $inventory = Inventory::create($request->all());
        return response()->json(['message' => 'Inventory created successfully', 'data' => $inventory], 201);
    }

    // Memperbarui data inventory (hanya admin/staff)
    public function update(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'staff') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $inventory = Inventory::findOrFail($id);

        $request->validate([
            'stock' => 'sometimes|integer|min:0',
            'status' => 'sometimes|string|in:available,warning,crisis',
        ]);

        $inventory->update($request->all());
        return response()->json(['message' => 'Inventory updated successfully', 'data' => $inventory]);
    }

    // Menghapus data inventory (hanya admin/staff)
    public function destroy($id)
    {
        if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'staff') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $inventory = Inventory::findOrFail($id);
        $inventory->delete();
        return response()->json(['message' => 'Inventory deleted successfully']);
    }
}
