<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',    // ID dari order terkait
        'product_id',  // ID dari produk terkait
        'quantity',    // Jumlah produk yang dipesan
        'price',       // Harga satuan produk pada saat order
    ];

    /**
     * Relasi dengan tabel orders.
     * Setiap item pesanan dimiliki oleh satu order.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relasi dengan tabel products.
     * Setiap item pesanan terhubung dengan satu produk.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
