<?php

namespace App\Models;

use App\Models\Traits\Cart\ChangeNumber;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cart extends Model
{
    use SoftDeletes, ChangeNumber;

    protected $fillable = [
        'wechat_user_id', 'product_id', 'product_sku_id', 'number',
    ];

    //  微信用户
    public function wechatUser()
    {
        return $this->belongsTo(WechatUser::class);
    }

    //  商品
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    //  商品sku
    public function productSku()
    {
        return $this->belongsTo(ProductSku::class);
    }

    //  访问器 是否失效
    public function getIsInvalidAttribute()
    {
        //  商品不存在
        if (!$this->product) {
            return 1;
        }

        //  商品未上架
        if (!$this->product->is_on) {
            return 1;
        }

        if ($this->product->is_enable_sku) {
            //  商品已开启sku 但 商品sku不存在
            if (!$this->product_sku_id || !$this->productSku) {
                return 1;
            }

            //  商品已开启sku 但 商品sku未开启
            if (!$this->productSku->is_enable) {
                return 1;
            }
        }

        //  商品未开启sku 但 拥有商品sku
        if (!$this->product->is_enable_sku && $this->product_sku_id) {
            return 1;
        }

        return 0;
    }
}
