<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * 订单状态
 *
 * Class OrderStatus
 * @package App\Enums
 */
final class OrderStatus extends Enum
{
    //  待付款
    const WaitForPay = 1;
    //  待发货
    const WaitForSend = 2;
    //  待收货
    const WaitForReceive = 3;
    //  已完成
    const Finished = 4;
    //  已取消
    const Canceled = 5;
    //  已退款
    const Refunded = 6;
}
