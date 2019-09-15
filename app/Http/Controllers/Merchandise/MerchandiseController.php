<?php

namespace App\Http\Controllers\Merchandise;

use App\Http\Controllers\Controller;
use App\Shop\Entity\Merchandise;
use Symfony\Component\HttpKernel\Tests\Controller\ControllerTestService;
use validator;

Class MerchandiseController extends Controller {

    //新增商品
    public function merchandiseCreateProcess(){
        //建立商品基本信息
        $merchandise_data = [
            'statue' =>'C',
            'name' =>'',
            'type' =>'',
            'introduction' =>'',
            'merchandise_photo' =>'',
            'photo' =>null,
            'detail_photo' =>'',
            'price' =>0,
            'remain_count' =>0,
        ];
        $Merchandise = Merchandise::create($merchandise_data);

        //重新定向到商品编辑页
        return redirect('/merchandise/'.$Merchandise->id.'/edit');
    }

    //编辑商品
    public function merchandiseItemEditPage($merchandise_id){
        //获取商品数据
        $Merchandise = Merchandise::findOrFail($merchandise_id);

        if (!is_null($Merchandise->photo)){
            $Merchandise->photo = url($Merchandise->photo);
        }

        $binding = [
            'title'=>'编辑商品',
            'Merchandise'=>$Merchandise,
        ];
        return view('merchandise.editMerchandise',$binding);
    }

    //商品管理
    public function merchandiseManageListPage(){
        return view('merchandise.manageMerchandise');
    }
}
