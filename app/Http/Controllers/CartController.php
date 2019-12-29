<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCartRequest;
use App\Models\CartItem;
use Illuminate\Http\Request;

class CartController extends Controller
{

    public function add(AddCartRequest $req)
    {
        $user   = $req->user();
        $skuId  = $req->input('sku_id');
        $amount = $req->input('amount');

        // 从数据中查询该商品是否已经在购物车中
        if ($cart = $user->cartTimes()->where('product_sku_id', $skuId)->first()){

            // 如果存在则直接叠加商品数量
            $cart->update([
               'amount' => $cart->amount + $amount,
            ]);

        } else {
            // 没有 创建一个新的购物车记录
            $cart = new CartItem(['amount' => $amount]);
            $cart->user()->associate($amount);
            $cart->productSku()->associate($skuId);
            $cart->save();
        }

        return [];
    }

}
