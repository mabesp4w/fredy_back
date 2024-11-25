<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    /** @use HasFactory<\Database\Factories\OrderItemFactory> */
    use HasFactory, HasUuids;

    // has one to productVariant
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    // belongsTo order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
