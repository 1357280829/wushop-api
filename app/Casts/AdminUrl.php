<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Facades\Storage;

class AdminUrl implements CastsAttributes
{
    public function get($model, $key, $value, array $attributes)
    {
        return $value ? (url()->isValidUrl($value) ? $value : Storage::disk('admin')->url($value)) : $value;
    }

    public function set($model, $key, $value, array $attributes)
    {
        return $value;
    }
}