<?php

namespace App\Http\Controllers;

use App\Models\CouponCode;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CouponCodesController extends Controller
{


    public function show($code)
    {
        // 判断优惠券是否存在
        if (!$record = CouponCode::where('code', $code)->first())
            abort(404);

        // 如果优惠券没有启用 等同于优惠券不存在
        if (!$record->enabled)
            abort(404);

        // 优惠券已被兑换完
        if ($record->total - $record->used <= 0)
            return response()->json(['msg' => '该优惠券已被兑换完'],403);

        // 优惠券未到使用时间
        if ($record->not_before && $record->not_before->gt(Carbon::now()))
            return response()->json(['msg' => '该优惠劵现在还不能被使用'],403);

        // 优惠券已过期
        if ($record->not_after && $record->not_after->lt(Carbon::now()))
            return response()->json(['msg' => '该优惠券已过期']);

        return $record;
    }

}
