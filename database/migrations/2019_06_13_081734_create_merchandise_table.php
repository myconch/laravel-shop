<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMerchandiseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('merchandise', function (Blueprint $table) {
            //商品编号（主键索引值）
            $table->bigIncrements('id');
            //商品名称
            $table->string('name')->nullable();
            //商品规格
            $table->string('type')->nullable();
            //标记商品装态，上架的商品才能被看到
            //-C(Create)：建立中
            //-S(Sell)：可销售
            $table->string('status',1)->default('C');  //默认为C
            //商品介绍
            $table->text('introduction')->nullable();
            //封面图片
            $table->string('photo',50)->nullable();
            //商品图片
            $table->string('merchandise_photo')->nullable();
            //详情图片
            $table->string('detail_photo')->nullable();
            //商品价格
            $table->float('price')->default(0);
            //商品剩余数量
            $table->integer('remain_count')->default(0);
            //时间戳，建立create_at及updated_at字段
            $table->timestamps();

            //索引设置
            $table->index(['status'],'merchandise_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('merchandise', function (Blueprint $table) {
            //
        });
    }
}
