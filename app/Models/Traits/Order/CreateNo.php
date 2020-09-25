<?php

namespace App\Models\Traits\Order;

trait CreateNo
{
    //  创建订单编号(保证长度在24位)
    public static function createNo($prefix = 'WY')
    {
        return $prefix
            . date('YmdHis')
            . substr(explode(' ', microtime())[0], 2, 3)
            . str_pad(random_int(1, 99999), 5, 0, STR_PAD_LEFT);
    }
}