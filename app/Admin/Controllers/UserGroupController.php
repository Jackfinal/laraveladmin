<?php

namespace App\Admin\Controllers;

use App\Models\UserGroup;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class UserGroupController extends Controller
{
    use ModelForm;

    protected $name = '用户组管理';

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
        return Admin::grid(UserGroup::class, function (Grid $grid) {
            $grid->model()->orderBy('minscore', 'DESC');

            $grid->column('id', 'ID')->sortable();
            $grid->column('name', '用户组名');
            $grid->column('minscore', '最小积分');
            $grid->column('maxscore', '最大积分');
            $grid->column('created_at', '创建时间');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(UserGroup::class, function (Form $form) {

            $form->text('id', 'ID')->attribute('disabled', true);
            $form->text('name', '用户组名');
            $form->number('minscore', '最小积分');
            $form->number('maxscore', '最大积分');
            $form->text('content','备注');
            $form->datetime('created_at', '创建时间')->attribute('disabled', true);
            $form->datetime('updated_at', '更新时间')->attribute('disabled', true);

            
        });
    }
}
