<?php

namespace App\Models;

use App\Models\Traits\ProductSku\AppendProductParams;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductSku extends Model
{
    use SoftDeletes, AppendProductParams;

    protected $fillable = [
        'product_id', 'product_param_ids', 'is_enable', 'stock', 'sale_price', 'cost_price', 'desc',
    ];

    protected $casts = [
        'product_param_ids' => 'json',
    ];
}
