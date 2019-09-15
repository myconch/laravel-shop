<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaction', function (Blueprint $table) {
            //交易编号
            $table->bigIncrements('id');
            //用户编号
            $table->integer('user_id');
            //商品编号
            $table->integer('merchandise_id');
            //当时购买的价格
            $table->float('price');
            //购买的数量
            $table->integer('buy_count');
            //交易总价格
            $table->float('total_price');
            //时间戳
            $table->timestamps();

            //索引设置
            $table->index(['user_id'],'users_transaction_idx');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transaction', function (Blueprint $table) {
            //
        });
    }
}
