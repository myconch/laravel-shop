<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Exceptions\InvalidRequestException;
use App\Models\OrderItem;
use App\Models\Category;
use App\Services\CategoryService;
use App\Services\ProductService;

class ProductsController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request){
        /*
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
        //dd($products);
        //paginate()分页取出数据,默认15个每页，此处为了显示好看，改为16个每页

        return view('products.index',[
            'products'=>$products,
            'filters' => [
                'search' => $search,
                'order'  => $order,
            ],
            //等价于 isset($category) ? $category : null
            'category' => $category ?? null,
            ]);
        */

        $response = $this->productService->get($request);
        return view('products.index',[
            'products'=>$response['products'],
            'filters'=> [
                'search'=>$response['search'],
                'order'=>$response['order'],
            ],
            'category'=>$response['category'],
        ]);
    }

    //商品详情页
    public function show(Product $product,Request $request) {

        //判断商品是否上架，若没有上架则抛出异常
        if (!$product->on_sale) {
            throw new InvalidRequestException('商品未上架');
        }

        $favored = false;
        //用户未登录是返回的是null，已登录时返回的是应用的用户对象
        if ($user = $request->user()) {
            //从当前用户已收藏的商品中搜索当前商品的id
            //boolval()函数用于把值转化为布尔值
            //favoriteProducts() 是多对多关联，在后面加上 find() 返回的是关联的模型，而不是中间表
            $favored = boolval($user->favoriteProducts()->find($product->id));

        }

        $reviews = OrderItem::query()
            ->with(['order.user','productSku'])  //预先加载关系
            ->where('product_id',$product->id)
            ->whereNotNull('reviewed_at')  //筛选出已评价的
            ->orderBy('reviewed_at','desc')  //按评价时间倒序
            ->limit(10)  //取出10条
            ->get();
        return view('products.show',['product' => $product,'favored'=>$favored,'reviews'=>$reviews]);
    }

    //收藏
    public function favor(Product $product,Request $request){

        //先判断用户是否已经收藏了此商品
        $user = $request->user();
        if ($user->favoriteProducts()->find($product->id)) {
            return [];
        }

        //attach()方法将当前用户和此商品关联起来
        //attach()方法参数可以是模型id，也可以是模型对象本身，此处也可写成 attach($product->id)
        $user->favoriteProducts()->attach($product);

        return [];
    }

    //取消收藏
    public function disfavor(Product $product,Request $request)
    {
        $user = $request->user();
        //detach()方法用于取消多对多的关联，用法与attach一致
        $user->favoriteProducts()->detach($product);

    }

    //收藏清单
    public function favorites(Request $request)
    {
        $products = $request->user()->favoriteProducts()->paginate(16);

        return view('products.favorites',['products'=>$products]);
    }

}
