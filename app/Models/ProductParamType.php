<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ProductParamType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
    ];
}
