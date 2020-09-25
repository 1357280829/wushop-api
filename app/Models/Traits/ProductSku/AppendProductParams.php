<?php

namespace App\Models\Traits\ProductSku;

use App\Models\ProductParam;
use Illuminate\Support\Collection;

trait AppendProductParams
{
    //  追加 商品规格
    public static function appendProductParams(Collection $productSkus)
    {
        if ($productSkus->isEmpty()) {
            return false;
        }

        $productParamsDictionary = ProductParam::query()
            ->whereKey($productSkus->pluck('product_param_ids')->flatten())
            ->pluck('name', 'id')
            ->toArray();

        $productSkus->each(function ($productSku) use ($productParamsDictionary) {
            $productSku->product_params = array_map(function ($productParamId) use ($productParamsDictionary) {
                return $productParamsDictionary[$productParamId] ?? '无规格';
            }, $productSku->product_param_ids);
        });
    }
}