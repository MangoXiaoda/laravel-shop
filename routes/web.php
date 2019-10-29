<?php

Route::get('/', 'PagesController@root')->name('root');

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
});


