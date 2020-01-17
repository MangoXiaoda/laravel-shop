<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCartRequest;
use App\Models\CartItem;
use App\Models\ProductSku;
use Illuminate\Http\Request;
use App\Services\CartService;

class CartController extends Controller
{

    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * 添加商品到购物车
     * @param AddCartRequest $req
     * @return array
     */
    public function add(AddCartRequest $req)
    {
        $this->cartService->add($req->input('sku_id'), $req->input('amount'));

        return [];
    }

    /**
     * 购物车列表页
     * @param Request $req
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $req)
    {
        $cartItems = $this->cartService->get();

        $addresses = $req->user()->addresses()->orderby('last_used_at', 'desc')->get();

        return view('cart.index', ['cartItems' => $cartItems, 'addresses' => $addresses]);
    }


    public function remove(ProductSku $sku, Request $req)
    {
        $this->cartService->remove($sku->id);

        return [];
    }

}
