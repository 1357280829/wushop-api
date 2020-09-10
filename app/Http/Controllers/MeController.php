<?php

namespace App\Http\Controllers;

use App\Enums\CustomCode;
use App\Enums\PhoneBindType;
use App\Enums\SmsAliyunTemplate;
use App\Exceptions\CustomException;
use App\Rules\ValidPhone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Overtrue\EasySms\EasySms;

class MeController extends Controller
{
    /**
     * showdoc
     * @catalog 接口/我的个人信息
     * @title 查看我的个人信息
     * @description 暂无
     * @method  get
     * @url  /me
     * @param
     * @return {}
     * @return_param me object 我的个人信息&nbsp;[参考](http://showdoc.deepack.top/web/#/4?page_id=47)
     * @remark 暂无
     * @number 1
     */
    public function index()
    {
        return $this->res(CustomCode::Success, ['me' => me()]);
    }

    /**
     * showdoc
     * @catalog 接口/我的个人信息
     * @title 修改我的个人信息
     * @description 暂无
     * @method  post
     * @url  /me
     * @param nickname 可选 string 用户昵称
     * @param avatar_url 可选 string 用户头像url
     * @param phone 可选 string 手机号
     * @param phone_bind_type 可选(phone不为空时必选) number 手机号绑定方式;1:微信绑定,2:短信验证码绑定
     * @param verification_code_key 可选(phone_bind_type为2时必选) string 短信验证码索引
     * @param verification_code 可选(phone_bind_type为2时必选) string 短信验证码
     * @return {}
     * @return_param
     * @remark 暂无
     * @number 1
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nickname' => '',
            'avatar_url' => '',
            'phone' => ['unique:wechat_users,phone,' . me()->id, new ValidPhone()],
            'phone_bind_type' => ['required_with:phone', Rule::in(PhoneBindType::getValues())],
            'verification_code_key' => 'required_if:phone_bind_type,' . PhoneBindType::Sms,
            'verification_code' => 'required_if:phone_bind_type,' . PhoneBindType::Sms,
        ]);

        if ($request->phone && $request->phone_bind_type == PhoneBindType::Sms) {
            $verificationCodeData = Cache::get($request->verification_code_key);
            if (!$verificationCodeData) {
                throw new CustomException('验证码非法或已失效');
            }

            if (
                $verificationCodeData['phone'] != $request->phone
                || !hash_equals($verificationCodeData['code'], $request->verification_code)
            ) {
                throw new CustomException('验证码错误');
            }

            Cache::forget($request->verification_code_key);
        }

        me()->fill($validatedData)->save();

        return $this->res();
    }

    /**
     * showdoc
     * @catalog 接口/我的个人信息
     * @title 发送短信验证码
     * @description 暂无
     * @method  post
     * @url  /me/verification-code
     * @param phone 必选 string 手机号
     * @return {}
     * @return_param verification_code_key string 短信验证码索引
     * @return_param expired_at date 过期时间
     * @remark 暂无
     * @number 1
     */
    public function verificationCodeStore(Request $request, EasySms $easySms)
    {
        $request->validate([
            'phone' => ['required', new ValidPhone()],
        ]);

        //  生成4位随机数，左侧补0
        $code = str_pad(random_int(1, 9999), 4, 0, STR_PAD_LEFT);

        try {
            $easySms->send($request->phone, [
                'template' => SmsAliyunTemplate::VerificationCode,
                'data' => ['code' => $code],
            ]);
        } catch (\Overtrue\EasySms\Exceptions\NoGatewayAvailableException $exception) {
            throw new CustomException($exception->getException('aliyun')->getMessage());
        }

        $verificationCodeKey = 'verification_code-' . Str::random();
        $expiredAt = now()->addMinutes(5);
        //  缓存验证码 5 分钟过期
        Cache::put($verificationCodeKey, ['phone' => $request->phone, 'code' => $code], $expiredAt);

        return $this->res(CustomCode::Success, [
            'verification_code_key' => $verificationCodeKey,
            'expired_at' => $expiredAt->toDateTimeString(),
        ], '发送成功');
    }
}
