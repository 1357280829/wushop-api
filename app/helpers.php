<?php

use App\Enums\CustomCode;

/**
 * 统一响应
 *
 * @param int $customCode
 * @param array $data
 * @param string $message
 * @return \Illuminate\Http\JsonResponse
 */
function res($customCode = CustomCode::Success, $data = [], $message = '')
{
    $message = $message ?: CustomCode::getDescription($customCode);

    return response()->json([
        'custom_code' => $customCode,
        'data' => $data,
        'message' => $message
    ]);
}

/**
 * 获取登陆的用户信息
 *
 * @return \App\Models\WechatUser
 */
function me()
{
    return request()->me;
}