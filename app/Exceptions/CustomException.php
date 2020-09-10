<?php

namespace App\Exceptions;

use Exception;

class CustomException extends Exception
{
    public function render($request)
    {
        $message = $this->getMessage() ?: '客户端异常';

        return res($this->getCode(), [], $message);
    }
}
