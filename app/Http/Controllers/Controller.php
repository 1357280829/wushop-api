<?php

namespace App\Http\Controllers;

use App\Enums\CustomCode;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function res($customCode = CustomCode::Success, $data = [], $message = '')
    {
        return res($customCode, $data, $message);
    }
}
