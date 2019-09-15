<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserAddressRequest extends Request
{
    /**
     * 验证规则.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'province' => 'required',
            'city' => 'required',
            'district' => 'required',
            'address' => 'required',
            'contact_name' => 'required',
            'contact_phone' => 'required',
        ];
    }

    //自定义属性名称替换验证消息，可以用作汉化
    public function attributes()
    {
        return [
            'province' =>'省'
            ,'city' => '城市'
            ,'district' => '地区'
            ,'address' => '详细地址'
            ,'zip' => '邮编'
            ,'contact_name' => '姓名'
            ,'contact_phone' => '电话',
        ];
    }
}
