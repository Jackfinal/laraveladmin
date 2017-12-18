<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Models\Category;
use Encore\Admin\Form;
use Encore\Admin\Controllers\ModelForm;

class CategoryController extends Controller
{
    protected $name = '分类管理';
    use ModelForm;
    
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header($this->name);
            $content->description($this->name);


            $content->body(Category::tree());
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
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        
        return Admin::form(Category::class, function (Form $form) {
            
            $form->number('id', 'ID')->attribute('disabled', false);
            $form->text('name', '分类名称')->rules('required|min:3');
            $form->text('description', '分类描述');
            $form->number('listorder', '排序')->help('数字越大排前');
            $form->select('parent_id', '父栏目')->options(function() {
                $temp = Category::select(array('id','name'))->get();
                $ret = array();
                $ret[0] = '根目录';
                foreach ( $temp as $row )
                {
                    $ret[$row['id']] = $row['name'];
                }
                return $ret;
            })->rules('required');
            
            
            $form->text('seo_title', 'SEO标题');
            $form->text('seo_keywords', 'SEO关键词');
            $form->text('seo_description', 'SEO描述');
            //$form->editor('contentccc', '描述');//->rules('required')
            $form->editor('content', '内容')->help('如果是单页内容则填写，比如关于我们，联系我们');
            
            
            
        });
    }
}
