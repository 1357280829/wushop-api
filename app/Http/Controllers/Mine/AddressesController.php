<?php

namespace App\Http\Controllers\Mine;

use App\Enums\CustomCode;
use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Rules\ValidPhone;
use Illuminate\Http\Request;

class AddressesController extends Controller
{
    /**
     * showdoc
     * @catalog 接口/我的
     * @title 我的收货地址列表
     * @description 暂无
     * @method  get
     * @url  /mine/addresses
     * @param
     * @return {}
     * @return_param addresses array 收货地址&nbsp;[参考](http://showdoc.deepack.top/web/#/4?page_id=55)
     * @remark 暂无
     * @number 1
     */
    public function index()
    {
        $addresses = Address::query()
            ->where('wechat_user_id', me()->id)
            ->get();

        return $this->res(CustomCode::Success, [
            'addresses' => $addresses,
        ]);
    }

    /**
     * showdoc
     * @catalog 接口/我的
     * @title 我的收货地址详情
     * @description 暂无
     * @method  get
     * @url  /mine/addresses/{address_id}
     * @param
     * @return {}
     * @return_param address object 收货地址&nbsp;[参考](http://showdoc.deepack.top/web/#/4?page_id=55)
     * @remark 暂无
     * @number 1
     */
    public function show(Address $address)
    {
        custom_authorize($address);

        $address->append('freight_price');

        return $this->res(CustomCode::Success, [
            'address' => $address,
        ]);
    }

    /**
     * showdoc
     * @catalog 接口/我的
     * @title 新增我的收货地址
     * @description 暂无
     * @method  post
     * @url  /mine/addresses
     * @param consignee_name  必选 string 收货人名称
     * @param consignee_phone 必选 string 收货人手机号
     * @param province        必选 string 省
     * @param city            必选 string 市
     * @param area            必选 string 区
     * @param detail          必选 string 详细地址
     * @param nation_code     可选 string 收货地址国家码
     * @param postal_code     可选 string 邮编
     * @param is_default      可选 number 是否为默认地址
     * @return {}
     * @return_param
     * @remark 暂无
     * @number 1
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'consignee_name' => 'required',
            'consignee_phone' => ['required', new ValidPhone()],
            'province' => 'required',
            'city' => 'required',
            'area' => 'required',
            'detail' => 'required',
            'nation_code' => '',
            'postal_code' => '',
            'is_default' => 'in:0,1',
        ]);

        $validatedData['wechat_user_id'] = me()->id;
        if ($request->is_default || Address::where('wechat_user_id', me()->id)->doesntExist()) {
            $validatedData['is_default'] = 1;
        }

        if ($request->is_default) {
            Address::query()
                ->where('wechat_user_id', me()->id)
                ->where('is_default', 1)
                ->update(['is_default' => 0]);
        }

        Address::create($validatedData);

        return $this->res();
    }

    /**
     * showdoc
     * @catalog 接口/我的
     * @title 修改我的收货地址
     * @description 暂无
     * @method  patch
     * @url  /mine/addresses/{address_id}
     * @param consignee_name  可选 string 收货人名称
     * @param consignee_phone 可选 string 收货人手机号
     * @param province        可选 string 省
     * @param city            可选 string 市
     * @param area            可选 string 区
     * @param detail          可选 string 详细地址
     * @param nation_code     可选 string 收货地址国家码
     * @param postal_code     可选 string 邮编
     * @param is_default      可选 number 是否为默认地址
     * @return {}
     * @return_param
     * @remark 暂无
     * @number 1
     */
    public function update(Request $request, Address $address)
    {
        custom_authorize($address);

        $validatedData = $request->validate([
            'consignee_name' => '',
            'consignee_phone' => new ValidPhone(),
            'province' => '',
            'city' => '',
            'area' => '',
            'detail' => '',
            'nation_code' => '',
            'postal_code' => '',
            'is_default' => 'in:0,1',
        ]);

        if ($request->is_default) {
            Address::query()
                ->where('wechat_user_id', me()->id)
                ->where('is_default', 1)
                ->update(['is_default' => 0]);
        }

        $address->fill($validatedData)->save();

        return $this->res();
    }

    /**
     * showdoc
     * @catalog 接口/我的
     * @title 删除我的收货地址
     * @description 暂无
     * @method  delete
     * @url  /mine/addresses/{address_id}
     * @param
     * @return {}
     * @return_param
     * @remark 暂无
     * @number 1
     */
    public function destroy(Address $address)
    {
        custom_authorize($address);

        $address->delete();

        return $this->res();
    }
}
