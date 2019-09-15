
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{$title}}</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="/static/layuiadmin/layui/css/layui.css" media="all">
    <link rel="stylesheet" href="/static/layuiadmin/style/admin.css" media="all">
</head>
<body>

<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-header">编辑商品</div>
        <!--编辑区域-->
        <div class="layui-card-body" style="padding: 15px;">
            <form class="layui-form" action="" lay-filter="component-form-group">
                <!--商品名称-->
                <div class="layui-form-item">
                    <label class="layui-form-label">商品名称</label>
                    <div class="layui-input-block">
                        <input type="text" name="name" lay-verify="title" autocomplete="off" placeholder="请输入商品名称" class="layui-input">
                    </div>
                </div>
                <!--商品介绍-->
                <div class="layui-form-item layui-form-text">
                    <label class="layui-form-label">商品介绍</label>
                    <div class="layui-input-block">
                        <textarea name="introduction" placeholder="请输入内容" class="layui-textarea"></textarea>
                    </div>
                </div>
                <!--规格表单-->
                <div class="layui-card-body">
                    <div class="layui-btn-group test-table-operate-btn" style="margin-bottom: 10px;">
                        <button class="layui-btn" type="button" data-type="getCheckData">获取选中行数据</button>
                        <button class="layui-btn" type="button" data-type="getCheckLength">获取选中数目</button>
                        <button class="layui-btn" type="button" data-type="isAll">验证是否全选</button>
                    </div>

                    <table class="layui-hide" id="test-table-operate" lay-filter="test-table-operate"></table>
                </div>
                <!--上传产品图片-->
                <div class="layui-col-md12">
                    <div class="layui-card">
                        <div class="layui-card-header">上传产品图片（第一张图将会作为封面）</div>
                        <div class="layui-card-body">
                            <div class="layui-upload">
                                <button type="button" class="layui-btn layui-btn-normal" id="upload-merchandise-photo">选择多文件</button>
                                <button type="button" class="layui-btn" id="test-upload-testListAction">开始上传</button>
                                <div class="layui-upload-list">
                                    <table class="layui-table">
                                        <thead>
                                        <tr><th>文件名</th>
                                            <th>大小</th>
                                            <th>状态</th>
                                            <th>操作</th>
                                        </tr></thead>
                                        <tbody id="test-upload-demoList"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--上传产品详情图片-->


                <div class="layui-form-item layui-layout-admin">
                    <div class="layui-input-block">
                        <div class="layui-footer" style="left: 0;">
                            <button class="layui-btn" lay-submit="" lay-filter="component-form-demo1">立即提交</button>
                            <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<script src="/static/layuiadmin/layui/layui.js"></script>
<script>
    layui.config({
        base: '/static/layuiadmin/' //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
    }).use(['index', 'form', 'laydate','table','upload'], function(){
        var $ = layui.$
            ,admin = layui.admin
            ,element = layui.element
            ,layer = layui.layer
            ,laydate = layui.laydate
            ,form = layui.form
            ,table = layui.table
            ,upload = layui.upload;

        form.render(null, 'component-form-group');

        //table表格头
        var test = [
            {"id":23,"type":"xu","price":"boy","remain_count":"beijing",}
        ];

        table.render({
            elem: '#test-table-operate'
            //,url: layui.setter.base + 'json/table/user.js'
            ,width: admin.screen() > 1 ? 892 : ''
            ,height: 332
            ,cols: [[
                {type:'checkbox', fixed: 'left'}
                ,{field:'id', width:80, title: 'ID', sort: true, fixed: 'left'}  //sort:是否运行排序
                ,{field:'type', edit:'text', width:80, title: '产品规格'}
                ,{field:'price', edit:'text', width:80, title: '价格', sort: true}
                ,{field:'remain_count', edit:'text', width:80, title: '数量'}
            ]]
            ,page: true
            ,data:test
        });

        //监听表格复选框选择
        table.on('checkbox(test-table-operate)', function(obj){
            console.log(obj)
        });

        table.on('getCheckData')
        var $ = layui.$, active = {
            getCheckData: function(){ //获取选中数据
                var test1 =
                    {"id":25,"type":"ppp","price":"boy","remain_count":"beijing",}
                ;
                test.push(test1);
                table.reload('test-table-operate',{data:test});
            }
            ,getCheckLength: function(){ //获取选中数目
                var checkStatus = table.checkStatus('test-table-operate')
                    ,data = checkStatus.data;
                layer.msg('选中了：'+ data.length + ' 个');
            }
            ,isAll: function(){ //验证是否全选
                layer.open({
                    type: 2,
                    area: ['700px', '450px'],
                    fixed: false,
                    maxmin: true,
                    content: 'http://shop.test/merchandise/create'
                });
            }
        };

        $('.test-table-operate-btn .layui-btn').on('click', function(){
            var type = $(this).data('type');
            active[type] ? active[type].call(this) : '';
        });
        //table表格尾

        //图片上传
        var demoListView = $('#test-upload-demoList')
            ,uploadListIns = upload.render({
            elem: '#upload-merchandise-photo'
            ,url: ''
            ,accept: 'file'
            ,multiple: true
            ,auto: false
            ,bindAction: '#test-upload-testListAction'
            ,choose: function(obj){
                var files = this.files = obj.pushFile(); //将每次选择的文件追加到文件队列
                //读取本地文件
                obj.preview(function(index, file, result){
                    var tr = $(['<tr id="upload-'+ index +'">'
                        ,'<td>'+ file.name +'</td>'
                        ,'<td>'+ (file.size/1014).toFixed(1) +'kb</td>'
                        ,'<td>等待上传</td>'
                        ,'<td>'
                        ,'<button type="button" class="layui-btn layui-btn-mini test-upload-demo-reload layui-hide">重传</button>'
                        ,'<button type="button" class="layui-btn layui-btn-mini layui-btn-danger test-upload-demo-delete">删除</button>'
                        ,'</td>'
                        ,'</tr>'].join(''));

                    //单个重传
                    tr.find('.test-upload-demo-reload').on('click', function(){
                        obj.upload(index, file);
                    });

                    //删除
                    tr.find('.test-upload-demo-delete').on('click', function(){
                        delete files[index]; //删除对应的文件
                        tr.remove();
                        uploadListIns.config.elem.next()[0].value = ''; //清空 input file 值，以免删除后出现同名文件不可选
                    });

                    demoListView.append(tr);
                });
            }
            ,done: function(res, index, upload){
                if(res.code == 0){ //上传成功
                    var tr = demoListView.find('tr#upload-'+ index)
                        ,tds = tr.children();
                    tds.eq(2).html('<span style="color: #5FB878;">上传成功</span>');
                    tds.eq(3).html(''); //清空操作
                    return delete this.files[index]; //删除文件队列已经上传成功的文件
                }
                this.error(index, upload);
            }
            ,error: function(index, upload){
                var tr = demoListView.find('tr#upload-'+ index)
                    ,tds = tr.children();
                tds.eq(2).html('<span style="color: #FF5722;">上传失败</span>');
                tds.eq(3).find('.test-upload-demo-reload').removeClass('layui-hide'); //显示重传
            }
        });

        laydate.render({
            elem: '#LAY-component-form-group-date'
        });







        /* 自定义验证规则 */
        form.verify({
            title: function(value){
                if(value.length < 5){
                    return '标题至少得5个字符啊';
                }
            }
            ,pass: [/(.+){6,12}$/, '密码必须6到12位']
            ,content: function(value){
                layedit.sync(editIndex);
            }
        });

        /* 监听指定开关 */
        form.on('switch(component-form-switchTest)', function(data){
            layer.msg('开关checked：'+ (this.checked ? 'true' : 'false'), {
                offset: '6px'
            });
            layer.tips('温馨提示：请注意开关状态的文字可以随意定义，而不仅仅是ON|OFF', data.othis)
        });

        /* 监听提交 */
        form.on('submit(component-form-demo1)', function(data){
            parent.layer.alert(JSON.stringify(data.field), {
                title: '最终的提交信息'
            })
            return false;
        });
    });
</script>
</body>
</html>
