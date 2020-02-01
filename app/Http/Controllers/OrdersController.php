<?php

namespace App\Http\Controllers;

use App\Events\OrderReviewed;
use App\Http\Requests\ApplyRefundRequest;
use App\Http\Requests\OrderRequest;
use App\Http\Requests\SendReviewRequest;
use App\Listeners\SendOrderPaidMail;
use App\Models\ProductSku;
use App\Models\UserAddress;
use App\Models\Order;
use Carbon\Carbon;
use App\Exceptions\InvalidRequestException;
use App\Jobs\CloseOrder;
use Illuminate\Http\Request;
use App\Services\CartService;
use App\Services\OrderService;

class OrdersController extends Controller
{


    public function store(OrderRequest $request, OrderService $orderService)
    {
        $user = $request->user();
        $address = UserAddress::find($request->input('address_id'));

        return $orderService->store($user, $address, $request->input('remark'), $request->input('items'));
    }

    /**
     * 订单列表页
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $orders = Order::query()
            // 使用 with 方法预加载，避免N + 1问题
            ->with(['items.product', 'items.ProductSku'])
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate();

        return view('orders.index', ['orders' => $orders]);
    }

    /**
     * 订单详情页
     * @param Order $order
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Order $order, Request $request)
    {
        $this->authorize('own', $order);
        return view('orders.show',['order' => $order->load(['items.productSku','items.product'])]);
    }

    /**
     * 确认发货接口
     * @param Order $order
     * @param Request $request
     * @return Order
     * @throws InvalidRequestException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function received(Order $order, Request $request)
    {
        // 校验权限
        $this->authorize('own', $order);

        // 判断订单的发货状态是否为已发货
        if ($order->ship_status != $order::SHIP_STATUS_DELIVERED)
            throw new InvalidRequestException('发货状态不正确');

        // 更新发货状态为已发货
        $order->update(['ship_status' => $order::SHIP_STATUS_RECEIVED]);

        // 返回原页面
        return $order;
    }

    /**
     * 展示评价页面
     * @param Order $order
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws InvalidRequestException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function review(Order $order)
    {
        // 校验权限
        $this->authorize('own', $order);

        // 判断是否已经支付
        if (!$order->paid_at)
            throw new InvalidRequestException('该订单未支付，不能评价');

        // 使用 load方法加载数据 避免 N + 1 性能问题
        return view('orders.review',['order' => $order->load(['items.productSku','items.product'])]);
    }

    /**
     * 提交评价
     * @param Order $order
     * @param SendReviewRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws InvalidRequestException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function sendReview(Order $order, SendReviewRequest $request)
    {
        // 校验权限
        $this->authorize('own', $order);

        // 判断是否已支付
        if (!$order->paid_at)
            throw new InvalidRequestException('该订单未支付，不能评价');

        // 判断是否已经评价
        if ($order->reviewed)
            throw new InvalidRequestException('该订单已评价，不可重复提交');

        $reviews = $request->input('reviews');

        // 开启事物
        \DB::transaction(function () use($reviews, $order){
            // 遍历用户提交的数据
            foreach ($reviews as $review){
                $orderItem = $order->items()->find($review['id']);
                // 保存评分和评价
                $orderItem->update([
                    'rating'  => $review['rating'],
                    'review'  => $review['review'],
                    'reviewed_at' => Carbon::now()
                ]);
            }
            // 将订单标记为已评价
            $order->update(['reviewed' => true]);

            // 更新商品评价数
            event(new OrderReviewed($order));
        });

        return redirect()->back();
    }


    public function applyRefund(Order $order, ApplyRefundRequest $request)
    {
        // 校验是否属于当前用户
        $this->authorize('own', $order);

        // 判断订单是否已经付款
        if (!$order->paid_at)
            throw new InvalidRequestException('该订单未支付，不可退款');

        // 判断订单退款是否正确
        if ($order->refund_status !== Order::REFUND_STATUS_PENDING)
            throw new InvalidRequestException('该订单已经申请过退款，请勿重复申请');

        // 将用户输入的退款理由放入到订单 extra 字段中
        $extra = $order->extra ?: [];
        $extra['refund_reason'] = $request->input('reason');

        // 将订单退款状态改为已申请退款
        $order->update([
            'refund_status' => Order::REFUND_STATUS_APPLIED,
            'extra'         => $extra
        ]);

        return $extra;
    }


}
