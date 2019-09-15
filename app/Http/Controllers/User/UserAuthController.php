<?php
//文件位置：app/Http/Controllers/User/UserAuthController.php

//命名空间
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;  //哈希，对密码进行加密，以免数据库被攻击时密码被窃取
use App\Shop\Entity\User;

Class UserAuthController extends Controller {

    //注册页
    public function signUpPage(){
        $binding =[
            'title' => '注册',
        ];
        return view('auth.signUp',$binding);
        /*
         * view()模块，第一参数指定模板名称，而不同的文件夹用句点(.)去串接。第二个参数表示传给模板的数据
         * auth.signUp即表示模板文件为resources/views/auth/signUp.blade.php
         */
    }

    //处理注册数据
    public function signUpProcess(){

        //接收处理的数据,与$_POST的效果相同
        $input = request()->all();
        $test = [
            'content' => $_POST['email'],
        ];

        $input['password'] = Hash::make($input['password']);
        //dd($input);
        //dd()函数会终止程序运行
        //输出变量的相关信息
        var_dump($input);
        $users = User::create($input);
        var_dump($users);

        return view('test',$test);
        exit;
    }

    //登录页
    public function signInPage(){
        $binding = [
            'title' => '登录',
            'errorIn'=>0,
        ];
        return view('auth.signIn',$binding);
    }

    //登录数据处理
    public function signInProcess(){
        $input = request()->all();
        $secret = false;
        //获取用户数据
        $User = User::where('email',$input['email'])->first();
        //first()若数据不存在则返回null
        if ($User !== null){
            //检查密码是否正确
            $secret = Hash::check($input['password'],$User->password);
            //var_dump($is_password_correct)
        };
        if (!$secret || $User===null){
                //密码错误回传
                $binding = [
                    'title'=>'错误',
                    'errorIn'=>1,
                ];
            return redirect()
                ->route('signIn',$binding)  //route只能输入路由的别名
                ->withInput();  //withInput（）指代入原先用户输入的表单数据到重新定向的页面，配合old（）使用
        }else{
            $binding = [
                'user_id'=> $User->id,
                'nickName'=> $User->nickname,
            ];

            //Session保存数据
            session()->put($binding);
            return redirect('/user/shop-manage');
        }
    }

    //退出登录
    public function signOutProcess(){
        //清除session
        session()->forget('user_id');

        return redirect('/user/auth/sign-in');
    }

    //店铺后台
    public function shopManagePage(){
        //先判断是否登录
        if(session()->has('user_id')) {
            return view('auth.shopManage');
        }else{
            return redirect('user/auth/sign-in');
        }
    }

}


