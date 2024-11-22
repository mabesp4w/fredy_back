<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    /** @use HasFactory<\Database\Factories\ProductVariantFactory> */
    use HasFactory, HasUuids;

    // belongsTo
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // hasMany
    public function productVariantImages()
    {
        return $this->hasMany(ProductImage::class);
    }

    // hasMany review
    public function review()
    {
        return $this->hasMany(Review::class);
    }

    // hasMany orderItem
    public function orderItem()
    {
        return $this->hasMany(OrderItem::class);
    }
}
