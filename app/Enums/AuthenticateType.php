<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * 认证方式
 *
 * Class AuthenticateType
 * @package App\Enums
 */
final class AuthenticateType extends Enum
{
    //  小程序认证
    const MiniProgram = 1;

    //  公众号认证
    const OfficialAccount = 2;
}
