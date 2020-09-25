<?php

namespace App\Http\Controllers\Mine;

use App\Enums\CustomCode;
use App\Http\Controllers\Controller;
use App\Models\Address;

class DefaultAddressController extends Controller
{
    /**
     * showdoc
     * @catalog 接口/我的
     * @title 获取我的默认收货地址
     * @description 暂无
     * @method  get
     * @url  /mine/default-address
     * @param
     * @return {}
     * @return_param address object 收货地址&nbsp;[参考](http://showdoc.deepack.top/web/#/4?page_id=55)
     * @remark 暂无
     * @number 1
     */
    public function index()
    {
        $address = Address::query()
            ->where('wechat_user_id', me()->id)
            ->where('is_default', 1)
            ->firstOr(function () {
                return Address::query()
                    ->where('wechat_user_id', me()->id)
                    ->first();
            });

        if ($address) {
            $address->append('freight_price');
        }

        return $this->res(CustomCode::Success, [
            'address' => $address,
        ]);
    }
}
