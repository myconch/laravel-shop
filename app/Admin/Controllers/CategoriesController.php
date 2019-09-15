<?php

namespace App\Admin\Controllers;

use App\Models\Category;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class CategoriesController extends Controller
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
            ->header('商品类目列表')
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
            ->header('编辑商品类目')
            ->body($this->form(true)->edit($id));
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
            ->header('新增商品类目')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Category);

        $grid->id('ID');
        $grid->name('名称');
        $grid->level('层级');
        $grid->is_directory('是否目录')->display(function ($value){
            return $value ? '是' : '否';
        });
        $grid->path('类目路径');

        $grid->actions(function ($actions){
            $actions->disableView();
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
        $show = new Show(Category::findOrFail($id));

        $show->id('Id')->sortable();
        $show->name('Name');
        $show->parent_id('Parent id');
        $show->is_directory('Is directory');
        $show->level('Level');
        $show->path('Path');
        $show->created_at('Created at');
        $show->updated_at('Updated at');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    //$isEditing是再edit中传入的参数
    protected function form($isEditing=false)
    {
        $form = new Form(new Category);

        $form->text('name', '名称')->rules('required');

        //如果是编辑的情况
        if($isEditing) {
            //不允许用户修改 是否目录 和父类目 字段的值
            //display（）方法用来展示值,with方法接受一个匿名函数，会把字段值传给匿名函数并把返回值展示出来
            $form->display('is_directory','是否目录')
                ->with(function ($value) {
                    return $value ? '是' : '否';
                });
            $form->display('parent.name','父类目');
        } else {
            $form->radio('is_directory', '是否有子类目')
                ->options([1=>'是',0=>'否'])
                ->default('true');

            $form->select('parent_id','所属类目')->options(function (){
                $categories = Category::query()
                    ->orderBy('path')
                    ->get();
                $array = array();
                foreach ($categories as $category) {
                    $array[$category->id] = $category->full_name;
                }
                return $array;
            })->rules('required');
        }

        return $form;
    }
}
