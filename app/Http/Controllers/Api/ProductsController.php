<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
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
    public function show($product_id)
    {
        $product = Product::with('skus','attributes')->where('id',$product_id)->first();
        if (!$product->on_sale) {
            throw new InvalidRequestException('商品未上架');
        }
        return $product;
        //return $this->response->item($product,new ProductTransformer());
    }
}
