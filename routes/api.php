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

//  分类列表
Route::get('categories', 'CategoriesController@index');
//  - 商品
Route::resource('products', 'ProductsController')->only(['index', 'show']);

Route::group([
    'middleware' => 'custom_token',
], function () {
    //  - 我的个人信息
    Route::resource('me', 'MeController')->only(['index', 'store']);
    //  发送短信验证码
    Route::post('me/verification-code', 'MeController@verificationCodeStore');

    Route::group([
        'middleware' => 'bound_phone',
    ], function () {
        //  - 我的购物车
        Route::resource('mine/carts', 'Mine\CartsController')->only(['index', 'store', 'update', 'destroy']);
        //  批量删除我的购物车
        Route::post('mine/carts/_batch_destroy', 'Mine\CartsController@batchDestroy');
        //  获取我的默认收货地址
        Route::get('mine/default-address', 'Mine\DefaultAddressController@index');
        //  - 我的收货地址
        Route::resource('mine/addresses', 'Mine\AddressesController');
        //  - 我的订单
        Route::resource('mine/orders', 'Mine\OrdersController');
    });
});

//  微信用户授权认证
Route::post('authenticated-wechat-users', 'AuthenticatedWechatUsersController@store');
