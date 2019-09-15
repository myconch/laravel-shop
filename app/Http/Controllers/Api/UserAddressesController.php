<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\UserAddress;
use App\Http\Requests\UserAddressRequest;
use App\Transformers\UserAddressTransformer;

class UserAddressesController extends Controller
{
    public function index()
    {
        return $this->response->item($this->user()->addresses()->orderBy('last_used_at','desc')->get(),new UserAddressTransformer());
    }

    public function edit($user_address_id)
    {
        $user_address = UserAddress::where('id',$user_address_id)->first();
        //授权验证
        $this->authorize('own',$user_address);
        return $this->response->item($user_address,new UserAddressTransformer());
    }

    public function update($user_address_id,UserAddressRequest $request)
    {
        $user_address = UserAddress::where('id',$user_address_id)->first();
        //授权验证
        $this->authorize('own',$user_address);
        $user_address->update($request->only([
            'province'
            ,'city'
            ,'district'
            ,'address'
            ,'contact_name'
            ,'contact_phone',
        ]));

        //返回值还未写好
        return 201;
    }

    public function store(UserAddressRequest $request)
    {
        $this->user()->addresses()->create($request->only([
            'province',
            'city',
            'district',
            'address',
            'contact_name',
            'contact_phone',
        ]));
    }

    public function destroy($user_address_id)
    {
        $user_address = UserAddress::where('id',$user_address_id)->first();
        $this->authorize('own',$user_address);
        $user_address->delete();

        //返回还没写好
        return;
    }
}
