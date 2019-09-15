<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    //up（）函数和down（）函数是相对的。up建立数据表，down撤销up函数做的修改
    public function up()
    {
        //建立数据表
        Schema::create('users', function (Blueprint $table) {
            //用户编号（主键索引值）
            $table->bigIncrements('id');
            //昵称
            $table->string('nickname');
            //Email
            $table->string('email')->unique();  //unique()设置额外主键
            //用户类型（type）：用于识别用户的身份
            //-A(Admin)：管理员
            //-G(general)：一般用户
            $table->string('type',1)->default('G');  //默认为G
            //时间戳，邮箱验证时间
            $table->timestamp('email_verified_at')->nullable();  //nullable()允许为空值
            //密码
            $table->string('password',60);  //laravel中密码都会限制60个字符
            //
            $table->rememberToken();
            //时间戳，建立create_at及updated_at字段
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
        //移除数据表
        Schema::dropIfExists('users');
    }
}
