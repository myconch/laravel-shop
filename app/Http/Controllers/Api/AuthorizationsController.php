<?php

namespace App\Http\Controllers\Api;

use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\Api\WeappAuthorizationRequest;
use Dingo\Api\Routing\Helpers;
use App\Http\Requests\Api\SocialAuthorizationRequest;

class AuthorizationsController extends Controller
{
    use Helpers;

    public function socialStore($type,SocialAuthorizationRequest $request)
    {
        if (!in_array($type,['weixin'])){
            return $this->response->errorBadRequest();
        }

        $driver = \Socialite::driver($type);

        try {
            if ($code = $request->code) {
                $response = $driver->getAccessTokenResponse($code);
                $token = array_get($response,'access_token');
            } else {
                $token = $request->access_token;

                //如果是使用微信登录，还需要传入openid
                if ($type == 'weixin') {
                    $driver->setOpenId($request->openid);
                }
            }

            $oauthUser = $driver->userFromToken($token);
        } catch (\Exception $e) {
            return $this->response->errorUnauthorized('参数错误，未获取用户信息');
        }

        switch ($type) {
            case 'weixin' :
                $unionid = $oauthUser->offsetExists('unionid') ? $oauthUser->offsetGet('unionid') : null;

                if ($unionid) {
                    $user = User::where('weixin_unionid',$unionid)->first();
                } else {
                    $user = User::where('weixin_openid',$oauthUser->getId())->first();
                }

                //如果没有用户，默认创建一个用户
                if (!$user) {
                    $user = User::create([
                        'name' => $oauthUser->getNickname(),
                        'email' => $oauthUser->getNickname().'@qq.cpm',
                        //'avatar' => $oauthUser->getAvatar(),
                        'weixin_openid' => $oauthUser->getId(),
                        'weixin_unionid' => $unionid,
                    ]);
                }

                break;
        }

        return $this->response->array(['token' => $user->id]);
    }

    public function weappStore(WeappAuthorizationRequest $request)
    {
        $code = $request->code;

        //根据code获取openid和session_key
        $miniProgram = \EasyWeChat::miniProgram();
        $data = $miniProgram->auth->session($code);

        //如果结果错误，说明code过期或不正确，返回401错误
        if (isset($data['errcode'])){
            return $this->response->errorUnauthorized('code 不正确');
        }

        //找到对应openid的用户
        $user = User::where('weapp_openid',$data['openid'])->first();

        $attributes['weixin_session_key'] = $data['session_key'];
        $array = array();

        //未找到对应用户则需要提交用户名密码进行用户绑定
        if (!$user) {
            //如果未提交用户名密码，403，错误提示
            if (!$request->username) {
                return $this->response->errorForbidden('用户不存在');
            }

            //如果是采用微信号登录的，就为其新建一个账户
            if ($request->sign) {
                $user = User::create([
                    'name' => $request->username,
                    'email' => $request->username.'@qq.com',
                    'avatar' => $request->avatarUrl,
                    'sex' => $request->sex
                ]);
                $attributes['weapp_openid'] = $data['openid'];
            } else {

                $username = $request->username;

                //用户名可以是邮箱或电话
                filter_var($username,FILTER_VALIDATE_EMAIL) ? $credentials['email'] = $username : $credentials['phone'] = $username;

                $credentials['password'] = $request->password;

                //验证用户名和密码是否正确
                if (!Auth::guard('api')->once($credentials)) {
                    //dd('这是一个错误');
                    return $this->response->errorUnauthorized('用户名或密码错误');
                }

                //获取对应的用户
                $user = Auth::guard('api')->getUser();
                $attributes['weapp_openid'] = $data['openid'];
            }
        }

        //更新用户数据
        $user->update($attributes);

        //为对应用户创建JWT
        $token = Auth::guard('api')->fromUser($user);
        //$token = Auth::guard('api')->once($credentials);

        return $this->respondWithToken($token)->setStatusCode(201);
    }

    protected function respondWithToken($token)
    {
        return $this->response->array([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => \Auth::guard('api')->factory()->getTTL()*60
        ]);
    }

    //刷新token
    public function update()
    {
        $token = Auth::guard('api')->refresh();
        return $this->respondWithToken($token);
    }

    //删除token
    public function destroy()
    {
        //当token过期时，logout()将会报错，因此将token先刷新，再退出
        try {
            Auth::guard('api')->logout();
        } catch (\Exception $e) {
            Auth::guard('api')->refresh();
            Auth::guard('api')->logout();
        }
        return $this->response->noContent();
    }
}
