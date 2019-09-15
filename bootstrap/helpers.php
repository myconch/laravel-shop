<?php

//此方法会将当前请求的路由名称转换为CSS类名称
//作用：允许我们针对某个页面做页面样式定制
function route_class(){

    return str_replace('.','-',Route::currentRouteName());
}

//默认的精度为小数点后两位
function big_number($number,$scale=2)
{
    return new \Moontoast\Math\BigNumber($number,$scale);
}

function ngrok_url($routeName,$parameters = [])
{
    //开发环境，并配置了 NGROK_URL
    if (app()->environment('local') && $url = config('app.ngrok_url')) {
        //route()函数第三个参数代表是否绝对路径
        return $url.route($routeName,$parameters,false);
    }

    return route($routeName,$parameters);
}