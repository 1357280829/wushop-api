<?php

namespace App\Http\Controllers;

use App\Enums\AuthenticateType;
use App\Enums\CustomCode;
use App\Http\Controllers\Traits\AuthenticatedWechatUsers\MiniProgramStore;
use App\Models\WechatUser;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AuthenticatedWechatUsersController extends Controller
{
    use MiniProgramStore;

    public function store(Request $request)
    {
        $request->validate([
            'authenticate_type' => ['required', Rule::in(AuthenticateType::getValues())],
        ]);

        switch ($request->authenticate_type) {
            case AuthenticateType::MiniProgram:
                //  小程序认证
                $wechatUser = $this->miniProgramStore();
                break;
            case AuthenticateType::OfficialAccount:
                //  TODO:公众号认证
                $wechatUser = WechatUser::find(1);
                break;
        }

        return $this->res(CustomCode::Success, [
            'me' => $wechatUser,
        ]);
    }
}
