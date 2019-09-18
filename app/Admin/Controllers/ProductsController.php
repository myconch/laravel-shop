<?php

namespace App\Admin\Controllers;

use App\Models\Product;
use App\Models\ProductSsr;
use App\Models\Category;
use App\Http\Controllers\Controller;
use App\Models\ProductAttribute;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Admin;
use App\Admin\Extensions\Attribute;

class ProductsController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('商品列表')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('Detail')
            ->description('description')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('编辑商品')
            ->body($this->form($id)->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('创建商品')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Product);
        $grid->model()->with(['category']);

        $grid->id('Id')->sortable();
        $grid->title('商品名称');
        $grid->column('category.name','类目');
        $grid->on_sale('已上架')->display(function ($value){
            return $value ? '是' : '否';
        });
        $grid->price('价格');
        $grid->rating('评分');
        $grid->sold_count('销量');
        $grid->review_count('评论数');


        $grid->actions(function($actions){
            //不在每一行后显示查看按钮
            $actions->disableView();
            //不在每一行后显示删除按钮
            $actions->disableDelete();

            // 自定义 规格 按钮
            $actions->append(new Attribute($actions->getkey()));
        });
        $grid->tools(function ($tools){
            //禁用批量删除按钮
            $tools->batch(function ($batch){
                $batch->disableDelete();
            });
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Product::findOrFail($id));

        $show->id('Id');
        $show->title('Title');
        $show->description('Description');
        $show->image('Image');
        $show->on_sale('On sale');
        $show->rating('Rating');
        $show->sold_count('Sold count');
        $show->review_count('Review count');
        $show->price('Price');
        $show->created_at('Created at');
        $show->updated_at('Updated at');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form1()
    {
        $form = new Form(new Product);

        /*
        $title = Product::query()->where('on_sale',true)->pluck('title');
        //dd($title);
        $form->select('','用户选择测试')->options($title);
        */

        //创建一个输入框，第一个参数title是模型的字段名，第二个参数是该字段的描述
        $form->text('title', '商品名称')->rules('required');
        $form->select('category_id','类目')->options(function (){
            $categories = Category::query()
                ->where('is_directory',0)
                ->orderBy('path')
                ->get();
            $array = array();
            foreach($categories as $category) {
                $array[$category->id] = $category->full_name;
            }
            return $array;
        });
        //创建一个选择图片的框
        $form->image('image', '封面图片')->rules('required|image');
        //创建一个富文本编辑器
        $form->text('description', '商品描述')->rules('required');
        //创建一组单选框
        $form->radio('on_sale', '上架')
            ->options(['1'=>'是','0'=>'否'])
            ->default('0');

        // 创建商品规格
        $form->hasMany('attributes','规格',function (Form\NestedForm $form){
            $form->text('name','名称')->rules('required');
            $form->text('values','值')->rules('required');
        });

        // 创建商品sku
        $form->hasMany('skus','SKU 列表',function (Form\NestedForm $form){
            $form->text('title','SKU 名称')->rules('required');
            $form->text('description','SKU 描述')->rules('required');
            $form->text('price','单价')->rules('required|numeric|min:0.01');
            $form->text('stock','剩余库存')->rules('required|integer|min:0');
        });

        //定义事件回调，当模型即将保存时会触发个回调
        $form->saving(function (Form $form){
            $form->model()->price =collect($form->input('skus'))->where(Form::REMOVE_FLAG_NAME,0)->min('price') ? : 0;
            //collect()是laravel提供的一个辅助函数，可以快速创建一个collection对象
            //在这里将SKU数据放到collection对象中，利用collection提供的min()方法求出SKU中的最小price
            //？：0 则是保证当SKU数据为空时，price字段被赋值0
            //where(Form::REMOVE_FLAG_NAME, 0)，当我们在前端移除一个 SKU 的之后，点击保存按钮时 Laravel-Admin 仍然会将被删除的 SKU 提交上去，但是会添加一个 _remove_=1 的字段
            //正常的 SKU 的 _remove_ 字段是 0，因此我们在查找最低价格时只需要查询 _remove_=0 的 SKU 即可
        });

        return $form;
    }

    protected function form()
    {
        Admin::html('
            <div id="myModal" class="modal-attri">
              <!-- 弹窗内容 -->
              <div class="modal-attri-content">
                <span class="close">&times;</span>
                <div id="popupForm" style="display:flex;"></div>
                <button type="button" id="skuAttribute" class="btn btn-success" style="margin-left:80%;margin-top:30px;width: 100px;">确定</button>
              </div>
              
            </div>
        ');

        Admin::style('
            .input-group .form-control {
                z-index: 0; /* 覆盖原有值，以免影弹窗显示 */
            }
            .modal-attri {
                display: none; /* 默认隐藏 */
                position: fixed; /* 固定定位 */
                z-index: 1; /* 设置在顶层 */
                left: 0;
                top: 0;
                width: 100%; 
                height: 100%;
                overflow: auto; 
                background-color: rgb(0,0,0); 
                background-color: rgba(0,0,0,0.4); 
            }
             
            /* 弹窗内容 */
            .modal-attri-content {
                background-color: #fefefe;
                margin: 15% auto; 
                padding: 20px;
                border: 1px solid #888;
                width: 80%; 
                z-index: 1;
            }
             
            /* 关闭按钮 */
            .close {
                color: #aaa;
                float: right;
                font-size: 28px;
                font-weight: bold;
            }
             
            .close:hover,
            .close:focus {
                color: black;
                text-decoration: none;
                cursor: pointer;
            }
            
            .select {
                display: inline-block;
                min-width: 150px;
                height: 35px;
                margin-left: 10px;
                position: relative;
                vertical-align: middle;
                padding: 0;
                overflow: hidden;
                background-color: #fff;
                color: #555;
                border: 1px solid #aaa;
                text-shadow: none;
                border-radius: 4px;	
                transition: box-shadow 0.25s ease;
                z-index: 2;
            }
 
            .select:hover {
                box-shadow: 0 1px 4px rgba(0, 0, 0, 0.15);
            }
         
            .select:before {
                content: "";
                position: absolute;
                width: 0;
                height: 0;
                border: 10px solid transparent;
                border-top-color: #ccc;
                top: 14px;
                right: 10px;
                cursor: pointer;
                z-index: -2;
            }
            .select select {
                cursor: pointer;
                padding: 10px;
                width: 100%;
                border: none;
                background: transparent;
                background-image: none;
                -webkit-appearance: none;
                -moz-appearance: none;
            }
         
            .select select:focus {
                outline: none;
            }
        ');

        Admin::script('
            var y = changeAttr();
            document.getElementById("popupForm").innerHTML = y;

            // 获取弹窗
            var modal = document.getElementById(\'myModal\');
             
            // 获取 span 元素，用于关闭弹窗
            var span = document.querySelector(\'.close\');
            
            // 建立一个全局变量，用来保存触发弹窗的元素$(this)
            var attriValue = "";
            
            // 点击 span (x), 关闭弹窗
            span.onclick = function() {
                modal.style.display = "none";
            }
             
            // 在用户点击其他地方时，关闭弹窗
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }

            // 修改过产品规格后，更新弹窗里的内容
            $(\'#vv\').click(function() {
                console.log("开心");
                /*
                var h = document.createElement("div");
                h.setAttribute("id","popupForm");
                h.setAttribute("class","modal-attri");
                document.body.appendChild(h);
                */
                var x = changeAttr();
                document.getElementById("popupForm").innerHTML = x;
                //document.getElementsByClassName("wrapper").innerHTML = h;
                //document.getElementsByClassName("wrapper").innerHTML = h;
            });
        
            // 生成选项列表
            function changeAttr () {
                var name = document.getElementsByClassName("form-control attributes name");
                var values = document.getElementsByClassName("form-control attributes values");
                var html = "";
                for (var i=0; i< name.length; i++) {
                    html += "<div style=\"flex:1;height:35px;margin-top:40px;\"><span>" + name[i].value + "</span>"
                    var valuesArray = values[i].value.split(",");
                    var select = \'<select name="attribute" class="select">\';
                    for (var j=0; j< valuesArray.length; j++) {
                        select += \'<option value=\' + valuesArray[j] + \'>\' + valuesArray[j] + \'</option>\'
                    }
                    select = select + \'</select></div>\';
                    html += select;
                }
                return html;
            };
            
            // 点击sku属性输入栏时，会弹出弹窗
            //var element = document.getElementsClassName("form-control skus title");
            $(\'.has-many-skus\').on(\'focus\',\'#title.skus\',function () {
                console.log("试试");
                modal.style.display = "block";
                attriValue = $(this)
                console.log(attriValue)
            });
            
            // 获取select选择的内容，合并后赋值给sku
            $(\'#skuAttribute\').click(function () {
                console.log("开始")
                let select = document.getElementsByName("attribute");
                let attriVal = "";
                for (var i=0; i< select.length; i++) {
                    console.log(select[i].value)
                    if (i+1 == select.length) {
                        attriVal += select[i].value
                    } else {
                        attriVal += select[i].value + ","
                    }
                }
                attriValue.val(attriVal)
                modal.style.display = "none";
            });
        ');

        $form = new Form(new Product);

        $form->tab('第一步 商品内容',function ($form){
            //创建一个输入框，第一个参数title是模型的字段名，第二个参数是该字段的描述
            $form->text('title', '商品名称')->rules('required');
            $form->select('category_id','类目')->options(function (){
                $categories = Category::query()
                    ->where('is_directory',0)
                    ->orderBy('path')
                    ->get();
                $array = array();
                foreach($categories as $category) {
                    $array[$category->id] = $category->full_name;
                }
                return $array;
            });
            //创建一个选择图片的框
            $form->text('image', '封面图片')->rules('required')
                    ->help('为了方便测试，此处只能传入网络图片');
            //创建一个富文本编辑器
            // 在写wangEditor模板（.blade.php)时，div的id不能是{{$id}},否则编辑器将无法显示，具体原因不详
            $form->wangEditor('description', '商品描述')->rules('required');
            //创建一组单选框
            $form->radio('on_sale', '上架')
                ->options(['1'=>'是','0'=>'否'])
                ->default('0');

        })->tab('第二步 商品规格',function ($form){
            // 创建商品规格
            $form->hasMany('attributes','规格',function (Form\NestedForm $form){
                $form->text('name','名称')->rules('required');
                $form->tagsinput('values','值')->rules('required');
            });
            $form->html("
                <button id=\"vv\" class=\"btn btn warning\" type=\"button\" style=\"width: 100px;float: right;background-color: gold;\">保存编辑</button>
                <span class=\"help-block\" style=\"color:red;\">
                    <i class=\"fa fa-info-circle\"></i>&nbsp;进入下一步前单击右方的“保存编辑”按钮，同步数据到sku属性选择菜单
                </span>
                ");
        })->tab('第三步 单品信息',function ($form){
            // 创建商品sku
            $form->hasMany('skus','SKU 列表',function (Form\NestedForm $form){
                $form->text('title','SKU 属性')->rules('required');
                $form->text('description','SKU 描述')->rules('required');
                $form->text('price','单价')->rules('required|numeric|min:0.01');
                $form->text('stock','剩余库存')->rules('required|integer|min:0');
            });
        });

        //定义事件回调，当模型即将保存时会触发个回调
        $form->saving(function (Form $form){
            $form->model()->price =collect($form->input('skus'))->where(Form::REMOVE_FLAG_NAME,0)->min('price') ? : 0;
            //collect()是laravel提供的一个辅助函数，可以快速创建一个collection对象
            //在这里将SKU数据放到collection对象中，利用collection提供的min()方法求出SKU中的最小price
            //？：0 则是保证当SKU数据为空时，price字段被赋值0
            //where(Form::REMOVE_FLAG_NAME, 0)，当我们在前端移除一个 SKU 的之后，点击保存按钮时 Laravel-Admin 仍然会将被删除的 SKU 提交上去，但是会添加一个 _remove_=1 的字段
            //正常的 SKU 的 _remove_ 字段是 0，因此我们在查找最低价格时只需要查询 _remove_=0 的 SKU 即可
        });

        return $form;

    }
}
