<?php

namespace App\Http\Controllers\Api;

use App\Events\OrderReviewed;
use App\Exceptions\CouponCodeUnavailableException;
use App\Exceptions\InvalidRequestException;
use App\Http\Requests\ApplyRefundRequest;
use App\Http\Requests\SendReviewRequest;
use App\Models\CouponCode;
use App\Models\Order;
use Carbon\Carbon;
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
        //dd($user->userAndMasters[0]->percent);
        $address = UserAddress::find($request->address_id);
        $coupon = null;

        //如果用户提交了优惠码
        if ($code = $request->coupon_code) {
            $coupon = CouponCode::where('code',$code)->first();
            if (!$coupon) {
                throw new CouponCodeUnavailableException('该优惠券不存在');
            }
        }

        return $this->orderService->store($user,$address,$request->remark,$request->items,$coupon);
    }

    // 查看订单
    public function index(Request $request)
    {
        $orders = '';
        switch ($request->type) {
            // 全部订单
            case "all" :
                $orders = Order::query()
                    ->with(['items.product','items.productSku'])
                    ->where('user_id',$request->user()->id)
                    ->orderBy('created_at','desc')
                    ->paginate();
                break;
            // 待付款
            case "pay":
                $orders = Order::query()
                    ->with(['items.product','items.productSku'])
                    ->where('user_id',$request->user()->id)
                    ->where('paid_at',null)
                    ->orderBy('created_at','desc')
                    ->paginate();
                break;
            // 待发货
            case "pending":
                $orders = Order::query()
                    ->with(['items.product','items.productSku'])
                    ->where('user_id',$request->user()->id)
                    ->where('ship_status','pending')
                    ->whereNotNull('paid_at')
                    ->orderBy('created_at','desc')
                    ->paginate();
                break;
            // 待收货
            case "delivered":
                $orders = Order::query()
                    ->with(['items.product','items.productSku'])
                    ->where('user_id',$request->user()->id)
                    ->where('ship_status','delivered')
                    ->orderBy('created_at','desc')
                    ->paginate();
                break;
            // 待评价
            case "received":
                $orders = Order::query()
                    ->with(['items.product','items.productSku'])
                    ->where('user_id',$request->user()->id)
                    ->where('ship_status','received')
                    ->orderBy('created_at','desc')
                    ->paginate();
                break;
            // 退款/售后
            case "refund":
                $orders = Order::query()
                    ->with(['items.product','items.productSku'])
                    ->where('user_id',$request->user()->id)
                    ->where('refund_status','!=','pending')
                    ->orderBy('created_at','desc')
                    ->paginate();
                break;
        };

        //dd($orders);

        return $orders;
    }

    //评论
    public function review ($order_id)
    {
        $order = Order::where('id',$order_id)->first();
        // 校验权限
        $this->authorize('own',$order);
        // 判断订单是否已支付
        if (!$order->paid_at) {
            throw new InvalidRequestException('订单未支付，不可评价！');
        }

        return $order->load(['items.productSku','items.product']);
    }

    // 提交评论
    public function sendReview ($order_id,SendReviewRequest $request)
    {
        $order = Order::where('id',$order_id)->first();
        // 校验权限
        $this->authorize('own',$order);
        if (!$order->paid_at) {
            throw new InvalidRequestException('该订单未支付，不可评价');
        }
        //判断是否已经评价
        if ($order->reviewed) {
            throw new InvalidRequestException('该订单已经评价，不可重复提交');
        }
        $reviews = $request->reviews;

        //开启事务
        \DB::transaction(function() use ($reviews,$order) {
            //遍历用户提交的数据
            foreach($reviews as $review) {
                $orderItem = $order->items()->find($review['id']);
                // 此处判断原本在SendReviewRequest中，但因为api中无法获取路由订单信息，因此将验证移到这里
                if (!$orderItem) {
                    throw new InvalidRequestException('输入orderItem的id错误！');
                }
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

        return $order;
    }

    // 退款申请
    public function applyRefund ($order_id,ApplyRefundRequest $request)
    {
        $order = Order::where('id',$order_id)->first();
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
        $extra['refund_reason'] = $request->reason;
        //将订单状态改为已申请退款
        $order->update([
            'refund_status' => Order::REFUND_STATUS_APPLIED,
            'extra' => $extra,
        ]);

        return $order;
    }

    // 删除订单
    public function destroy($order_id)
    {
        $order = Order::where('id',$order_id)->first();
        $this->authorize('own',$order);
        $order->delete();

        //返回还没写好
        return;
    }
}
