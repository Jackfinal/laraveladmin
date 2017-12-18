<?php

namespace App\Admin\Controllers;

use App\User;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use App\Models\UserGroup;
use Illuminate\Support\Facades\Auth;
class UserController extends Controller
{
    use ModelForm;

    protected $name = '用户管理';

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
        return Admin::grid(User::class, function (Grid $grid) {
            $grid->model()->orderBy('id', 'DESC');

            $grid->column('id', 'ID')->sortable();
            $grid->column('name', '用户名');
            $grid->column('nickname', '昵称');
            $grid->column('email', '邮箱');
            $grid->column('score', '积分')->editable('textarea');
            $grid->column('user_group', '用户组')->display(function($score){
                $tmp = UserGroup::belongsToGroup($this->score);
                if($tmp)
                {
                    return $tmp->name;
                }else {
                    return '无用户组';
                }
                
            });
            $grid->column('user_mobile', '手机号');
            $states = [
                'on'  => ['value'=>1,'text' => '男', 'color' => 'primary'],
                'off' => ['value'=>2,'text' => '女', 'color' => 'default'],
            ];
            $grid->column('sex', '性别')->switch($states);
            
            $grid->column('last_login_ip', '最后登录IP');
            $grid->column('last_login_time', '最后登录时间');
            $states = [
                'on'  => ['value'=>1,'text' => '启用', 'color' => 'primary'],
                'off' => ['value'=>0,'text' => '拉黑', 'color' => 'default'],
            ];
            $grid->column('user_status', '状态')->switch($states);
            
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
        return Admin::form(User::class, function (Form $form) {

            $form->text('id', 'ID')->attribute('disabled', true);
            $form->text('user_login','用户名')->rules('required|min:3');
            $form->text('user_nicename', '昵称')->rules('required|min:2');
            $form->email('user_email', '邮箱')->rules('required|min:3');
            $form->text('user_mobile', '手机号')->rules('required|min:3');
            $form->number('score', '积分');
            $form->select('sex')->options([0 => '未知', 1 => '男', 2 => '女']);
            $form->radio('user_status','用户状态')->options([ 1 => '启用', 0 => '拉黑'])->default('1');
            
            $form->password('user_pass', '密码');

            $form->datetime('created_at', '创建时间')->attribute('disabled', true);
            $form->datetime('updated_at', '更新时间')->attribute('disabled', true);

            $form->saving(function (Form $form) {
                if ($form->user_pass && $form->model()->user_pass != $form->user_pass) {
                    $form->user_pass = bcrypt($form->user_pass);
                }
            });
        });
    }
}
