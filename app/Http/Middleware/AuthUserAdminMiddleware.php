<?php

namespace App\Http\Middleware;

use App\Shop\Entity\User;
use Closure;

class AuthUserAdminMiddleware
{
    //处理请求
    public function handle($request, Closure $next)
    {
        //预设不允许存取
        $is_allow_access = false;
        //取得用户编号
        $user_id = session()->get('user_id');

        if (!is_null($user_id)){
            //session有用户编号，取得用户数据
            $User = User::findOrFail($user_id);

            if ($User->type == 'A'){
                //是管理员允许存取
                $is_allow_access = true;
            }
        }

        if (!$is_allow_access){
            //若非管理员，重新定向页面，不再继续执行
            return redirect('/');
        }

        //运行存取，继续做下一个请求的处理
        return $next($request);
    }
}
