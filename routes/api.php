<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//  封面页
Route::get('/', function () {
    return view('welcome');
});

Route::group([
    'middleware' => 'custom_token',
], function () {
    Route::resource('me', 'MeController')->only(['index', 'store']);
    Route::post('me/verification-code', 'MeController@verificationCodeStore');

    Route::group([
        'middleware' => 'bound_phone',
    ], function () {
        Route::resource('tests', 'TestsController');
    });
});

Route::post('authenticated-wechat-users', 'AuthenticatedWechatUsersController@store');
