<?php

use App\Enums\CustomCode;
use App\Exceptions\CustomException;
use App\Models\Model;

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

/**
 * 判断两个数组是否相等(元素值一样，顺序可以不一样)
 *
 * @param array $array1
 * @param array $array2
 * @return bool
 */
function array_equal(array $array1, array $array2) {
    return !array_diff($array1, $array2) && !array_diff($array2, $array1);
}

/**
 * 将json数组的每个元素替换成整数
 *
 * @param $jsonArray
 * @return array|null
 */
function json_array_map_intval($jsonArray) {
    return ($jsonArray && is_array($jsonArray)) ? array_map('intval', $jsonArray) : null;
}

/**
 * 自定义授权
 *
 * @param Model $model
 * @throws CustomException
 */
function custom_authorize(Model $model)
{
    if ($model->wechat_user_id != me()->id) {
        throw new CustomException('非法授权操作');
    }
}