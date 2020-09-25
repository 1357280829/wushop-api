<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class OrderProduct extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_id', 'product_id', 'product_sku_id', 'number', 'product', 'product_sku',
    ];

    protected $casts = [
        'product' => 'json',
        'product_sku' => 'json',
    ];
}
