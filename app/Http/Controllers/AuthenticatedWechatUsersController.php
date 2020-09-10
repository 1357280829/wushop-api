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

    /**
     * showdoc
     * @catalog 接口
     * @title 微信用户授权认证
     * @description 暂无
     * @method  post
     * @url  /authenticated-wechat-users
     * @param authenticate_type 必选 number 认证方式;1:小程序认证,2:公众号认证
     * @param code           可选(authenticate_type为1时必选) number wx\.login用户登录凭证
     * @param encrypted_data 可选(authenticate_type为1时必选) string wx\.getUserInfo加密数据
     * @param iv             可选(authenticate_type为1时必选) string wx\.getUserInfo加密算法的初始向量
     * @param raw_data       可选(authenticate_type为1时必选) string wx\.getUserInfo原始数据字符串
     * @param signature      可选(authenticate_type为1时必选) string wx\.getUserInfo校验字符串
     * @return {}
     * @return_param me object 我的个人信息&nbsp;[参考](http://showdoc.deepack.top/web/#/4?page_id=47)
     * @return_param me\.token string 客户服务器token
     * @remark 暂无
     * @number 1
     */
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
