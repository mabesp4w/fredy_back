<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    /** @use HasFactory<\Database\Factories\ProductImageFactory> */
    use HasFactory, HasUuids;

    // belongsTo productVariant
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
