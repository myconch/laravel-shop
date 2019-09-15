<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//我们希望用户一进来就能看到商品列表，因此让首页直接跳转到商品页面
/* 删除原先的首页路由
Route::get('/', 'PagesController@root')
        ->name('root');
*/
Route::redirect('/','/products')
        ->name('root');


//用户
Route::group(['prefix'=>'user'],function (){
    //用户认证
    Route::group(['prefix'=>'auth'],function (){
        //用户注册界面
        Route::get('/sign-up','User\UserAuthController@signUpPage');
        //用户数据新增
        Route::post('/sign-up','User\UserAuthController@signUpProcess');

        //用户登录界面
        Route::get('/sign-in','User\UserAuthController@signInPage')->name('signIn');
        //用户登录数据处理
        Route::post('/sign-in','User\UserAuthController@signInProcess');

        //退出登录
        Route::get('/sign-out','User\UserAuthController@signOutProcess');
    });

    //店铺管理
    Route::get('/shop-manage','User\UserAuthController@shopManagePage');

});

//商品merchandise
Route::group(['namespace'=>'Merchandise','prefix'=>'merchandise'],function (){
    //商品清单检视
    Route::get('/','MerchandiseController@merchandiseListPage');
    //商品数据新增
    Route::get('/create','MerchandiseController@merchandiseCreateProcess')
        ->name('merchandise.create')
        ->middleware(['user.auth.admin']);
    //商品管理清单检视
    Route::get('/manage','MerchandiseController@merchandiseManageListPage')
        ->name('merchandise.manage')
        ->middleware(['user.auth.admin']);  //添加中间件，验证是否为管理员

    //指定商品
    Route::group(['prefix'=>'{merchandise_id}'],function (){
        //商品单品检视
        Route::get('/','MerchandiseController@merchandiseItemPage');
        //商品单品编辑页面检视
        Route::get('/edit','MerchandiseController@merchandiseItemEditPage');
        //商品单品数据修改
        Route::put('/','MerchandiseController@merchandiseItemUpdateProcess');
        //购买商品
        Route::post('/buy','MerchandiseController@merchandiseItemBuyProcess');
    });
});

//用户认证路由
Auth::routes(['verify'=>true]);

//收获地址
//auth中间件表示需要登录，verified中间件表示需要经过邮箱认证
Route::group(['middleware'=>['auth','verified']],function(){
    //收货地址
    Route::get('user_addresses','userAddressesController@index')
            ->name('user_addresses.index');
    //新增收货地址
    Route::get('user_addresses/create','UserAddressesController@create')
            ->name('user_addresses.create');
    //提交收货地址数据
    Route::post('user_addresses','UserAddressesController@store')
            ->name('user_addresses.store');
    //编辑地址页面
    Route::get('user_address/{user_address}','UserAddressesController@edit')
            ->name('user_addresses.edit');
    //编辑地址提交
    Route::put('user_addresses/{user_address}','UserAddressesController@update')
            ->name('user_addresses.update');
    //删除地址
    Route::delete('user_addresses/{user_address}','UserAddressesController@destroy')
            ->name('user_addresses.destroy');
    //收藏商品
    Route::post('products/{product}/favorite','ProductsController@favor')
            ->name('products.favor');
    //取消收藏
    Route::delete('products/{product}/favorite','ProductsController@disfavor')
            ->name('products.disfavor');
    //收藏清单
    Route::get('products/favorites','ProductsController@favorites')
            ->name('products.favorites');
    //添加到购物车
    Route::post('cart','CartController@add')
            ->name('cart.add');
    //购物车页面
    Route::get('cart','CartController@index')
            ->name('cart.index');
    //购物车移除按钮
    Route::delete('cart/{sku}','CartController@remove')
            ->name('cart.remove');
    //提交订单
    Route::post('orders','OrdersController@store')
            ->name('orders.store');
    //查看订单
    Route::get('orders','OrdersController@index')
            ->name('orders.index');
    //查看订单详情
    Route::get('orders/{order}','OrdersController@show')
            ->name('orders.show');
    //支付宝支付
    Route::get('payment/{order}/alipay','PaymentController@payByAlipay')
            ->name('payment.alipay');
    //支付宝前端回调
    Route::get('payment/alipay/return','PaymentController@alipayReturn')
            ->name('payment.alipay.return');
    //用户确认收货
    Route::post('orders/{order}/received','OrdersController@received')
            ->name('orders.received');
    //用户评价页
    Route::get('orders/{order}/review','OrdersController@review')
            ->name('orders.review.show');
    //提交用户评价
    Route::post('orders/{order}/review','OrdersController@sendReview')
            ->name('orders.review.store');
    //申请退款
    Route::post('orders/{order}/apply_refund','OrdersController@applyRefund')
            ->name('orders.apply_refund');
    //检查优惠券
    Route::get('coupon_codes/{code}','CouponCodesController@show')
            ->name('coupon_codes.show');
    //选择分期支付
    Route::post('payment/{order}/installment','PaymentController@payByInstallment')
            ->name('payment.installment');
    //查看分期支付
    Route::get('installments','InstallmentsController@index')
            ->name('installments.index');
    //查看分期支付详情页
    Route::get('installments/{installment}','InstallmentsController@show')
            ->name('installments.show');
    //分期付款支付宝支付
    Route::get('installments/{installment}/alipay','InstallmentsController@payByAlipay')
            ->name('installments.alipay');
    //分期支付支付宝前端回调
    Route::get('installments/alipay/return','InstallmentsController@alipayReturn')
            ->name('installments.alipay.return');
});
//支付宝服务端回调
Route::post('payment/alipay/notify','PaymentController@alipayNotify')
            ->name('payment.alipay.notify');
//分期支付支付宝服务端回调
Route::post('installments/alipay/notify','InstallmentsController@alipayNotify')
            ->name('installments.alipay.notify');

//商品路由,游客也能访问，因此不要auth中间件
Route::get('products','ProductsController@index')
        ->name('products.index');
//商品详情页
Route::get('products/{product}','ProductsController@show')
        ->name('products.show');

// wangEditor 图片上传
Route::post('/uploadFile','UploadsController@uploadImg');

