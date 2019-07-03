<?php

namespace App\Admin\Controllers;

use App\Jobs\SyncOneProductToES;
use App\Models\Category;
use App\Models\Product;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;


class ProductsController extends CommonProductsController
{
    use HasResourceActions;

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Product);
        $grid->model()->where('type', $this->getProductType())->with(['category']);

        $grid->id('ID')->sortable();
        $grid->title('商品名称');
        $grid->column('category.name', '类目名称');
        $grid->on_sale('已上架')->display(function ($value) {
            return $value ? '是' : '否';
        });
        $grid->price('价格');
        $grid->rating('评分');
        $grid->sold_count('销量');
        $grid->review_count('评论数');

        $grid->actions(function ($actions) {
            $actions->disableView();
            $actions->disableDelete();
        });
        $grid->tools(function ($tools) {
            // 禁用批量删除按钮
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });

        return $grid;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Product);

        $form->hidden('type')->value($this->getProductType());
        // 创建一个输入框，第一个参数 title 是模型的字段名，第二个参数是该字段描述
        $form->text('title', '商品名称')->rules('required');
        // 放在商品名称后面
        $form->text('long_title', '商品长标题')->rules('required');
        $form->select('category_id', '类目名称')->options(function ($category_id){
            $category = Category::find($category_id);
            if($category){
                return [$category->id => $category->name];
            }

        })->ajax(route('category.api').'?is_directory=0');


        // 创建一个选择图片的框
        $form->image('image', '封面图片')->rules('required|image');

        // 创建一个富文本编辑器
        $form->editor('description', '商品描述')->rules('required');

        // 创建一组单选框
        $form->radio('on_sale', '上架')->options(['1' => '是', '0'=> '否'])->default('0');

        // 直接添加一对多的关联模型
        $form->hasMany('skus', 'SKU 列表', function (Form\NestedForm $form) {
            $form->text('title', 'SKU 名称')->rules('required');
            $form->text('description', 'SKU 描述')->rules('required');
            $form->text('price', '单价')->rules('required|numeric|min:0.01');
            $form->text('stock', '剩余库存')->rules('required|integer|min:0');
        });

        $form->hasMany('properties', '属性列表', function (Form\NestedForm $form){
            $form->text('name', '属性名')->rules('required');
            $form->text('value', '属性值')->rules('required');
        });

        // 定义事件回调，当模型即将保存时会触发这个回调
        $form->saving(function (Form $form) {
            $form->model()->price = collect($form->input('skus'))->where(Form::REMOVE_FLAG_NAME, 0)->min('price') ?: 0;
        });

        //将商品同步到 Elasticsearch
        $form->saved(function (Form $form){
            $product = $form->model();
            $this->dispatch(new SyncOneProductToES($product));
        });
        return $form;
    }

    public function getProductType()
    {
        return Product::TYPE_NORMAL;
    }
}
