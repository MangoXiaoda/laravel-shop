<?php

namespace App\Exceptions;

use Illuminate\Http\Request;
use Exception;
use Throwable;

class CouponCodeUnavailableException extends Exception
{
    /**
     * CouponCodeUnavailableException constructor.
     * @param string $message
     * @param int $code
     */
    public function __construct($message = "", int $code = 403)
    {
        parent::__construct($message, $code);
    }

    // 当这个异常被触发时，会调用 render 方法来输出给用户
    public function render(Request $request)
    {
        // 如果用户通过 API 请求，则返回json格式的错误信息
        if ($request->expectsJson())
            return response()->json(['msg' => $this->message], $this->code);

        return redirect()->back()->withErrors(['coupon_code' => $this->message]);
    }

}
