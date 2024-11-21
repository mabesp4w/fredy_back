<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory, HasUuids;

    // has many order items
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // belongs to user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // has one shipping cost
    public function shippingCost()
    {
        return $this->hasOne(ShippingCost::class, 'id', 'shipping_cost_id');
    }

    // has one shipping status
    public function shippingStatus()
    {
        return $this->hasOne(ShippingStatus::class, 'order_id', 'id');
    }

    // hasMany reviews
    public function review()
    {
        return $this->hasMany(Review::class);
    }
}
