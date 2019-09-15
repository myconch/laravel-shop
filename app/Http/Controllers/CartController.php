<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AddCartRequest;
use App\Models\CartItem;
use App\Models\ProductSku;
use App\Services\CartService;

class CartController extends Controller
{
    protected $cartService;

    //利用laravel的自动解析功能注入CartService类
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function add(AddCartRequest $request)
    {
        /*
        $user = $request->user();
        $skuId = $request->input('sku_id');
        $amount = $request->input('amount');

        //从数据库中查询该商品是否已经在购物车中
        if ($cart = $user->cartItems()->where('product_sku_id',$skuId)->first()){
            //如果存在则直接叠加商品数量
            $cart->update([
                'amount' => $cart->amount + $amount,
            ]);
        } else {
            //否则创建一个新的购物记录
            $cart = new CartItem(['amount'=>$amount]);
            $cart->user()->associate($user);
            $cart->productSku()->associate($skuId);
            $cart->save();
        }
        */
        $this->cartService->add($request->input('sku_id'),$request->input('amount'));

        return [];
    }

    public function index(Request $request)
    {
        $cartItems = $this->cartService->get();
        //with(['productSku.product']) 方法用来预加载购物车里的商品和 SKU 信息，减轻数据库负担，提高响应速度
        // Laravel 还支持通过 . 的方式加载多层级的关联关系，这里我们就通过 . 提前加载了与商品 SKU 关联的商品

        $addresses = $request->user()->addresses()->orderBy('last_used_at','desc')->get();

        return view('cart.index',['cartItems'=>$cartItems,'addresses'=>$addresses]);
    }

    public function remove(ProductSku $sku,Request $request)
    {
        //$request->user()->cartItems()->where('product_sku_id',$sku->id)->delete();
        $this->cartService->remove($sku->id);

        return [];
    }
}
