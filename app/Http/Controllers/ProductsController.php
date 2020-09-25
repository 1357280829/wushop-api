<?php

namespace App\Http\Controllers;

use App\Enums\CustomCode;
use App\Exceptions\CustomException;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    /**
     * showdoc
     * @catalog 接口
     * @title 商品列表
     * @description 暂无
     * @method  get
     * @url  /products
     * @param category_id 可选 number 分类id
     * @return {}
     * @return_param products pagination 商品&nbsp;[参考](http://showdoc.deepack.top/web/#/4?page_id=53)
     * @remark 暂无
     * @number 1
     */
    public function index(Request $request)
    {
        $request->validate([
            'category_id' => 'integer',
        ]);

        $products = Product::query()
            ->selectBrief()
            ->where('is_on', 1)
            ->when($request->category_id, function ($query, $categoryId) {
                return $query->whereHas('categories', function ($subQuery) use ($categoryId) {
                    return $subQuery->where('category_id', $categoryId);
                });
            })
            ->paginate($request->per_page);

        return $this->res(CustomCode::Success, [
            'products' => $products,
        ]);
    }

    /**
     * showdoc
     * @catalog 接口
     * @title 商品详情
     * @description 暂无
     * @method  get
     * @url  /products/{product_id}
     * @param
     * @return {}
     * @return_param product object 商品&nbsp;[参考](http://showdoc.deepack.top/web/#/4?page_id=53)
     * @return_param product.categories array 分类&nbsp;[参考](http://showdoc.deepack.top/web/#/4?page_id=52)
     * @return_param product.product_skus array 商品sku&nbsp;[参考](http://showdoc.deepack.top/web/#/4?page_id=54)
     * @remark 暂无
     * @number 1
     */
    public function show(Product $product)
    {
        if (!$product->is_on) {
            throw new CustomException('商品未上架');
        }

        $product->load(['categories','productSkus']);

        return $this->res(CustomCode::Success, [
            'product' => $product,
        ]);
    }
}
