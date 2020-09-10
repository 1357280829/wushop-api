<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * 自定义状态码
 *
 * Class CustomCode
 * @package App\Enums
 */
final class CustomCode extends Enum
{
    const Success = 1;
    const Fail = 0;

    const ValidateError = 10000;

    const AuthError = 20000;
    const UnboundPhone = 20001;

    protected static $customCodeToMessages = [
        self::Success => '请求成功',
        self::Fail => '请求失败',

        self::ValidateError => '参数校验错误',

        self::AuthError => '用户授权认证失败',
        self::UnboundPhone => '用户未绑定手机号',
    ];

    public static function getDescription($value): string
    {
        return self::$customCodeToMessages[$value] ?? '未知的状态码';
    }
}
