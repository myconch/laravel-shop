<?php

namespace App\Http\Controllers;

use App\Exceptions\CouponCodeUnavailableException;
use Illuminate\Http\Request;
use App\Http\Requests\OrderRequest;
use App\Models\ProductSku;
use App\Models\UserAddress;
use App\Models\Order;
use Carbon\Carbon;
use App\Exceptions\InvalidRequestException;
use App\Jobs\CloseOrder;
use App\Services\CartService;
use App\Services\OrderService;
use App\Http\Requests\SendReviewRequest;
use App\Events\OrderReviewed;
use App\Http\Requests\ApplyRefundRequest;
use App\Models\CouponCode;

class OrdersController extends Controller
{
    //利用laravel自动解析的功能注入CartService类
    public function store(OrderRequest $request,OrderService $orderService)
    {
        $user = $request->user();
        $address = UserAddress::find($request->input('address_id'));
        $coupon = null;

        //如果用户提交了优惠码
        if ($code = $request->input('coupon_code')) {
            $coupon = CouponCode::where('code',$code)->first();
            if (!$coupon) {
                throw new CouponCodeUnavailableException('该优惠券不存在');
            }
        }

        return $orderService->store($user,$address,$request->input('remark'),$request->input('items'),$coupon);
    }

    public function index(Request $request)
    {
        $orders = Order::query()
            ->with(['items.product','items.productSku'])
            ->where('user_id',$request->user()->id)
            ->orderBy('created_at','desc')
            ->paginate();

         //dd($orders);

        return view('orders.index',['orders'=>$orders]);
    }

    public function show(Order $order)
    {
        //$s=$order->items[0]->id;

        //dd($s);
        //授权验证，验证提交的用户是否与当前登录的账户一致
        $this->authorize('own',$order);

        return view('orders.show',['order'=>$order->load(['items.productSku','items.product'])]);

    }

    public function received(Order $order,Request $request)
    {
        //校验权限
        $this->authorize('own',$order);

        //判断订单的发货状态是否未已发货
        if ($order->ship_status !== Order::SHIP_STATUS_DELIVERED) {
            throw new InvalidRequestException('发货状态不正确');
        }

        //更新发货状态为已收到
        $order->update(['ship_status'=>Order::SHIP_STATUS_RECEIVED]);

        //返回原页面
        return $order;
    }

    public function review(Order $order)
    {
        //校验权限
        $this->authorize('own',$order);
        //判断是否已经支付
        if (!$order->paid_at) {
            throw new InvalidRequestException('该订单未支付，不可评价');
        }
        //使用load方法加载关联数据，避免N+1性能问题
        return view('orders.review',['order'=>$order->load(['items.productSku','items.product'])]);
    }

    public function sendReview(Order $order,SendReviewRequest $request)
    {
        //校验权限
        $this->authorize('own',$order);
        if (!$order->paid_at) {
            throw new InvalidRequestException('该订单未支付，不可评价');
        }
        //判断是否已经评价
        if ($order->reviewed) {
            throw new InvalidRequestException('该订单已经评价，不可重复提交');
        }
        $reviews = $request->input('reviews');
        //开启事务
        \DB::transaction(function() use ($reviews,$order) {
            //遍历用户提交的数据
            foreach($reviews as $review) {
                $orderItem = $order->items()->find($review['id']);
                //保存评分和评价
                $orderItem->update([
                    'rating' => $review['rating'],
                    'review' => $review['review'],
                    'reviewed_at' => Carbon::now(),
                ]);
            }
            //将订单标记为已评价
            $order->update(['reviewed'=> true]);
            event(new OrderReviewed($order));
        });

        return redirect()->back();
    }

    public function applyRefund(Order $order,ApplyRefundRequest $request)
    {
        //校验订单是否属于当前用户
        $this->authorize('own',$order);
        //判断订单是否已付款
        if (!$order->paid_at) {
            throw new InvalidRequestException('该订单未支付，不可退款');
        }
        //判断订单退款状态是否正确
        if($order->refund_status !== Order::REFUND_STATUS_PENDING) {
            throw new InvalidRequestException('该订单已申请退款，请勿重复申请');
        }
        //将用户输入的退款理由放到订单的extra字段中
        $extra = $order->extra ? : [];
        $extra['refund_reason'] = $request->input('reason');
        //将订单状态改为已申请退款
        $order->update([
            'refund_status' => Order::REFUND_STATUS_APPLIED,
            'extra' => $extra,
        ]);

        return $order;
    }

}
