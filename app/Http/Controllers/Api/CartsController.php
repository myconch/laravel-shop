<?php

namespace App\Http\Controllers\Api;

use App\Models\ProductSku;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\CartService;
use App\Http\Requests\AddCartRequest;

class CartsController extends Controller
{
    protected $CartService;

    public function __construct(CartService $CartService)
    {
        return $this->CartService = $CartService;
    }

    // 获取购物车数据
    public function index ()
    {
        $Carts = $this->CartService->get();
        return $Carts;
    }

    // 添加商品到购物车
    public function add (AddCartRequest $request)
    {
        $this->CartService->add($request->sku_id,$request->amount);
        return 200;
    }

    public function remove(ProductSku $sku,Request $request)
    {
        //$request->user()->cartItems()->where('product_sku_id',$sku->id)->delete();
        $this->cartService->remove($sku->id);

        return [];
    }
}
