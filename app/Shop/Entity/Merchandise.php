<?php
namespace App\Shop\Entity;

use Illuminate\Database\Eloquent\Model;

class Merchandise extends Model {
    //数据表名称
    protected $table ='merchandise';
    //主键名称
    protected $primaryKey = 'id';
    //可以大量指定变更字段
    protected $fillable =[
        "id",
        "status",
        "name",
        "type",
        "introduction",
        "merchandise_photo",
        "photo",
        "detail_photo",
        "price",
        "remain_count",
    ];

}
