<?php

//命名空间
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use PhpParser\Builder\Class_;

Class IndexController extends Controller {

    //主页
    public function console(){
        return view('admin.home.console');
    }
}