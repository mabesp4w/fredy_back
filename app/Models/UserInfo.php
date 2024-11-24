<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInfo extends Model
{
    /** @use HasFactory<\Database\Factories\UserInfoFactory> */
    use HasFactory, HasUuids;

    // belongsTo shippingCost
    public function shippingCost()
    {
        return $this->belongsTo(ShippingCost::class);
    }
}
