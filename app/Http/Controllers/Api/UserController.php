<?php

namespace App\Http\Controllers\Api;

use App\Models\Master;
use Illuminate\Http\Request;
//use App\Http\Controllers\Controller;
use App\Models\User;
use App\Transformers\UserTransformer;
use App\Http\Requests\MasterRequest;

class UserController extends Controller
{


    public function me()
    {
        return $this->user()->load('master');
        //此处调用的Dingo\Api\Routing\Helpers 这个trait ，它提供了user方法。$this->user() 等同于 \Auth::guard('api')->user()
        return $this->response->item($this->user(),new UserTransformer());
    }

    // master表存储
    public function masterStore(MasterRequest $request)
    {
        $request->user()->master()->create($request->only([
            'percent',
            'signboard',
            'layout'
        ]));
    }

    // 编辑master表
    public function masterEdit(MasterRequest $request)
    {
        $request->user()->master()->update($request->only([
            'percent',
            'signboard',
            'layout'
        ]));
    }

    // 获取master信息
    public function master($master_id)
    {
        $master = Master::where('id',$master_id)->first();
        return $master;
    }
}
