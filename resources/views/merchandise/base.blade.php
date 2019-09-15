
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>商品列表</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="/static/layuiadmin/layui/css/layui.css" media="all">
    <link rel="stylesheet" href="/static/layuiadmin/style/admin.css" media="all">
    <link rel="stylesheet" href="/static/layuiadmin/style/template.css" media="all">
</head>
<body>

<div class="layui-fluid layadmin-cmdlist-fluid">
    <div class="layui-row layui-col-space30">
        <div class="layui-col-md2 layui-col-sm4">
            <div class="cmdlist-container" >
                <!--弹窗显示编辑页-->
                <a onclick="edit()">
                    <div style="padding-top: 71px"></div>
                    <!--此处的div标签是因为实在没有找到将图标的位置向下移动而采取的无奈之举-->
                    <i class="layui-icon layui-icon-add-circle-fine" style="font-size: 100px;padding-left: 21px"></i>
                    <div style="padding-top: 71px"></div>
                </a>
            </div>
        </div>
        <!--商品展示-->
        <div class="layui-col-md2 layui-col-sm4">
            <div class="cmdlist-container">
                <a href="javascript:;">
                    <img src="/static/layuiadmin/style/res/template/portrait.png">
                </a>
                <a href="javascript:;">
                    <div class="cmdlist-text">
                        <p class="info">2018春夏季新款港味短款白色T恤+网纱中长款chic半身裙套装两件套</p>
                        <div class="price">
                            <b>￥79</b>
                            <p>
                                ¥
                                <del>130</del>
                            </p>
                            <span class="flow">
                      <i class="layui-icon layui-icon-rate"></i>
                      433
                    </span>
                        </div>
                    </div>
                </a>
            </div>

        </div>
        <div class="layui-col-md2 layui-col-sm4">
            <div class="cmdlist-container">
                <a href="javascript:;">
                    <img src="/static/layuiadmin/style/res/template/portrait.png">
                </a>
                <a href="javascript:;">
                    <div class="cmdlist-text">
                        <p class="info">2018春夏季新款港味短款白色T恤+网纱中长款chic半身裙套装两件套</p>
                        <div class="price">
                            <b>￥79</b>
                            <p>
                                ¥
                                <del>130</del>
                            </p>
                            <span class="flow">
                      <i class="layui-icon layui-icon-rate"></i>
                      433
                    </span>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="layui-col-md12 layui-col-sm12">
            <div id="demo0"></div>
        </div>
    </div>
</div>


<script src="/static/layuiadmin/layui/layui.js"></script>
<script>
    layui.config({
        base: '/static/layuiadmin/' //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
    }).use(['index']);
    layui.use(['laypage', 'layer'], function(){
        var laypage = layui.laypage
            ,layer = layui.layer;

        //总页数低于页码总数
        laypage.render({
            elem: 'demo0'
            ,count: 50 //数据总数
        });
    });
</script>
<script>
    function edit() {
        layer.open({
            title:'编辑商品',
            type: 2,
            area: ['700px', '450px'],
            fixed: false,
            maxmin: true,
            content: 'http://shop.test/merchandise/create'
        });
    }
</script>
</body>
</html>