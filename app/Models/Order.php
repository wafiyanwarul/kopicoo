<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',      // ID pengguna yang membuat order
        'status',       // Status order (pending, completed, canceled)
        'total_price',  // Total harga dari order
    ];

    /**
     * Relasi dengan tabel order_items.
     * Satu order memiliki banyak order_items.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Relasi dengan tabel users.
     * Satu order dibuat oleh satu pengguna.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
