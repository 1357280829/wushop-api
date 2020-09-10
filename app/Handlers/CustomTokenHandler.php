<?php

namespace App\Handlers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CustomTokenHandler
{
    //  创建 custom_token
    public static function create($wechatUserId)
    {
        $tokenIndex = 'wechat_user' . '-' . $wechatUserId;

        //  保证 custom_token 对于 wechat_user 是唯一的
        if ($oldToken = Cache::get($tokenIndex)) {
            Cache::forget($oldToken);
            Cache::forget($tokenIndex);
        }

        $ttl = config('custom-token.ttl');
        $token = md5(config('app.key') . $wechatUserId . (microtime(true) * 10000) . Str::random());
        Cache::put($token, $wechatUserId, $ttl);
        Cache::put($tokenIndex, $token, $ttl);

        return $token;
    }

    //  创建 超级custom_token
    public static function createSuper($wechatUserId)
    {
        $tokenIndex = 'super_wechat_user' . '-' . $wechatUserId;

        //  保证 超级custom_token 对于 wechat_user 是唯一的
        if ($oldToken = Cache::get($tokenIndex)) {
            Cache::forget($oldToken);
            Cache::forget($tokenIndex);
        }

        $ttl = 86400 * 365;
        $token = md5(config('app.key') . $wechatUserId . (microtime(true) * 10000) . Str::random());
        Cache::put($token, $wechatUserId, $ttl);
        Cache::put($tokenIndex, $token, $ttl);

        return $token;
    }
}