<?php

use Illuminate\Database\Seeder;

class CouponCodesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 生成一批假优惠券数据
        factory(\App\Models\CouponCode::class, 20)->create();
    }
}
