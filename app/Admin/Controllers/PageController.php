<?php

namespace App\Admin\Controllers;

use App\Models\Page;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class PageController extends Controller
{
    use ModelForm;

    protected $name = '单页管理';

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header($this->name);
            $content->description('列表');

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
        return Admin::grid(Page::class, function (Grid $grid) {
            $grid->model()->orderBy('id', 'DESC');

            $grid->column('id', 'ID')->sortable();
            $grid->column('alias', '别名')->display(function ($value) {
                return trans('base.page.' . $value) == 'base.page.' . $value ? $value : trans('base.page.' . $value);
            });
            $grid->column('content', '内容')->display(function ($item) {
                return str_limit(strip_tags($item), 100);
            });

            $grid->column('created_at', '创建时间');
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
        return Admin::form(Page::class, function (Form $form) {

            $form->text('id', 'ID')->attribute('disabled', true);
            $form->text('alias', '别名');
            $form->editor('content', '内容');

            $form->datetime('created_at', '创建时间')->attribute('disabled', true);
            $form->datetime('updated_at', '更新时间')->attribute('disabled', true);
        });
    }
}
