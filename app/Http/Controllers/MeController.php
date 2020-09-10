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
    public function index()
    {
        return $this->res(CustomCode::Success, ['me' => me()]);
    }

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

    //  新增短信验证码
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
