<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SendReviewRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'reviews' => ['required','array'],
            'reviews.*.id' => [
                'required',
                //$this->route('order') 可以获得当前路由对应的订单对象
                //Rule::exists() 判断用户提交的ID是否属于此订单
                /*  因api中无法获取当前路由对应的订单对象，因此将此处验证移到OrdersController中
                Rule::exists('order_items','id')
                    ->where('order_id',$this->route('order')->id)
                */
            ],
            'reviews.*.rating' => ['required','integer','between:1,5'],
            'reviews.*.review' => ['required'],
        ];
    }

    public function attributes()
    {
        return [
            'reviews.*.rating' => '评分',
            'reviews.*.review' => '评价',
        ];
    }
}
