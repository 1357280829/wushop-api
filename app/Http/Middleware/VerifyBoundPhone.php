<?php

namespace App\Http\Middleware;

use App\Enums\CustomCode;
use App\Exceptions\CustomException;
use Closure;

class VerifyBoundPhone
{
    //  检查是否绑定手机号
    public function handle($request, Closure $next)
    {
        if (!$request->me) {
            throw new CustomException('token 非法或已失效', CustomCode::AuthError);
        }

        if (!$request->me->phone) {
            throw new CustomException('用户未绑定手机号', CustomCode::UnboundPhone);
        }

        return $next($request);
    }
}
