<?php
namespace App\Admin\Extensions\Tools;

use Encore\Admin\Admin;
use Encore\Admin\Grid\Tools\AbstractTool;
class ImportReport extends AbstractTool
{
    protected function script()
    {
        $url = url('admin/reportImport');

        return <<<EOT
$('#uplaodXls a').click(function(){
    $('#uplaodXls input[type=file]').click();    
})
$('#uplaodXls').change(function () {
console.log(new FormData($('#uplaodXls')));
    $.ajax({
            url: "$url",
            type: "POST",
            data:  new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            // 显示加载图片
            beforeSend: function () {
                $('.loading-shadow').addClass('active');
            },
            success: function (data) {console.log(data);
               
            },
            error: function(){}             
        });     
});
EOT;
    }

    public function render()
    {
        Admin::script($this->script());
        $csrf_field = csrf_field();

        $options = [
            'all'   => 'All',
            'm'     => 'Male',
            'f'     => 'Female',
        ];

        return view('admin.tools.import', compact('options','csrf_field'));
    }
}