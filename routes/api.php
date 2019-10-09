<?php

use Illuminate\Http\Request;

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

/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/

$api = app('Dingo\Api\Routing\Router');

$api->version('v1',[
    'namespace' => 'App\Http\Controllers\Api',
    'middleware' => 'serializer:array'
], function ($api){

    $api->group([
        //频率限制中间件
        'middleware' => 'api.throttle',
        'limit' => config('api.rate_limits.sign.limit'),
        'expires' => config('api.rate_limits.sign.expires'),
    ],function ($api) {
        //第三方登录
        $api->post('socials/{social_type}/authorizations','AuthorizationsController@socialStore')
            ->name('api.socials.authorizations.store');
        //小程序登录
        $api->post('weapp/authorizations','AuthorizationsController@weappStore')
            ->name('api.weapp.authorizations.store');
        //刷新token
        $api->put('authorizations/current','AuthorizationsController@update')
            ->name('api.authorizations.update');
        //删除token
        $api->delete('authorizations/current','AuthorizationsController@destroy')
            ->name('api.authorizations.destroy');
    });

    $api->group([
        'middleware' => 'api.throttle',
        'limit' => config('api.rate_limits.access.limit'),
        'expires' => config('api.rate_limits.access.expires'),
    ],function ($api){
        //游客可以访问的接口
        $api->get('products','ProductsController@index')
            ->name('api.products.index');
        $api->get('products/{product}','ProductsController@show')
            ->name('api.products.show');

        //需要token 验证
        $api->group([
            'middleware' => 'api.auth'
        ],function ($api) {
            //当前登录用户信息
            $api->get('user','UserController@me')
                ->name('api.user.show');
            //当前用户的地址
            $api->get('address','UserAddressesController@index')
                ->name('api.address.show');
            //编辑收货地址页面
            $api->get('address/{user_address}','UserAddressesController@edit')
                ->name('api.address.edit');
            //提交编辑的收货地址
            $api->put('address/{user_address}','UserAddressesController@update')
                ->name('api.address.update');
            //新增收货地址
            $api->post('address','UserAddressesController@store')
                ->name('api.address.store');
            //删除收货地址
            $api->delete('address/{user_address}','UserAddressesController@destroy')
                ->name('api.address.destroy');
            //购物车信息
            $api->get('cart','CartsController@index')
                ->name('api.cart.index');
            //新增购物车
            $api->post('cart','CartsController@add')
                ->name('api.cart.add');
            // 提交订单
            $api->post('order','OrdersController@store')
                ->name('api.order.store');
            // 查看订单
            $api->get('order','OrdersController@index')
                ->name('api.order.index');
            // 评价商品
            $api->get('order/{order_id}/review','OrdersController@review')
                ->name('api.order.review');
            // 提交评价
            $api->post('order/{order_id}/review','OrdersController@sendReview')
                ->name('api.order.sendReview');
        });
    });

} );


