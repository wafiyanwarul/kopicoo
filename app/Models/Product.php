<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    // Nama tabel (jika nama tabel di database berbeda dari plural model name)
    protected $table = 'products';

    // Kolom yang dapat diisi melalui mass assignment
    protected $fillable = [
        'name',
        'description',
        'price',
        'category',
        'image',
    ];

    // Default timestamps aktif (created_at dan updated_at)
    public $timestamps = true;

    // Relasi dengan tabel inventory (satu produk memiliki satu inventory)
    public function inventory()
    {
        return $this->hasOne(Inventory::class, 'product_id', 'id');
    }

    // App\Models\Product.php

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }
}
