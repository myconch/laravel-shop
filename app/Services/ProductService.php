<?php
namespace App\Services;

use App\Models\Category;
use Auth;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductService
{
    public function get(Request $request) {
        //创建一个查询构造器
        $builder = Product::query()->where('on_sale',true);
        //$category = Category::query();

        //判断是否有提交search参数，如果有就赋值给$search变量
        //search 参数用来模糊搜索商品
        if ($search = $request->input('search','')){
            $like = '%'.$search.'%';
            //模糊搜素商品标题、详情、sku标题、sku描述
            $builder->where(function ($query) use ($like) {
                $query->where('title','like',$like)
                    ->orWhere('description','like',$like)
                    ->orWhereHas('skus',function ($query) use ($like) {
                        $query->where('title','like',$like)
                            ->orWhere('description','like',$like);
                    });
            });
        }

        //判断是否有提交 category_id 参数
        if ($request->input('category_id') && $category=
                Category::find($request->input('category_id'))) {
            //判断对应的id是否有子目录
            if (!$category->is_directory){
                $builder->where('category_id',$category->id);
            } else {
                /*
                $array = $category->where('is_directory',0)
                        ->where('path','like','%-'.$id.'-%')
                        ->pluck('id');
                $builder->whereIn('category_id',$array);
                */
                //上面是之前写的代码，功能可以实现但较为繁琐，下面是通过向关联模型中添加约束，通过whereHas来实现
                $builder->whereHas('category',function($query) use($category){
                    //因为products中对应的category_id都必须是没有子目录的，因此不需要约束is_directory=0
                    $query->where('path','like',$category->path.$category->id.'-%');
                });
            }
        }

        //是否有提交order参数，如果有就赋值给$order
        //order参数用来控制商品排序规则
        if ($order = $request->input('order','')) {
            //是否以_asc或_desc结尾
            if (preg_match('/^(.+)_(asc|desc)$/',$order,$m)) {
                //如果字符串是这三个字符之一，说明是一个合法的排序
                if (in_array($m[1],['price','sold_count','rating'])) {
                    //根据传入的排序值来构造排序参数
                    $builder->orderBy($m[1],$m[2]);
                }
            }
        }

        $products = $builder->paginate(16);

        $response = [
            'products' => $products,
            'search' => $search,
            'order' => $order,
            'category' => $category ?? null,
        ];
        return $response;
    }
}
