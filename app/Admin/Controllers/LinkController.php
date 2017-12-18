<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Models\Link;
use Encore\Admin\Form;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Grid;
use App\Models\Category;

class LinkController extends Controller
{
    protected $name = '友情链接管理';
    use ModelForm;
    
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header($this->name);
            $content->description($this->name);


            $content->body($this->grid());
        });
    }
    
    /**
     * Edit interface.
     *
     * @param $id
     *
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header($this->name);
            $content->description('编辑');

            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header($this->name);
            $content->description('创建');

            $content->body($this->form());
        });
    }
    
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Link::class, function (Grid $grid) {
            $grid->model()->orderBy('id', 'DESC');
            
            $grid->filter(function($filter){
                
                $filter->like('name', '标题');
                //$filter->addFilter($filter->like('post_title', '标题'));
                
            
            });
            $grid->column('id', 'ID')->sortable();
            $grid->column('listorder', '排序')->editable();
            
            $grid->column('name', '名称');
            $grid->column('url', '链接');
            
            $grid->column('updated_at', '更新时间');
        });
    }
    
    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        
        return Admin::form(Link::class, function (Form $form) {
            
            $form->number('id', 'ID')->attribute('disabled', false);
            $form->text('name', '名称')->rules('required|min:3');
            $form->url('url', '链接')->rules('nullable')->help('例如：http://www.anhuinews.com/');
            $form->image('thumb','图片链接')->uniqueName();
            $form->number('listorder', '排序')->help('数字越大排前');
            
        });
    }
}
