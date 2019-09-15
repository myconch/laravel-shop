<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements MustVerifyEmailContract, JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','weixin_openid','weixin_unionid','weixin_session_key','weapp_openid','avatar','sex','phone'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function addresses(){

        return $this->hasMany(UserAddress::class);
    }

    public function favoriteProducts(){

        //belongsToMany()方法用于定义一个多对多的关联，第一个参数是关联模型的类名，第二个参数是中间表的表名
        return $this->belongsToMany(Product::class,'user_favorite_products')
                ->withTimestamps()
                ->orderBy('user_favorite_products.created_at','desc');
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function getJWTIdentifier()
    {
        return $this->getkey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
