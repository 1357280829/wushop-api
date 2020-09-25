<?php

namespace App\Models\Traits\Product;

use Illuminate\Support\Arr;

trait GetBrief
{
    protected $complex = [
        'banner_urls', 'desc', 'detail_introduction', 'detail_after_sale',
    ];

    //  查询 简要字段
    public function getBrief()
    {
        return array_values(
            array_diff(
                array_merge(
                    [$this->getKeyName()],
                    $this->getFillable(),
                    [$this->getCreatedAtColumn(), $this->getUpdatedAtColumn()]
                ),
                $this->complex
            )
        );
    }
}