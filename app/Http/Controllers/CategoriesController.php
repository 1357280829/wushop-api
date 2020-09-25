<?php

namespace App\Http\Controllers;

use App\Enums\CustomCode;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    /**
     * showdoc
     * @catalog 接口
     * @title 分类列表
     * @description 暂无
     * @method  get
     * @url  /categories
     * @param is_with_products 可选 number 是否附带商品数据
     * @return {}
     * @return_param categories array 分类&nbsp;[参考](http://showdoc.deepack.top/web/#/4?page_id=52)
     * @remark 暂无
     * @number 1
     */
    public function index(Request $request)
    {
        $request->validate([
            'is_with_products' => 'in:0,1',
        ]);

        $categories = Category::query()
            ->where('parent_id', 0)
            ->withSubsDeeply(1, function ($query) {
                return $query->orderByDesc('sort')->withProductsOrNot(request()->is_with_products);
            })
            ->orderByDesc('sort')
            ->withProductsOrNot($request->is_with_products)
            ->get();

        return $this->res(CustomCode::Success, [
            'categories' => $categories,
        ]);
    }
}
