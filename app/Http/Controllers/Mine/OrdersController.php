<?php

namespace App\Http\Controllers\Mine;

use App\Enums\CustomCode;
use App\Enums\OrderStatus;
use App\Exceptions\CustomException;
use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Freight;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductSku;
use App\Rules\ValidPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OrdersController extends Controller
{
    /**
     * showdoc
     * @catalog 接口/我的
     * @title 我的订单列表
     * @description 暂无
     * @method  get
     * @url  /mine/orders
     * @param
     * @return {}
     * @return_param orders pagination 订单&nbsp;[参考](http://showdoc.deepack.top/web/#/4?page_id=56)
     * @return_param orders.data.*.order_products array 订单商品&nbsp;[参考](http://showdoc.deepack.top/web/#/4?page_id=57)
     * @remark 暂无
     * @number 1
     */
    public function index(Request $request)
    {
        $request->validate([
            'status' => Rule::in(array_merge([0], OrderStatus::getValues())),
        ]);

        $orders = Order::query()
            ->with('orderProducts')
            ->where('wechat_user_id', me()->id)
            ->whenFilter('status')
            ->paginate($request->per_page);

        return $this->res(CustomCode::Success, [
            'orders' => $orders,
        ]);
    }

    /**
     * showdoc
     * @catalog 接口/我的
     * @title 我的订单详情
     * @description 暂无
     * @method  get
     * @url  /mine/orders/{order_id}
     * @param
     * @return {}
     * @return_param order object 订单&nbsp;[参考](http://showdoc.deepack.top/web/#/4?page_id=56)
     * @return_param order.order_products array 订单商品&nbsp;[参考](http://showdoc.deepack.top/web/#/4?page_id=57)
     * @remark 暂无
     * @number 1
     */
    public function show(Order $order)
    {
        custom_authorize($order);

        $order->load('orderProducts');

        return $this->res(CustomCode::Success, [
            'order' => $order,
        ]);
    }

    /**
     * showdoc
     * @catalog 接口/我的
     * @title 新增我的订单
     * @description 暂无
     * @method  post
     * @url  /mine/orders
     * @param freight_price                   必选 number 运费
     * @param total_sale_price                必选 number 总销售价
     * @param actual_price                    必选 number 总合计价
     * @param remark                          可选 string 买家留言
     * @param address_id                      必选 number 收货地址id
     * @param order_products                  必选 array  订单商品数组
     * @param order_products.*.product_id     必选 number 商品id
     * @param order_products.*.product_sku_id 可选 number 商品SKUid
     * @param order_products.*.number         可选 number 商品数量
     * @return {}
     * @return_param
     * @remark 暂无
     * @number 1
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'freight_price' => ['required', new ValidPrice()],
            'total_sale_price' => ['required', new ValidPrice()],
            'actual_price' => ['required', new ValidPrice()],
            'remark' => '',
            'address_id' => 'required|integer',
            'order_products' => 'required|array',
            'order_products.*.product_id' => 'required|integer',
            'order_products.*.product_sku_id' => 'integer',
            'order_products.*.number' => 'integer',
        ]);

        $products = Product::findMany(array_column($validatedData['order_products'], 'product_id'));
        $productSkus = ProductSku::findMany(array_column($validatedData['order_products'], 'product_sku_id'));

        ProductSku::appendProductParams($productSkus);

        DB::beginTransaction();

        $totalSalePrice = 0;
        foreach ($validatedData['order_products'] as &$orderProduct) {
            $product = $products->find($orderProduct['product_id']);
            if (!$product) {
                DB::rollBack();
                throw new CustomException('商品不存在');
            }

            if (!$product->is_on) {
                DB::rollBack();
                throw new CustomException('商品未上架');
            }

            $number = $orderProduct['number'] ?? 1;
            if (isset($orderProduct['product_sku_id'])) {
                if (!$product->is_enable_sku) {
                    DB::rollBack();
                    throw new CustomException('商品未开启SKU');
                }

                $productSku = $productSkus->find($orderProduct['product_sku_id']);
                if (!$productSku) {
                    DB::rollBack();
                    throw new CustomException('商品SKU不存在');
                }

                if (!$productSku->is_enable) {
                    DB::rollBack();
                    throw new CustomException('商品SKU未开启');
                }

                if ($productSku->stock < $number) {
                    DB::rollBack();
                    throw new CustomException('商品库存量不足');
                }

                $orderProduct['product_sku'] = $productSku;
                $totalSalePrice += $number * $productSku->sale_price;

                $productSku->decrement('stock', $number);
            } else {
                if ($product->stock < $number) {
                    DB::rollBack();
                    throw new CustomException('商品库存量不足');
                }

                $totalSalePrice += $number * $product->sale_price;

                $product->decrement('stock', $number);
            }

            $orderProduct['product'] = $product;
        }

        if ($totalSalePrice != $validatedData['total_sale_price']) {
            DB::rollBack();
            throw new CustomException('总销售价有误');
        }

        $address = Address::findOrFail($validatedData['address_id']);
        if (Freight::getFreightPriceByRegion($address) != $validatedData['freight_price']) {
            DB::rollBack();
            throw new CustomException('运费价格有误');
        }

        if ($validatedData['actual_price'] != $validatedData['total_sale_price'] + $validatedData['freight_price']) {
            DB::rollBack();
            throw new CustomException('总合计价有误');
        }

        $validatedData['address'] = $address;
        $validatedData['no'] = Order::createNo();
        $validatedData['wechat_user_id'] = me()->id;
        $validatedData['wechat_user'] = me();

        $order = Order::create($validatedData);

        $order->orderProducts()->createMany($validatedData['order_products']);

        DB::commit();

        return $this->res();
    }

    //  TODO:订单流程
    public function update(Request $request, Order $order)
    {
        custom_authorize($order);

        $validatedData = $request->validate([
            'status' => 'in:5',
        ]);

        $order->fill($validatedData)->save();

        return $this->res();
    }

    /**
     * showdoc
     * @catalog 接口/我的
     * @title 删除我的订单
     * @description 暂无
     * @method  delete
     * @url  /mine/orders/{order_id}
     * @param
     * @return {}
     * @return_param
     * @remark 暂无
     * @number 1
     */
    public function destroy(Order $order)
    {
        custom_authorize($order);

        $order->orderProducts()->delete();

        $order->delete();

        return $this->res();
    }
}
