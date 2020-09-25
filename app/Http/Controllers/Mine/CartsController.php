<?php

namespace App\Http\Controllers\Mine;

use App\Enums\CustomCode;
use App\Exceptions\CustomException;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartsController extends Controller
{
    /**
     * showdoc
     * @catalog 接口/我的
     * @title 我的购物车列表
     * @description 暂无
     * @method  get
     * @url  /mine/carts
     * @param
     * @return {}
     * @return_param carts                        array  购物车&nbsp;[参考](http://showdoc.deepack.top/web/#/4?page_id=58)
     * @return_param carts.*.product              object 商品&nbsp;[参考](http://showdoc.deepack.top/web/#/4?page_id=53)
     * @return_param carts.*.product.product_skus array  商品所有SKU&nbsp;[参考](http://showdoc.deepack.top/web/#/4?page_id=54)
     * @return_param carts.*.product_sku          object 商品确选SKU&nbsp;[参考](http://showdoc.deepack.top/web/#/4?page_id=54)
     * @remark 暂无
     * @number 1
     */
    public function index()
    {
        $carts = Cart::query()
            ->with(['product', 'productSku', 'product.productSkus'])
            ->where('wechat_user_id', me()->id)
            ->get()
            ->each(function (Cart $cart) {
                $cart->append('is_invalid');
            });

        return $this->res(CustomCode::Success, [
            'carts' => $carts,
        ]);
    }

    /**
     * showdoc
     * @catalog 接口/我的
     * @title 新增我的购物车
     * @description 暂无
     * @method  post
     * @url  /mine/carts
     * @param product_id     必选 number 商品id
     * @param product_sku_id 可选 number 商品SKUid
     * @param incre_number   可选 number 新增数量
     * @return {}
     * @return_param
     * @remark 暂无
     * @number 1
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'product_sku_id' => 'integer',
            'incre_number' => 'integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);
        if (!$product->is_on) {
            throw new CustomException('该商品未上架');
        }

        $cart = Cart::firstOrCreate(
            [
                'wechat_user_id' => me()->id,
                'product_id' => $product->id,
                'product_sku_id' => $request->product_sku_id ?: 0,
            ],
            ['number' => 0]
        );

        $cart->incrementNumber($request->incre_number);

        return $this->res();
    }

    /**
     * showdoc
     * @catalog 接口/我的
     * @title 修改我的购物车
     * @description 暂无
     * @method  patch
     * @url  /mine/carts/{cart_id}
     * @param product_sku_id 可选 number 商品SKUid
     * @param number         可选 number 数量
     * @param incre_number   可选 number 新增数量
     * @param decre_number   可选 number 减少数量
     * @return {}
     * @return_param
     * @remark 暂无
     * @number 1
     */
    public function update(Request $request, Cart $cart)
    {
        custom_authorize($cart);

        $validatedData = $request->validate([
            'product_sku_id' => 'integer',
            'number' => 'integer|min:1|max:200',
            'incre_number' => 'integer|min:1',
            'decre_number' => 'integer|min:1',
        ]);

        if ($request->incre_number) {
            $cart->incrementNumber($request->incre_number);
        }

        if ($request->decre_number) {
            $cart->decrementNumber($request->decre_number);
        }

        if (isset($validatedData['number'])) {
            $validatedData['number'] = $cart->getChangingNumber($validatedData['number']);
        }

        $cart->fill($validatedData)->save();

        return $this->res();
    }

    /**
     * showdoc
     * @catalog 接口/我的
     * @title 删除我的购物车
     * @description 暂无
     * @method  delete
     * @url  /mine/carts/{cart_id}
     * @param
     * @return {}
     * @return_param
     * @remark 暂无
     * @number 1
     */
    public function destroy(Cart $cart)
    {
        custom_authorize($cart);

        $cart->delete();

        return $this->res();
    }

    /**
     * showdoc
     * @catalog 接口/我的
     * @title 批量删除我的购物车
     * @description 暂无
     * @method  post
     * @url  /mine/carts/_batch_destroy
     * @param ids 必选 array 购物车ids
     * @return {}
     * @return_param
     * @remark 暂无
     * @number 1
     */
    public function batchDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer',
        ]);

        if (Cart::whereKey($request->ids)->count() != count($request->ids)) {
            throw new CustomException('待删除购物车不存在');
        }

        Cart::destroy($request->ids);

        return $this->res();
    }
}
