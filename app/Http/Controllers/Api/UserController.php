<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
//use App\Http\Controllers\Controller;
use App\Models\User;
use App\Transformers\UserTransformer;

class UserController extends Controller
{


    public function me()
    {
        //此处调用的Dingo\Api\Routing\Helpers 这个trait ，它提供了user方法。$this->user() 等同于 \Auth::guard('api')->user()
        return $this->response->item($this->user(),new UserTransformer());
    }
}
