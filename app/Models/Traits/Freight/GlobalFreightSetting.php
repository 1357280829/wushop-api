<?php

namespace App\Models\Traits\Freight;

use App\Models\Freight;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Cache;

trait GlobalFreightSetting
{
    //  获取 全局运费设置
    public static function getGlobalFreightSetting()
    {
        return [
            'is_free' => Cache::get('global_freight_setting-is_free', 1),
            'global_price' => Cache::get('global_freight_setting-global_price', 0.00),
        ];
    }

    //  通过 地区 获取 运费
    public static function getFreightPriceByRegion($region)
    {
        $globalFreightSetting = self::getGlobalFreightSetting();
        if ($globalFreightSetting['is_free']) {
            return 0;
        }

        if ($region instanceof Arrayable) {
            [$province, $city, $area] = [$region['province'], $region['city'], $region['area']];
        } else {
            $region = func_get_args();
            [$province, $city, $area] = [$region[0] ?? null, $region[1] ?? null, $region[2] ?? null];
        }

        //  根据 统一配置、省配置、市配置、区配置 来决定运费
        $provinceConfigWhere = function ($query) use ($province) {
            return $query->where('province', $province)->whereNull('city')->whereNull('area');
        };
        $cityConfigWhere = function ($query) use ($province, $city) {
            return $query->where('province', $province)->where('city', $city)->whereNull('area');
        };
        $areaConfigWhere = function ($query) use ($province, $city, $area) {
            return $query->where('province', $province)->where('city', $city)->where('area', $area);
        };

        $freights = Freight::query()
            ->where(function ($query) use ($provinceConfigWhere) {
                return $provinceConfigWhere($query);
            })
            ->orWhere(function ($query) use ($cityConfigWhere) {
                return $cityConfigWhere($query);
            })
            ->orWhere(function ($query) use ($areaConfigWhere) {
                return $areaConfigWhere($query);
            })
            ->get();

        if ($freights->isNotEmpty()) {
            $freightConfigs = [
                $areaConfigWhere($freights)->first(),
                $cityConfigWhere($freights)->first(),
                $provinceConfigWhere($freights)->first(),
            ];
            foreach ($freightConfigs as $freightConfig) {
                if ($freightConfig) {
                    return $freightConfig->is_free ? 0 : $freightConfig->price;
                }
            }
        }

        return $globalFreightSetting['global_price'];
    }
}