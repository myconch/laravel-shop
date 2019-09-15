<?php
namespace App\Shop\Entity;

use Illuminate\Database\Eloquent\Model;

class User extends Model {
    //数据表名称
    protected $table ='users';
    //主键名称
    protected $primaryKey = 'id';
    //可以大量指定变更字段
    protected $fillable =[
        "email",
        "password",
        "nickname"
    ];

}
