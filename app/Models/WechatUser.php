<?php

namespace App\Models;

class WechatUser extends Model
{
    protected $fillable = [
        'nickname', 'phone', 'unionid', 'openid_mini_program', 'openid_official_account', 'avatar_url', 'gender',
        'country', 'province', 'city',
    ];

    protected $hidden = [
        'unionid', 'openid_mini_program', 'openid_official_account', 'deleted_at',
    ];
}
