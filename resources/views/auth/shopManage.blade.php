

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>商城后台管理</title>
    <meta name="renderer" content="webkit">
    <!--告诉浏览器该页面适合使用什么内核进行渲染
        1.webkit代表用webkit内核；
        2.ie-comp代表用IE兼容内核；
        3.ie-stand代表用IE标准内核；
    -->

    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="/static/layuiadmin/layui/css/layui.css" media="all">
    <link rel="stylesheet" href="/static/layuiadmin/style/admin.css" media="all">
    <script>
        /^http(s*):\/\//.test(location.href) || alert('请先部署到 localhost 下再访问');
    </script>
</head>
<body bgcolor="#D6D6D6" class="layui-layout-body">

<div id="LAY_app">
    <!--LAY_app容器ID-->
    <div class="layui-layout layui-layout-admin">
        <div class="layui-header">
            <!-- 头部区域 -->
            <ul class="layui-nav layui-layout-left">
                <li class="layui-nav-item layadmin-flexible" lay-unselect>
                    <!--lay-unselect无选中效果，实现效果上还有bug，先不要在意这个-->
                    <a href="javascript:;" layadmin-event="flexible" title="侧边伸缩" >
                        <!--layadmin-event定义了一个点击事件，触发函数“flexible”-->
                        <i class="layui-icon layui-icon-shrink-right" id="LAY_app_flexible" ></i>
                    </a>
                </li>
                <li class="layui-nav-item layui-hide-xs" lay-unselect>
                    <a href="http://www.layui.com/admin/" target="_blank" title="前台">
                        <!--target属性规定在何处打开链接
                            _blank：在新窗口中打开被链接文档
                            _self：默认。在相同的框架中打开被链接文档
                            _parent：在父框架中打开被链接的文档
                            framename：在指定的框架中打开被链接的文档
                        -->
                        <i class="layui-icon layui-icon-website"></i>
                    </a>
                </li>
                <li class="layui-nav-item" lay-unselect>
                    <a href="javascript:;" layadmin-event="refresh" title="刷新">
                        <i class="layui-icon layui-icon-refresh-3"></i>
                    </a>
                </li>
                <li class="layui-nav-item layui-hide-xs" lay-unselect>
                    <input type="text" placeholder="搜索..." autocomplete="off" class="layui-input layui-input-search" layadmin-event="serach" lay-action="template/search.html?keywords=">
                    <!--autocomplete规定是否启用表单自动完成功能，默认“on”，即输入过一次后下次输入相同内容会有提示-->
                </li>
            </ul>

            <ul class="layui-nav layui-layout-right" lay-filter="layadmin-layout-right">

                <li class="layui-nav-item" lay-unselect>
                    <a lay-href="app/message/index.html" layadmin-event="message" lay-text="消息中心">
                        <!--点击后layadmin-event="message" 查询是否有小红点，有就删去-->
                        <i class="layui-icon layui-icon-notice"></i>

                        <!-- 如果有新消息，则显示小圆点 -->
                        <span class="layui-badge-dot"></span>
                    </a>
                </li>
                <li class="layui-nav-item layui-hide-xs" lay-unselect>
                    <a href="javascript:;" layadmin-event="theme">
                        <i class="layui-icon layui-icon-theme"></i>
                    </a>
                </li>
                <li class="layui-nav-item layui-hide-xs" lay-unselect>
                    <a href="javascript:;" layadmin-event="note">
                        <i class="layui-icon layui-icon-note"></i>
                    </a>
                </li>
                <li class="layui-nav-item layui-hide-xs" lay-unselect>
                    <a href="javascript:;" layadmin-event="fullscreen">
                        <i class="layui-icon layui-icon-screen-full"></i>
                    </a>
                </li>
                <li class="layui-nav-item" lay-unselect>
                    <a href="javascript:;">
                        <cite>{{session('nickName')}}</cite>
                    </a>
                    <dl class="layui-nav-child">
                        <dd><a lay-href="set/user/info.html">基本资料</a></dd>
                        <dd><a lay-href="set/user/password.html">修改密码</a></dd>
                        <hr>
                        <dd  style="text-align: center;"><a href="/user/auth/sign-out" >退出</a></dd>
                    </dl>
                </li>

                <li class="layui-nav-item layui-hide-xs" lay-unselect>
                    <a href="javascript:;" layadmin-event="about"><i class="layui-icon layui-icon-more-vertical"></i></a>
                </li>
                <li class="layui-nav-item layui-show-xs-inline-block layui-hide-sm" lay-unselect>
                    <a href="javascript:;" layadmin-event="more"><i class="layui-icon layui-icon-more-vertical"></i></a>
                </li>
            </ul>
        </div>

        <!-- 侧边菜单 -->
        <div class="layui-side layui-side-menu">
            <div class="layui-side-scroll">
                <div class="layui-logo" lay-href="home/console.html">
                    <span>Conch</span>
                </div>

                <ul class="layui-nav layui-nav-tree" lay-shrink="all" id="LAY-system-side-menu" lay-filter="layadmin-system-side-menu">
                    <li data-name="home" class="layui-nav-item layui-nav-itemed">
                        <a href="javascript:;" lay-tips="主页" lay-direction="2">
                            <!--标签页中默认打开-->
                            <i class="layui-icon layui-icon-home"></i>
                            <cite>主页</cite>
                        </a>
                    </li>

                    <!--商品管理-->
                    <li data-name="component" class="layui-nav-item">
                        <a href="javascript:;" lay-tips="商品管理" lay-direction="2">
                            <i class="layui-icon layui-icon-cart"></i>
                            <cite>商品管理</cite>
                        </a>
                        <dl class="layui-nav-child">
                            <dd data-name="goodsList"><a lay-href="{{route('merchandise.manage')}}">商品列表</a></dd>
                            <dd data-name="dataGroup"><a lay-href="template/datagroup.html">商品组</a></dd>
                            <!--第一次将别处的页面带过来后，打开该标签页css样式怎么页加载不上，后来将所有的css样式都注释掉，打开，再取消注释，就显示正常了-->
                        </dl>
                    </li>

                    <!--订单管理-->
                    <li data-name="orderList" class="layui-nav-item">
                        <a href="javascript:;" lay-tips="订单管理" lay-direction="2">
                            <i class="layui-icon layui-icon-form"></i>
                            <cite>订单管理</cite>
                        </a>
                        <dl class="layui-nav-child">
                            <dd data-name="order"><a lay-href="">订单信息</a></dd>
                            <dd data-name="invoice"><a lay-href="">发货信息</a></dd>
                            <dd data-name="pickup"><a lay-href="">提货信息</a></dd>
                            <dd data-name="salesReturn"><a lay-href="">退货信息</a></dd>
                            <dd data-name="backstage"><a lay-href="">后台单信息</a></dd>
                    </li>

                    <!--统计报表-->
                    <li data-name="statistics" class="layui-nav-item">
                        <a href="javascript:;" lay-tips="统计报表" lay-direction="2">
                            <i class="layui-icon layui-icon-table"></i>
                            <cite>统计报表</cite>
                        </a>
                        <dl class="layui-nav-child">
                            <dd data-name="salesVolume"><a lay-href="">商品销量</a></dd>
                            <dd data-name="receipt"><a lay-href="">财务收款</a></dd>
                            <dd data-name="browseCount"><a lay-href="">客户浏览信息</a></dd>
                        </dl>
                    </li>

                    <!--财务管理-->
                    <li data-name="money" class="layui-nav-item">
                        <a href="javascript:;" lay-tips="财务管理" lay-direction="2">
                            <i class="layui-icon layui-icon-rmb"></i>
                            <cite>财务管理</cite>
                        </a>
                        <dl class="layui-nav-child">
                            <dd data-name="receipt"><a lay-href="">收款单列表</a></dd>
                            <dd data-name="reimburse"><a lay-href="">退款单列表</a></dd>
                            <dd data-name="payment"><a lay-href="">支付单列表</a></dd>
                            <dd data-name="report"><a lay-href="">财务报告</a></dd>
                        </dl>
                    </li>

                    <!--促销及广告-->
                    <li dataname="promotion" class="layui-nav-item">
                        <a href="javascript:;" lay-tips="促销及广告" lay-direction="2">
                            <i class="layui-icon layui-icon-fire"></i>
                            <cite>促销及广告</cite>
                        </a>
                        <dl class="layui-nav-child">
                            <dd data-name="promotionList"><a lay-href="">促销列表</a></dd>
                            <dd data-name="coupon"><a lay-href="">优惠券组</a></dd>
                            <dd data-name="article"><a lay-href="">文章列表</a></dd>
                            <dd data-name="carousel"><a lay-href="">轮播图</a></dd>
                            <dd data-name="video"><a lay-href="">视频</a></dd>
                        </dl>
                    </li>

                    <!--会员管理-->
                    <li data-name="customer" class="layui-nav-item">
                        <a href="javascript:;" lay-tips="会员管理" lay-direction="2">
                            <i class="layui-icon layui-icon-user"></i>
                            <cite>会员管理</cite>
                        </a>
                    </li>

                    <!--前端布局-->
                    <li data-name="myPage" class="layui-nav-item">
                        <a href="javascript:;" lay-tips="前端布局" lay-direction="2">
                            <i class="layui-icon layui-icon-layouts"></i>
                            <cite>前端布局</cite>
                        </a>
                    </li>

                </ul>
            </div>
        </div>

        <!-- 页面标签 -->
        <div class="layadmin-pagetabs" id="LAY_app_tabs">
            <div class="layui-icon layadmin-tabs-control layui-icon-prev" layadmin-event="leftPage"></div>
            <div class="layui-icon layadmin-tabs-control layui-icon-next" layadmin-event="rightPage"></div>
            <div class="layui-icon layadmin-tabs-control layui-icon-down">
                <ul class="layui-nav layadmin-tabs-select" lay-filter="layadmin-pagetabs-nav">
                    <li class="layui-nav-item" lay-unselect>
                        <a href="javascript:;"></a>
                        <dl class="layui-nav-child layui-anim-fadein">
                            <dd layadmin-event="closeThisTabs"><a href="javascript:;">关闭当前标签页</a></dd>
                            <dd layadmin-event="closeOtherTabs"><a href="javascript:;">关闭其它标签页</a></dd>
                            <dd layadmin-event="closeAllTabs"><a href="javascript:;">关闭全部标签页</a></dd>
                        </dl>
                    </li>
                </ul>
            </div>
            <div class="layui-tab" lay-unauto lay-allowClose="true" lay-filter="layadmin-layout-tabs">
                <ul class="layui-tab-title" id="LAY_app_tabsheader">
                    <li lay-id="home/console.html" lay-attr="home/console.html" class="layui-this"><i class="layui-icon layui-icon-home"></i></li>
                </ul>
            </div>
        </div>


        <!-- 主体内容 -->
        <div class="layui-body" id="LAY_app_body">
            <div class="layadmin-tabsbody-item layui-show">
                <iframe src="{{route('admin.console')}}" frameborder="0" class="layadmin-iframe"></iframe>
                <!--route()函数根据路由的别名生成url-->
            </div>
        </div>

        <!-- 辅助元素，一般用于移动设备下遮罩 -->
        <div class="layadmin-body-shade" layadmin-event="shade"></div>
    </div>
</div>

<script src="/static/layuiadmin/layui/layui.js"></script>
<script>
    layui.config({
        base: '/static/layuiadmin/' //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
    }).use('index');
</script>

<!-- 百度统计 -->
<script>
    var _hmt = _hmt || [];
    (function() {
        var hm = document.createElement("script");
        hm.src = "https://hm.baidu.com/hm.js?d214947968792b839fd669a4decaaffc";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();
</script>
</body>
</html>



