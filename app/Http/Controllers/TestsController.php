<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;

class TestsController extends Controller
{
    public function index()
    {
        print_r(me());
        exit();
    }
}
