<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserAddressRequest;
use Illuminate\Http\Request;
use App\Models\UserAddress;

class UserAddressesController extends Controller
{
    //收货地址列表
    public function index(Request $request){

        //将当前用户下的所有地址作为变量$addresses注入到模板user_addresses.index中
        return view('user_addresses.index',
            ['addresses' => $request->user()->addresses,]);
    }

    //新增收货地址页
    public function create(){

        return view('user_addresses.create_and_edit',['address'=>new UserAddress()]);
    }

    //新增收货地址提交
    public function store(UserAddressRequest $request){

        $request->user()->addresses()->create($request->only([
            'province',
            'city',
            'district',
            'address',
            'zip',
            'contact_name',
            'contact_phone',
        ]));

        return redirect()->route('user_addresses.index');
    }

    //编辑地址页
    public function edit(UserAddress $user_address){

        $this->authorize('own',$user_address);

        return view('user_addresses.create_and_edit',['address'=>$user_address]);
    }

    //提交编辑的地址
    public function update(UserAddress $user_address,UserAddressRequest $request){

        $this->authorize('own',$user_address);

        $user_address->update($request->only([
            'province'
            ,'city'
            ,'district'
            ,'address'
            ,'zip'
            ,'contact_name'
            ,'contact_phone',
        ]));

        return redirect()->route('user_addresses.index');
    }

    //删除地址
    public function destroy(UserAddress $user_address){

        $this->authorize('own',$user_address);

        $user_address->delete();
        //删除接口的请求方式从表单请求改成了AJAX请求，因此将之前的redirect改为返回空数组
        return [];
    }
}
