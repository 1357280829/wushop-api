<?php

namespace App\Models;

use App\Casts\AdminUrl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'cover_url', 'parent_id', 'sort',
    ];

    protected $casts = [
        'cover_url' => AdminUrl::class,
    ];

    //  商品
    public function products()
    {
        return $this->belongsToMany(Product::class, 'category_product_relations');
    }

    //  下级分类
    public function subs()
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    //  是否附带商品
    public function scopeWithProductsOrNot(Builder $query, $isWithProducts)
    {
        return $query->when($isWithProducts, function ($subQuery) {
            return $subQuery->with([
                'products' => function ($deepQuery) {
                    return $deepQuery->selectBrief()->where('is_on', 1);
                },
            ]);
        });
    }

    //  深度预加载下级分类
    public function scopeWithSubsDeeply(Builder $query, int $deepCount = 1, \Closure $closure = null)
    {
        $withArray = [];
        for ($i = 1; $i <= $deepCount; $i++) {
            $withArray[implode('.', array_fill(0, $i, 'subs'))] = function ($subQuery) use ($closure) {
                return $closure($subQuery);
            };
        }

        return $query->when($withArray, function ($subQuery, $withArray) {
            return $subQuery->with($withArray);
        });
    }
}
