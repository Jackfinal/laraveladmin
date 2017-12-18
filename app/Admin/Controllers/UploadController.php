<?php
/**
 * Created by PhpStorm.
 * User: frowhy
 * Date: 2017/7/21
 * Time: 下午4:49
 */

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class UploadController extends Controller
{
    use ModelForm;

    /**
     * Storage instance.
     *
     * @var string
     */
    protected $storage = '';
    protected $preUrl  = '';

    protected $useUniqueName = false;

    /**
     * File name.
     *
     * @var null
     */
    protected $name = null;

    /**
     * Upload directory.
     *
     * @var string
     */
    protected $directory = '';

    public function postUploadImg(Request $request)
    {
        if ($request->hasFile('wang-editor-image-file')) {
            //
            $file     = $request->file('wang-editor-image-file');
            $data     = $request->all();
            $rules    = [
                'wang-editor-image-file' => 'max:5120',
            ];
            $messages = [
                'wang-editor-image-file.max' => '文件过大,文件大小不得超出5MB',
            ];

            $validator = Validator($data, $rules, $messages);
            
            if ($validator->fails()) {
                return 'error|文件过大,文件大小不得超出5MB';
            } else {
                if ($file->isValid()) {
                    $disk = Storage::disk('admin');
                    // 获取文件相关信息
                    $originalName = $file->getClientOriginalName();
                    $ext          = $file->getClientOriginalExtension();
                    $realPath     = $file->getRealPath();
                    $type         = $file->getClientMimeType();
                    $filename     = md5(uniqid()) . '.' . $ext;
                    
                    $bool         = $disk->put('wangEditor/'.$filename, file_get_contents($realPath));


                    if ($bool) {
                        return url('upload/wangEditor/' . $filename);
                    } else {
                        return 'error|上传失败';
                    }
                } else {
                    return 'error|文件错误';
                }
            }
        }
    }
}