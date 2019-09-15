
@extends('layouts.app')

@section('title',$product->title)

@section('content')
    <div class="row">
        <div class="col-lg-10 offset-lg-1">
            <div class="card">
                <div class="card-body product-info">
                    <div class="row">
                        <div class="col-5">
                            <img class="cover" src="{{$product->image_url}}" alt="">
                        </div>
                        <div class="col-7">
                            <div class="title">{{$product->title}}</div>
                            <div class="price">
                                <label>价格</label><em>￥</em><span>{{$product->price}}</span>
                            </div>
                            <div class="sales_and_reviews">
                                <div class="sold_count">
                                    累计销量<span class="count">{{$product->sold_count}}</span>
                                </div>
                                <div class="review_count">
                                    累计评价<span class="count">{{$product->review_count}}</span>
                                </div>
                                <div class="rating" title="评分 {{$product->rating}}">
                                    评分<span class="count">{{str_repeat('★',floor($product->rating))}}{{str_repeat('☆',5-floor($product->rating))}}</span>
                                </div>
                            </div>
                            <div class="skus">
                                <label>选择</label>
                                <!-- 这里使用了 Bootstrap 的按钮组来输出 SKU 列表 -->
                                <div class="btn-group btn-group-toggle" data-toggle="buttons">

                                    @foreach($product->skus as $sku)
                                        <label class="btn sku-btn" data-price="{{$sku->price}}" data-stock="{{$sku->stock}}" data-togge="tooltip" title="{{$sku->description}}" data-placement="bottom">
                                            {{-- data-togge="tooltip" 工具提示；data-placement="bottom" 底部显示提示；需要配合js中的 $('[data-toggle="tooltip"]').tooltip() 使用 --}}
                                            <input type="radio" name="skus" autocomplete="off" value="{{$sku->id}}">{{$sku->title}}
                                            {{-- <input>标签在不同的type中value的作用不同 --}}
                                            {{-- type="checkbox", "radio", "image" - 定义与输入相关联的值 --}}
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <div class="cart_amount">
                                <label>数量</label>
                                <input type="text" class="form-control form-control-sm" value="1">
                                <span>件</span>
                                <span class="stock"></span>
                            </div>
                            <div class="buttons">
                                @if($favored)
                                    <button class="btn btn-danger btn-disfavor">取消收藏</button>
                                @else
                                    <button class="btn btn-success btn-favor">❤ 收藏</button>
                                @endif
                                <button class="btn btn-primary btn-add-to-cart">加入购物车</button>
                            </div>
                        </div>
                    </div>
                    <div class="product-detail">

                        <!-- 使用 Bootstrap 的 Tab 插件，我们用来输出商品详情和评价列表 -->
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                {{-- <a ... href="#..." ...> 指向页面中的锚 --}}
                                <a class="nav-link active" href="#product-detail-tab" aria-controls="product-detail-tab" role="tab" data-toggle="tab" aria-selected="true">
                                    商品详情
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#product-reviews-tab" aria-controls="product-reviews-tab" role="tab" data-toggle="tab" aria-selected="false">
                                    用户评价
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="product-detail-tab">
                                {!! $product->description !!}
                                {{-- {!! $product->description !!} 因为我们后台编辑商品详情用的是富文本编辑器，
                                     提交的内容是 Html 代码，此处需要原样输出而不需要进行 Html 转义 --}}
                            </div>
                            <div role="tabpanel" class="tab-pane" id="product-reviews-tab">
                                <!-- 评论列表开始 -->
                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <td>用户</td>
                                        <td>商品</td>
                                        <td>评分</td>
                                        <td>评价</td>
                                        <td>时间</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($reviews as $review)
                                        <tr>
                                            <td>{{$review->order->user->name}}</td>
                                            <td>{{$review->productSku->title}}</td>
                                            <td>{{str_repeat('★',$review->rating)}}{{str_repeat('☆',5-$review->rating)}}</td>
                                            <td>{{$review->review}}</td>
                                            <td>{{$review->reviewed_at->format('Y-m-d H:i')}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                <!-- 评论列表结束 -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('scriptsAfterJs')
    <script>
        $(document).ready(function () {
            $('[data-toggle="tooltip"]').tooltip({trigger:'hover'});

            {{-- 在输出 SKU 的按钮组的时候，我们通过 data-* 属性把对应 SKU 的价格和剩余库存放在了 Html 标签里 --}}
            $('.sku-btn').click(function () {
                $('.product-info .price span').text($(this).data('price'));
                $('.product-info .stock').text('库存：' + $(this).data('stock') + '件');
            });

            //监听搜藏按钮的点击事件
            $('.btn-favor').click(function(){
                //发起一个post ajax请求，请求url通过后端的route()函数生成
                axios.post('{{route('products.favor',['product'=>$product->id])}}').then(function () {
                    //请求成功会执行这个回调
                    swal('操作成功','','success')
                        .then(function () {
                        location.reload();
                    });
                },function (error) {
                    //请求失败会执行这个回调
                    //如果返回码是401代表没有登录
                    if (error.response && error.response.status === 401) {
                        swal('请先登录','','error');
                    } else if (error.response && error.response.data.msg) {
                        //其他有msg字段的情况，将msg提示给用户
                        swal(error.response.data.msg,'',error);
                    } else {
                        //其他情况应该是系统挂了
                        swal('系统错误','','error');
                    }
                });
            });

            $('.btn-disfavor').click(function(){
                axios.delete('{{route('products.disfavor',['product'=>$product->id])}}')
                    .then(function(){
                        swal('操作成功','','success')
                            .then(function(){
                                location.reload();
                            });
                    });
            });

            //加入购物车按钮点击事件
            $('.btn-add-to-cart').click(function(){
                //请求加入购物车接口
                axios.post('{{route('cart.add')}}',{
                    sku_id: $('label.active input[name=skus]').val(),
                    amount: $('.cart_amount input').val(),
                }).then(function () {
                        //请求成功执行此回调
                        swal('加入购物车成功','','success');
                    },function (error) {  //error为$fail返回的json数据
                        console.log(JSON.stringify(error));
                        //请求失败执行此回到
                        if (error.response.status === 401) {
                            //http状态码为401代表用户未登录
                            swal('请先登录','','error');
                        } else if (error.response.status === 403) {
                            //http状态码为403代表用户未验证邮箱
                            swal('用户邮箱未验证','','error');
                        }
                        else if (error.response.status === 422) {
                            console.log(error.response.data.errors);
                            //http状态码为422代表用户输入校验失败
                            var html = '<div>';
                            // - 表示使用的是lodash 工具库，该库在3.4新建收货地址那一章引入
                            _.each(error.response.data.errors,function (error){
                                console.log(error);
                                _.each(error,function (error){
                                    html +=error+'<br>';
                                })
                            });
                            html += '</div>';
                            swal({content:$(html)[0],icon:'error'})
                        } else {
                            //其他情况应该是系统挂了
                            swal('系统错误','','error');
                        }
                    });
            });
        });
    </script>
@endsection