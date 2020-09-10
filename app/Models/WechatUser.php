<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class WechatUser extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nickname', 'phone', 'unionid', 'openid_mini_program', 'openid_official_account', 'avatar_url', 'gender',
        'country', 'province', 'city',
    ];

    protected $hidden = [
        'unionid', 'openid_mini_program', 'openid_official_account', 'deleted_at',
    ];
}
