<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    /** @use HasFactory<\Database\Factories\ReviewFactory> */
    use HasFactory, HasUuids;

    // belongsTo product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // belongsTo user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
