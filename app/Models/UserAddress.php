<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{

    protected $fillable = [
        'province',
        'city',
        'district',
        'address',
        'zip',
        'contact_name',
        'contact_phone',
        'last_used_at',
    ];

    protected $dates = ['last_used_at'];  //表示last_used_at字段是一个时间日期类型
    //在之后的代码中 $address->last_used_at 返回的就是一个时间日期对象

    //public function user() 与 User 模型关联，User模型中也应关联上UserAddress模型
    public function user(){

        return $this->belongsTo(User::class);
    }

    //在之后的代码里可以直接通过 $address->full_address 来获取完整的地址，而不用每次都去拼接
    public function getFullAddressAttribute(){

        return "{$this->province}{$this->city}{$this->district}{$this->address}";
    }
}
