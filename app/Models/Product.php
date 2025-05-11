<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'category',
        'image_url',
        'sku',
        'stock',
        'status'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer'
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
            ->orWhere('brand', 'like', "%{$search}%");
    }

    public static function getCachedActiveProducts()
    {
        return Cache::remember('active_products', 3600, function () {
            return self::active()->get();
        });
    }

    public static function getCachedProduct($id)
    {
        return Cache::remember('product_' . $id, 3600, function () use ($id) {
            return self::findOrFail($id);
        });
    }

    protected static function booted()
    {
        static::created(function ($product) {
            Cache::forget('active_products');
        });

        static::updated(function ($product) {
            Cache::forget('active_products');
            Cache::forget('product_' . $product->id);
        });

        static::deleted(function ($product) {
            Cache::forget('active_products');
            Cache::forget('product_' . $product->id);
        });
    }
}
