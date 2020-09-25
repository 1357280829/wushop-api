<?php

namespace App\Models;

use App\Casts\AdminUrl;
use App\Models\Traits\Product\GetBrief;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes, GetBrief;

    protected $fillable = [
        'banner_urls', 'cover_url', 'name', 'desc', 'is_on', 'is_enable_sku', 'stock', 'sale_price', 'cost_price',
        'minimum_price', 'detail_introduction', 'detail_after_sale', 'sort', 'on_at',
    ];

    protected $casts = [
        'banner_urls' => 'json',
        'cover_url' => AdminUrl::class,
    ];

    //  分类
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_product_relations');
    }

    //  商品sku
    public function productSkus()
    {
        return $this->hasMany(ProductSku::class, 'product_id', 'id');
    }

    //  查询简要字段
    public function scopeSelectBrief(Builder $query)
    {
        return $query->select($this->getBrief());
    }
}
