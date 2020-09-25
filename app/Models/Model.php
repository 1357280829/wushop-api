<?php

namespace App\Models;

use App\Models\Traits\Model\ScopeWhenFilter;
use Illuminate\Database\Eloquent\Model as BaseModel;

class Model extends BaseModel
{
    use ScopeWhenFilter;

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format($this->dateFormat ?: 'Y-m-d H:i:s');
    }
}
