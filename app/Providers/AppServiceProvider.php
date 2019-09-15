<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Monolog\Logger;
use Yansongda\Pay\Pay;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //往服务容器中注入一个名为alipay的单例对象
        //$this->app->singleton() 往服务容器中注入一个单例对象，第一次从容器中取对象时会调用回调函数来生成对应的对象并保存到容器中，之后再去取的时候直接将容器中的对象返回。
        $this->app->singleton('alipay',function (){
            $config = config('pay.alipay');
            //$config['notify_url'] = route('payment.alipay.notify');
            $config['notify_url'] = ngrok_url('payment.alipay.notify');
            $config['return_url'] = route('payment.alipay.return');

            //判断当前项目运行环境是否为线上环境
            //app()->environment() 获取当前运行的环境，线上环境会返回 production
            if (app()->environment() !== 'production') {
                //启用开发模式，微信无开发模式
                $config['mode'] = 'dev';
                //日志级别设置为DEBUG
                $config['log']['level'] = Logger::DEBUG;
            } else {
                $config['log']['level'] = Logger::WARNING;
            }
            //调用Yansongda\Pay来创建一个支付宝支付对象
            return Pay::alipay($config);
        });

        $this->app->singleton('wechat_pay',function (){
            $config = config('pay.wechat');
            if (app()->environment() !== 'production') {
                $config['log']['level'] = Logger::DEBUG;
            } else {
                $config['log']['level'] = Logger::WARNING;
            }
            //调用Yansongda\Pay来创建一个微信支付对象
            return Pay::wechat($config);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //当啦laravel渲染products.index 和 products.show模板时就会使用CategoryTreeComposer 来注入类目树变量
        //laravel还支持通配符，例如 products.* 即代表当渲染products目录下的模板时都会执行这个 ViewComposer
        \View::composer(['products.index','products.show'],\App\Http\ViewComposers\CategoryTreeComposer::class);
    }
}
