<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'title','description','image','on_sale',
        'rating','sold_count','review_count','price'
    ];

    protected $casts = [
        'on_sale' => 'boolean',  //on_sale是一个布尔类型的字段
    ];

    //与商品sku关联
    public function skus(){

        return $this->hasMany(ProductSku::class);
    }

    //与商品规格Attribute关联
    public function attributes(){
        return $this->hasMany(ProductAttribute::class);
    }

    public function getImageUrlAttribute(){

        //如果image本身就已经是完美的url就直接返回
        if (Str::startsWith($this->attributes['image'],['http://','https://'])) {
            return $this->attributes['image'];
        }

        //Storage::disk('public')访问对应的磁盘，参数 public 需要和我们在 config/admin.php 里面的 upload.disk 配置一致
        return \Storage::disk('public')->url($this->attributes['image']);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /*
    public function getImageAttribute($image)
    {
        return json_decode($image,true);
    }

    public function setImageAttribute($image)
    {
        if (is_array($image)) {
            return $this->attributes['image'] = json_encode($image);
        }
    }
    */
}
