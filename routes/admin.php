<?php
/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| 后台公共路由部分
|
*/

Route::group(['namespace'=>'Admin','prefix'=>'admin'],function (){
   Route::group(['prefix'=>'home'],function (){
       //主页
       Route::get('/console','IndexController@console')->name('admin.console');
   }) ;
});