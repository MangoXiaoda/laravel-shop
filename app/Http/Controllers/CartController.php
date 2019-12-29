<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCartRequest;
use App\Models\CartItem;
use App\Models\ProductSku;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * 添加商品到购物车
     * @param AddCartRequest $req
     * @return array
     */
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

    /**
     * 购物车列表页
     * @param Request $req
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $req)
    {
        $cartItems = $req->user()->cartTimes()
            ->with(['productSku.product'])
            ->get();

        return view('cart.index', ['cartItems' => $cartItems]);
    }


    public function remove(ProductSku $sku, Request $req)
    {
        $req->user()->cartTimes()->where('product_sku_id', $sku->id)->delete();

        return [];
    }

}
