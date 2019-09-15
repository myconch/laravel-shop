<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\CouponCodeUnavailableException;
use App\Models\CouponCode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\OrderService;
use App\Http\Requests\OrderRequest;
use App\Models\UserAddress;

class OrdersController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        return $this->orderService = $orderService;
    }

    public function store (OrderRequest $request)
    {
        $user = $request->user();
        $address = $request->user()->addresses()->orderBy('last_used_at','desc')->first();
        $coupon = null;

        //如果用户提交了优惠码
        if ($code = $request->input('coupon_code')) {
            $coupon = CouponCode::where('code',$code)->first();
            if (!$coupon) {
                throw new CouponCodeUnavailableException('该优惠券不存在');
            }
        }

        return $this->orderService->store($user,$address,$request->remark,$request->items,$coupon);
    }
}
