
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>{{$title}} - ConchMarket</title>
  <meta name="renderer" content="webkit">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
  <link rel="stylesheet" href="/static/layuiadmin/layui/css/layui.css" media="all">
  <link rel="stylesheet" href="/static/layuiadmin/style/admin.css" media="all">
  <link rel="stylesheet" href="/static/layuiadmin/style/login.css" media="all">
</head>
<body>
<div class="layadmin-user-login layadmin-user-display-show" id="LAY-user-login" style="display: none;">
    <div class="layadmin-user-login-main">
        <div class="layadmin-user-login-box layadmin-user-login-header">
            <h2>ConchMarket</h2>
        </div>
        <!--注册表单-->
        <form action="/user/auth/sign-up" method="post" class="layadmin-user-login-box layadmin-user-login-body layui-form">

            <!-- ★自动产生 csrf_token隐藏字段，否则post方法无法提交-->
            {{ csrf_field() }}

            <!--邮箱-->
            <div class="layui-form-item">
                <label class="layadmin-user-login-icon layui-icon layui-icon-face-smile" for="LAY-user-login-email"></label>
                <!--for属性，规定label标签与哪个表单绑定，此处表示的与下面id="LAY-user-login-cellphone"绑定-->
                <input type="text" name="email" id="LAY-user-login-email" lay-verify="email" placeholder="邮箱" class="layui-input">
                <!--lay-verify将会对输入的数据进行验证，验证不通过无法提交表单-->
            </div>
            <!--密码-->
            <div class="layui-form-item">
                <label class="layadmin-user-login-icon layui-icon layui-icon-password" for="LAY-user-login-password"></label>
                <input type="password" name="password" id="LAY-user-login-password" lay-verify="pass" placeholder="密码" class="layui-input">
                <!--lay-verify="pass" pass验证在user.js中定义-->
            </div>
            <!--再次输入密码-->
            <div class="layui-form-item">
                <label class="layadmin-user-login-icon layui-icon layui-icon-password" for="LAY-user-login-repass"></label>
                <input type="password" name="repass" id="LAY-user-login-repass" lay-verify="required" placeholder="确认密码" class="layui-input">
            </div>
            <!--昵称-->
            <div class="layui-form-item">
                <label class="layadmin-user-login-icon layui-icon layui-icon-username" for="LAY-user-login-nickname"></label>
                <input type="text" name="nickname" id="LAY-user-login-nickname" lay-verify="nickname" placeholder="昵称" class="layui-input">
            </div>
            <!--用户协议-->
            <div class="layui-form-item">
                <input type="checkbox" name="agreement" lay-skin="primary" title="同意用户协议" checked>
            </div>
            <!--注册按钮-->
            <div class="layui-form-item">
                <button class="layui-btn layui-btn-fluid" lay-submit lay-filter="LAY-user-reg-submit">注 册</button>
            </div>
            <!--已有账号登陆-->
            <div class="layui-trans layui-form-item layadmin-user-login-other">
                <a href="login.html" class="layadmin-user-jump-change layadmin-link layui-hide-xs">用已有帐号登入</a>
                <a href="login.html" class="layadmin-user-jump-change layadmin-link layui-hide-sm layui-show-xs-inline-block">登入</a>
            </div>
        </form>
    </div>
    <!--版权信息-->
    <div class="layui-trans layadmin-user-login-footer">
        <p>© 2019 <a href="http://www.conchmarket.com/" target="_blank">conchmarket.com</a></p>
    </div>

</div>

<script src="/static/layuiadmin/layui/layui.js"></script>
<script>
    layui.config({
        base: '/static/layuiadmin/' //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
        //index.js会加载admin.js和view.js,并重新将目录指向module文件夹，
        // 因此下面的use（）会加载module文件夹下的user.js
    }).use(['index', 'user'], function(){
        var $ = layui.$
            ,setter = layui.setter
            ,admin = layui.admin
            ,form = layui.form
            ,router = layui.router();

        form.render();


    });
</script>

</body>
</html>