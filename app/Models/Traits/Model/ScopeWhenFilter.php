<?php

namespace App\Models\Traits\Model;

use Illuminate\Database\Eloquent\Builder;

trait ScopeWhenFilter
{
    //  筛选过滤查询 where
    public function scopeWhenFilter(Builder $query, $filterName, $fieldName = null)
    {
        $fieldName = $fieldName ?: $filterName;
        return $query->when(request()->$filterName, function (Builder $subQuery, $filterValue) use ($fieldName) {
            return $subQuery->where($fieldName, $filterValue);
        });
    }
}