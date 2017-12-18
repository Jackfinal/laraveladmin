<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Models\Article;
use Encore\Admin\Form;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Grid;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
class ArticleController extends Controller
{
    protected $name = '分类管理';
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
        return Admin::grid(Article::class, function (Grid $grid) {
            $grid->model()->orderBy('id', 'DESC');
            
            $grid->filter(function($filter){
            
                // 去掉默认的id过滤器
                //$filter->disableIdFilter();
                
                $filter->useModal();
            
                // 在这里添加字段过滤器
                
                $filter->equal('post_type','栏目')->select(function(){
                    $temp = Category::get();
                    $ret = array();
                    $ret[0] = '根目录';
                    foreach ( $temp as $row )
                    {
                        $ret[$row['id']] = $row['name'];
                    }
                    return $ret;
                });
                $filter->like('post_title', '标题');
                //$filter->addFilter($filter->like('post_title', '标题'));
                
            
            });
    
            $grid->column('id', 'ID')->sortable();
            $grid->column('post_title', '标题');
            $grid->column('post_type', '栏目')->display(function ($index) {
                return $this->Category->name;
            });
            $grid->column('post_hits', '点击量');
            $grid->column('post_author', '发布人')->display(function ($index) {
                $users = \DB::table('admin_users')
                    ->where('id', $index)
                    ->select('id', 'name')
                    ->first();
                return $users->name;
            });
            
            $grid->column('recommended','推荐')->options()->select(['0' => '不推荐','1' => '首页Banner', '2'=> '首页要闻上2条', '3'=> '首页要闻下4条']);
            
            $states = [
                'on'  => ['value'=>1,'text' => '已审核', 'color' => 'primary'],
                'off' => ['value'=>0,'text' => '未审核', 'color' => 'default'],
            ];
            $grid->column('post_status', '状态')->switch($states);
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
        
        return Admin::form(Article::class, function (Form $form) {
            
            $form->number('id', 'ID')->attribute('disabled', false);
            $form->text('post_title', '标题')->rules('required|min:3');
            $form->text('post_keywords', '关键词')->rules('nullable')->rules('required|min:2');
            $form->text('post_excerpt', '摘要')->rules('nullable');
            $form->text('post_source', '来源')->rules('nullable');
            $form->url('jumpurl', '外部链接')->rules('nullable')->help('例如：http://ah.anhuinews.com/system/2017/11/08/007744812.shtml');
            $form->image('post_thumb','标题图片')->uniqueName();
            $form->text('post_hits', '点击数')->default(rand(50,100));
            $form->radio('istop','置顶')->options(['1' => '是', '0'=> '否'])->default('1');
            $form->select('recommended','推荐')->options(['0' => '不推荐','1' => '首页Banner', '2'=> '首页要闻上2条', '3'=> '首页要闻下4条']);
            $form->number('listorder', '排序')->help('数字越大排前');
            $form->radio('post_status','审核状态')->options(['1' => '是', '0'=> '否'])->default('0');
            $form->select('post_type', '栏目')->rules(function ($form) {
                    return 'required|numeric|min:0';
            })->options(function() {
                $temp = Category::select(array('id','name'))->get();
                $ret = array();
                //$ret[0] = '根目录';
                foreach ( $temp as $row )
                {
                    $ret[$row['id']] = $row['name'];
                }
                return $ret;
            })->default('3');
            
            $form->editor('post_content', '内容');
            $form->hidden('post_author');
            $form->saving(function (Form $form) {
                $form->post_author = Auth::guard('admin')->user()->id;
            });
        });
    }
}
