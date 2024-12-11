<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventory';

    protected $fillable = [
        'product_id',
        'stock',
        'status',
    ];

    public $timestamps = true;

    // Relasi dengan tabel products (satu inventory terkait dengan satu produk)
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
