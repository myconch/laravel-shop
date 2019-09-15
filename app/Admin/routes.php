<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');
    $router->get('users','UsersController@index');

    //****商品
    $router->get('products','ProductsController@index');
    $router->get('products/create','ProductsController@create');

    //ProductsController 中并没有 store 方法，这是因为 Laravel-Admin 在创建控制器的时候默认引入了 HasResourceActions 这个 Trait
    //打开 Encore\Admin\Controllers\HasResourceActions 这个类可以看到里面定义了 store 方法
    $router->post('products','ProductsController@store');

    $router->get('products/{id}/edit','ProductsController@edit');
    $router->put('products/{id}','ProductsController@update');
    //控制器中的 update() 方法也是来自 HasResourceActions 这个 Trait

    //****订单
    $router->get('orders','OrdersController@index')
            ->name('admin.orders.index');
    $router->get('orders/{order}','OrdersController@show')
            ->name('admin.orders.show');
    $router->post('orders/{order}/ship','OrdersController@ship')
            ->name('admin.orders.ship');
    $router->post('orders/{order}/refund','OrdersController@handleRefund')
            ->name('admin.orders.handle_refund');

    //****优惠券
    $router->get('coupon_codes','CouponCodesController@index');
    //CouponCodesController 中没有store方法，store 方法在控制器引入的 HasResourceActions 中
    $router->post('coupon_codes','CouponCodesController@store');
    $router->get('coupon_codes/create','CouponCodesController@create');
    $router->get('coupon_codes/{id}/edit','CouponCodesController@edit');
    $router->put('coupon_codes/{id}','CouponCodesController@update');
    $router->delete('coupon_codes/{id}','CouponCodesController@destroy');

    //****商品类目
    $router->get('categories','CategoriesController@index')
            ->name('admin.categories.index');
    $router->get('categories/create','CategoriesController@create')
            ->name('admin.categories.create');
    $router->post('categories','CategoriesController@store');
    $router->get('categories/{id}/edit','CategoriesController@edit')
            ->name('admin.categories.edit');
    $router->put('categories/{id}','CategoriesController@update');
    $router->delete('categories/{id}','CategoriesController@destroy');
});
