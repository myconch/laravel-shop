<?php

namespace App\Services;

use App\Models\Category;

class CategoryService
{
    public function getCategoryTree($parentId = null,$allCategories = null)
    {
        if (is_null($allCategories)){
            //从数据库中一次性取出所有类目
            $allCategories = Category::all();
        }

        return $allCategories->where('parent_id',$parentId)
                            //遍历这些类目，并用返回值构建一个新的集合
                            ->map(function($category) use($allCategories) {
                                $data = ['id'=>$category->id,'name'=>$category->name];
                                if (!$category->is_directory) {
                                    return $data;
                                }
                                //否则递归调用本方法，将返回值放入‘children’字段中
                                $data['children'] = $this->getCategoryTree($category->id,$allCategories);

                                return $data;
                            });
    }
}