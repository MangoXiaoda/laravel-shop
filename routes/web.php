<?php

// Route::get('/', 'PagesController@root')->name('root');

// 首页商品列表
Route::redirect('/', '/products')->name('root');
Route::get('products', 'ProductsController@index')
    ->name('products.index');

// 商品详情页
Route::get('products', 'ProductsController@index')
    ->name('products.index');

// 临时测试支付
//Route::get('alipay', function(){
//    return app('alipay')->web([
//        'out_trade_no' => time(),
//        'total_amount' => '1',
//        'subject' => 'test subject - 测试',
//    ]);
//});

// 加入邮箱验证规则
Auth::routes(['verify' => true]);

// auth 中间件代表需要登录，verified中间件代表需要经过邮箱验证
Route::group(['middleware' => ['auth', 'verified']], function(){
    Route::get('user_addresses', 'UserAddressesController@index')
        ->name('user_addresses.index');

    Route::get('user_addresses/create', 'UserAddressesController@create')
        ->name('user_addresses.create');

    Route::post('user_addresses', 'UserAddressesController@store')
        ->name('user_addresses.store');

    Route::get('user_addresses/{user_address}', 'UserAddressesController@edit')
        ->name('user_addresses.edit2');

    Route::put('user_addresses/{user_address}', 'UserAddressesController@update')
        ->name('user_addresses.update');

    Route::delete('user_addresses/{user_address}', 'UserAddressesController@destroy')
        ->name('user_addresses.destroy');

    Route::post('products/{product}/favorite', 'ProductsController@favor')
        ->name('products.favor');

    Route::delete('products/{product}/favorite', 'ProductsController@disfavor')
        ->name('products.disfavor');

    Route::get('products/favorites', 'ProductsController@favorites')
        ->name('products.favorites');

    // 添加购物车
    Route::post('cart', 'CartController@add')
        ->name('cart.add');

    // 购物车列表页
    Route::get('cart', 'CartController@index')
        ->name('cart.index');

    // 移除购物车
    Route::delete('cart/{sku}', 'CartController@remove')
        ->name('cart.remove');

    // 订单
    Route::post('orders', 'OrdersController@store')
        ->name('orders.store');

    // 订单列表页
    Route::get('orders', 'OrdersController@index')
        ->name('orders.index');

    // 用户订单详情页
    Route::get('orders/{order}', 'OrdersController@show')
        ->name('orders.show');

    // 支付宝支付
    Route::get('payment/{order}/alipay', 'PaymentController@payByAlipay')
        ->name('payment.alipay');

    // 支付宝前端回调
    Route::get('payment/alipay/return', 'PaymentController@alipayReturn')
        ->name('payment.alipay.return');

    // 微信支付
    Route::get('payment/{order}/wechat', 'PaymentController@payByWechat')
        ->name('payment.wechat');

    // 用户确认收获
    Route::post('orders/{order}/received', 'OrdersController@received')
        ->name('orders.received');

    // 展示评价
    Route::get('orders/{order}/review', 'OrdersController@review')
        ->name('orders.review.show');

    // 提交评价
    Route::post('orders/{order}/review', 'OrdersController@sendReview')
        ->name('orders.review.store');

    // 申请退款
    Route::post('orders/{order}/apply_refund', 'OrdersController@applyRefund')
        ->name('orders.apply_refund');

    // 检查优惠券路由
    Route::get('coupon_codes/{code}', 'CouponCodesController@show')
        ->name('coupon_codes.show');
});

// 支付宝服务端回调
Route::post('payment/alipay/notify', 'PaymentController@alipayNotify')
    ->name('payment.alipay.notify');

// 微信支付服务端回调
Route::post('payment/wechat/notify', 'PaymentController@wechatNotify')
    ->name('payment.wechat.notify');

// 微信支付退款回调路由
Route::post('payment/wechat/refund_notify', 'PaymentController@wechatRefundNotify')
    ->name('payment.wechat.refund_notify');

Route::get('products/{product}', 'ProductsController@show')
    ->name('products.show');


