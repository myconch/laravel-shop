<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
use App\Models\Master;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Transformers\ProductTransformer;
use Illuminate\Support\Facades\DB;
use App\Services\ProductService;

class ProductsController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    // 获取商品数据
    public function index(Request $request)
    {
        $response = $this->productService->get($request);
        return $response;
        //$products = Product::where('on_sale',1)->paginate(10);
        //return $this->response->paginator($products,new ProductTransformer());
    }

    // 获取商品详情
    public function show($product_id,Request $request)
    {
        $product = Product::with('skus','attributes')->where('id',$product_id)->first();
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
        // 判断是否有shopId
        /*
        if ($shopId = $request->shopId) {
            $master = Master::where('id',$shopId)->first();
            if ($master) {
                $product->price = $product->price * (1 + $master->percent);
                foreach ($product->skus as $sku) {
                    $sku->price = $sku->price * (1 + $master->percent);
                }
            }
        }
        */

        $array = [
            'product' => $product,
            'favored' => $favored
        ];
        return $array;
        //return $this->response->item($product,new ProductTransformer());
    }

    //收藏
    public function favor($product_id,Request $request)
    {
        //先判断用户是否已经收藏了此商品
        $user = $request->user();
        $product = Product::where('id',$product_id)
                            ->where('on_sale',1)->first();
        if (!$product) {
            throw new InvalidRequestException('商品未上架');
        }
        if ($user->favoriteProducts()->find($product->id)) {
            return [];
        }

        //attach()方法将当前用户和此商品关联起来
        //attach()方法参数可以是模型id，也可以是模型对象本身，此处也可写成 attach($product->id)
        $user->favoriteProducts()->attach($product);

        return [];
    }

    //取消收藏
    public function disfavor($product_id,Request $request)
    {
        $user = $request->user();
        //detach()方法用于取消多对多的关联，用法与attach一致
        $user->favoriteProducts()->detach($product_id);

    }

    //收藏清单
    public function favorites(Request $request)
    {
        $products = $request->user()->favoriteProducts()->paginate(16);

        return $products;
    }
}
