<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestsController extends Controller
{
    public function index(Request $request)
    {
        $aaa = 333;

        print_r($aaa);
        exit();
    }
}
