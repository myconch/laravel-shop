<?php

namespace App\Services;

use Auth;
use App\Models\CartItem;

class CartService
{
    public function get()
    {
        //直接用Auth::user()获取当前用户
        return Auth::user()->cartItems()->with(['productSku.product.attributes'])->get();
    }

    public function add($skuId,$amount)
    {
        $user = Auth::user();
        //数据库种查询该商品是否已经在购物车中
        if ($item = $user->cartItems()->where('product_sku_id',$skuId)->first()){
            //如果存在则叠加商品数量
            $item->update(['amount'=>$item->amount+$amount]);
        } else {
            //否则创建一个新的购物车记录
            $item = new CartItem(['amount'=>$amount]);
            $item->user()->associate($user);
            $item->productSku()->associate($skuId);
            $item->save();
        }

        return $item;
    }

    public function remove($skuIds)
    {
        //可以传单个ID，也可以传ID数组
        if (!is_array($skuIds)) {
            $skuIds = [$skuIds];
        }

        Auth::user()->cartitems()->whereIn('product_sku_id',$skuIds)->delete();
    }
}
