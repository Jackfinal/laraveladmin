<?php

namespace App\Admin\Extensions\Form;

use Encore\Admin\Form\Field;

/**
 * Created by PhpStorm.
 * User: frowhy
 * Date: 2017/7/21
 * Time: 下午12:02
 */
class WangEditor extends Field
{
    protected $view = 'admin.form.editor';

    protected static $css = [
        '/vendor/wangEditor-2.1.23/dist/css/wangEditor.min.css',
    ];

    protected static $js = [
        '/vendor/wangEditor-2.1.23/dist/js/wangEditor.min.js',
    ];

    public function render()
    {
        $editor_id = $this->id;
        $z_index   = 999999;

        $printLog     = config('wang-editor.printLog', 'true');
        $uploadImgUrl = config('wang-editor.uploadImgUrl', '/admin/upload/uploadImg');
        $pasteFilter  = config('wang-editor.pasteFilter', 'false');
        $pasteText    = 'true';
        if ($pasteFilter == 'true') {
            $pasteText = config('wang-editor.pasteText', 'true');
        }
        $token = csrf_token();

        $this->script = <<<EOT

            var menus = [
                'source',
                '|',
                'bold',
                'underline',
                'italic',
                'strikethrough',
                'eraser',
                'forecolor',
                'bgcolor',
                '|',
                'quote',
                'fontfamily',
                'fontsize',
                'head',
                'unorderlist',
                'orderlist',
                'alignleft',
                'aligncenter',
                'alignright',
                '|',
                'link',
                'unlink',
                'table',
                'emotion',
                '|',
                'img',
                'video',
                'insertcode',
                '|',
                'undo',
                'redo',
                'fullscreen'
            ];
            wangEditor.config.printLog = {$printLog};
            var _{$editor_id} = new wangEditor('{$editor_id}');
            _{$editor_id}.config.uploadImgUrl = "{$uploadImgUrl}";
            _{$editor_id}.config.uploadParams = {
                    _token : '{$token}'
            };
            _{$editor_id}.config.zindex = {$z_index};
            var _pasteFilter = {$pasteFilter};
            _{$editor_id}.config.pasteFilter = _pasteFilter;
            if (_pasteFilter == true) {
                _{$editor_id}.config.pasteText = {$pasteText};
            }
            _{$editor_id}.config.uploadImgFileName = 'wang-editor-image-file';
            _{$editor_id}.create();
EOT;

        return parent::render();
    }
}