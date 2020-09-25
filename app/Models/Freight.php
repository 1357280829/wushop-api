<?php

namespace App\Models;

use App\Models\Traits\Freight\GlobalFreightSetting;
use Illuminate\Database\Eloquent\SoftDeletes;

class Freight extends Model
{
    use SoftDeletes, GlobalFreightSetting;

    protected $fillable = [
        'price', 'is_free', 'province', 'city', 'area',
    ];
}
