<?php

Route::get('/', 'PagesController@root')->name('root');

// 加入邮箱验证规则
Auth::routes(['verify' => true]);

