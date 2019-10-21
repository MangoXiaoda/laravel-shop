<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->bigIncrements('id')
                ->comment('自增长ID');
            $table->unsignedBigInteger('user_id')
                ->comment('该地址所属的用户');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->string('province')
                ->comment('省');
            $table->string('city')
                ->comment('市');
            $table->string('district')
                ->comment('区');
            $table->string('address')
                ->comment('具体地址');
            $table->unsignedInteger('zip')
                ->comment('邮编');
            $table->string('contact_name')
                ->comment('联系人姓名');
            $table->string('contact_phone')
                ->comment('联系人电话');
            $table->dateTime('last_used_at')
                ->nullable()
                ->comment('最后一次使用时间');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_addresses');
    }
}
