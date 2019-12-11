<?php

// Route::get('/', 'PagesController@root')->name('root');

// 首页商品列表
Route::redirect('/', '/products')->name('root');
Route::get('products', 'ProductsController@index')->name('products.index');

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
        ->name('user_addresses.edit');

    Route::put('user_addresses/{user_address}', 'UserAddressesController@update')
        ->name('user_addresses.update');

    Route::delete('user_addresses/{user_address}', 'UserAddressesController@destroy')
        ->name('user_addresses.destroy');

});


