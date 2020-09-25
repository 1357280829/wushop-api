<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'wechat_user_id', 'consignee_name', 'consignee_phone', 'province', 'city', 'area', 'detail', 'nation_code',
        'postal_code', 'is_default',
    ];

    //  访问器 运费
    public function getFreightPriceAttribute()
    {
        return Freight::getFreightPriceByRegion($this);
    }
}
