<?php

namespace App\Http\Middleware;

use App\Enums\CustomCode;
use App\Exceptions\CustomException;
use App\Models\WechatUser;
use Closure;
use Illuminate\Support\Facades\Cache;

class VerifyCustomToken
{
    //  检查custom_token
    public function handle($request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            throw new CustomException('token 不存在', CustomCode::AuthError);
        }

        $wechatUserId = Cache::get($token);
        if (!$wechatUserId) {
            throw new CustomException('token 非法或已失效', CustomCode::AuthError);
        }

        $wechatUser = WechatUser::find($wechatUserId);
        if (!$wechatUser) {
            throw new CustomException('该用户已不存在');
        }

        //  将登陆用户数据放置在该次请求中
        $request->me = $wechatUser;

        return $next($request);
    }
}
