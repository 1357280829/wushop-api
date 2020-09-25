<?php

namespace App\Models;

use App\Models\Traits\Order\CreateNo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes, CreateNo;

    protected $fillable = [
        'wechat_user_id', 'no', 'status', 'freight_price', 'total_sale_price', 'actual_price', 'remark', 'address',
        'wechat_user',
    ];

    protected $casts = [
        'address' => 'json',
        'wechat_user' => 'json',
    ];

    //  订单商品
    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class);
    }
}
