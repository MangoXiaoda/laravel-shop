<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\CouponCode;
use Faker\Generator as Faker;

$factory->define(CouponCode::class, function (Faker $faker) {

    // 首先随机获得一个类型
    $type = $faker->randomElement(array_keys(CouponCode::$typeMap));

    // 根据取得的类型生成对应的折扣
    $value = $type === CouponCode::TYPE_FIXED ? random_int(1, 200) : random_int(1, 50);

    // 如果是固定金额，则最低订单金额必须优惠金额高出 0.01元

    if ($type === CouponCode::TYPE_FIXED) {
        $minAmount = $value + 0.01;
    } else {
        // 如果是百分比折扣，有50%概率不需要订单最低金额
        if (random_int(1, 50) < 50){
            $minAmount = 0;
        } else {
            $minAmount = random_int(100, 1000);
        }

    }

    return [
        'name' => join(' ', $faker->words), // 随机生成名称
        'code' => CouponCode::findAvailableCode(),
        'type' => $type,
        'value'=> $value,
        'total'=> 1000,
        'used' => 0,
        'min_amount' => $minAmount,
        'not_before' => null,
        'not_after' => null,
        'enabled' => true,
    ];
});
