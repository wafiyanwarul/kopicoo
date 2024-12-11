<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', function (Request $request) {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    });

    // Products CRUD Routes
    Route::get('/products', [ProductController::class, 'index']); // View all products
    Route::post('/products', [ProductController::class, 'store']); // Create product
    Route::put('/products/{id}', [ProductController::class, 'update']); // Update product
    Route::delete('/products/{id}', [ProductController::class, 'destroy']); // Delete product

    // Inventory CRUD Routes
    Route::get('/inventory', [InventoryController::class, 'index']); // View all inventory
    Route::post('/inventory', [InventoryController::class, 'store']); // Create inventory
    Route::put('/inventory/{id}', [InventoryController::class, 'update']); // Update inventory
    Route::delete('/inventory/{id}', [InventoryController::class, 'destroy']); // Delete inventory

    // Order CRUD Routes
    Route::get('/orders', [OrderController::class, 'index']); // View all orders
    Route::post('/order', [OrderController::class, 'store']); // Create order and order_items
    Route::put('/order/{id}', [OrderController::class, 'update']); // Update order
    Route::delete('/order/{id}', [OrderController::class, 'destroy']); // Delete order and order_items


    // Order Item CRUD Routes
    Route::prefix('/order-items')->group(function () {
        Route::get('/', [OrderItemController::class, 'index']);       // Get all order items
        Route::post('/', [OrderItemController::class, 'store']);      // Create new order with items
        Route::get('/{id}', [OrderItemController::class, 'show']);    // Get order item by ID
        Route::put('/{id}', [OrderItemController::class, 'update']);  // Update order item
        Route::delete('/{id}', [OrderItemController::class, 'destroy']); // Delete order item
    });
});
