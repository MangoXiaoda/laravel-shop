<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('admin.home');

    // 用户管理
    $router->get('users', 'UsersController@index');

    // 商品管理
    $router->get('products', 'ProductsController@index');

    // 添加商品
    $router->get('products/create', 'ProductsController@create');
    $router->post('products', 'ProductsController@store');

    // 编辑商品
    $router->get('products/{id}/edit', 'ProductsController@edit');
    $router->put('products/{id}', 'ProductsController@update');

    // 订单列表
    $router->get('orders', 'OrdersController@index')
        ->name('admin.orders.index');

    // 订单详情
    $router->get('orders/{order}','OrdersController@show')
        ->name('admin.orders.show');

    // 订单发货
    $router->post('orders/{order}/ship', 'OrdersController@ship')
        ->name('admin.orders.ship');

    // 拒绝退款
    $router->post('orders/{order}/refund', 'OrdersController@handleRefund')
        ->name('admin.orders.handle_refund');

    // 优惠券列表
    $router->get('coupon_codes', 'CouponCodesController@index');

    // 添加优惠券路由
    $router->post('coupon_codes', 'CouponCodesController@store');
    $router->get('coupon_codes/create', 'CouponCodesController@create');

    // 修改优惠券路由
    $router->get('coupon_codes/{id}/edit', 'CouponCodesController@edit');
    $router->put('coupon_codes/{id}', 'CouponCodesController@update');

    // 删除优惠券路由
    $router->delete('coupon_codes/{id}', 'CouponCodesController@destroy');

});
