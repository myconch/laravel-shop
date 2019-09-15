<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name','is_directory','level','path'
    ];
    protected $casts = [
        'is_directory' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        //监听 Category 的创建事件，用于初始化 path 和 level 字段
        static::creating (function (Category $category){
            //如果创建的是一个根类目
            if (is_null($category->parent_id)){
                //将层级设置为0
                $category->level = 0;
                //将path设置为 -
                $category->path = '-';
            } else {
                //将层级设置为父类目 +1
                $category->level = $category->parent->level +1;
                //将path设置为父类目path，并追加父类目的id及跟上一个 -
                $category->path = $category->parent->path.$category->parent->id.'-';
            }
        });

    }

    public function parent()
    {
        return $this->belongsTo(Category::class);
    }

    public function children()
    {
        return $this->hasMany(Category::class,'parent_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    //定义一个访问器，获取所有祖先类目的ID值
    public function getPathIdsAttribute()
    {
        // trim($str, '-') 将字符串两端的 - 符号去除
        // explode() 将字符串以 - 为分隔切割为数组
        // 最后 array_filter 将数组中的空值移除
        return array_filter(explode('-',trim($this->path,'-')));
    }

    //定义一个访问器，获取所有祖先类目，并按层级排序
    public function getAncestorsAttribute()
    {
        return Category::query()
            //使用上面的访问器获取所有祖先类目的ID
            ->whereIn('id',$this->path_ids)
            ->orderBy('level')
            ->get();
    }

    //定义个访问器，获取以 - 为分隔的所有祖先类目名称及当前类目的名称
    public function getFullNameAttribute()
    {
        return $this->ancestors
            ->pluck('name')
            ->push($this->name)
            ->implode('-');  //用 - 符号将数组的值组装成一个字符串
    }

}
