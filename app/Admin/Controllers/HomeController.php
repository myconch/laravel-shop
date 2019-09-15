<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;

class HomeController extends Controller
{
    public function index(Content $content)
    {
        admin_toastr('这是个浮动提示','success');
        return $content
            ->withInfo('测试','我卡卡')
            ->header('Dashboard')
            ->description('Description...')
            ->row(Dashboard::title())
            //row是body()方法的别名，可以接受任何可字符串化的对象作为参数
            ->row(function (Row $row) {

                //laravel采用的bootstrap的栅格系统，每行长度是12，下面三个每个长度是4
                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::environment());
                });

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::extensions());
                });

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::dependencies());
                });
            });
    }
}
