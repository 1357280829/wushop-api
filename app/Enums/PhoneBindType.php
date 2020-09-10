<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * 手机号绑定方式
 *
 * Class PhoneBindType
 * @package App\Enums
 */
final class PhoneBindType extends Enum
{
    const Wechat = 1;
    const Sms = 2;
}
