<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingCost extends Model
{
    /** @use HasFactory<\Database\Factories\ShippingCostFactory> */
    use HasFactory, HasUuids;

    // belongsTo subDistrict
    public function subDistrict()
    {
        return $this->belongsTo(SubDistrict::class);
    }
}
