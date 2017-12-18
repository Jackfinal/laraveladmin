<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Cache;
use Gregwar\Captcha\CaptchaBuilder;
use Session;
class CommonController extends Controller
{
    
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }
    public function sendSms(Request $request)
    {
        //验证60S只能发送一次短信
        if (Cache::get($request->mobile))return response('请勿重新发送短信，请等待60S.', 302);
    
        $messages = [
            'mobile.required' => '手机号必须填写.',
            'mobile.unique' => '手机号已重复.',
            'mobile.size' => '手机号为11位.',
            'type.required' => '验证码类型必须填写.',
            'type.in' => '验证码类型不对.',
        ];
        $ruls = [
            'type' => [
                'required',
                Rule::in(['register', 'forgotpassword', 'report']),
            ]
        ];
        if( $request->register )
        {
            $ruls['mobile'] = 'required|unique:users|size:11';
        }else {
            $ruls['mobile'] = 'required|size:11';
        }
        $this->validate($request, $ruls, $messages);
    
        $codeNumber = rand(1000,9999);
        $content = str_replace('[code]', $codeNumber, config('sms_register'));
        $res = User::sendSms($request->mobile, $content, $codeNumber ,$request->type);
        //return $content;
        return $res;
    
    }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function captcha($tmp)
    {
        //生成验证码图片的Builder对象，配置相应属性
        $builder = new CaptchaBuilder;
        //可以设置图片宽高及字体
        $builder->build($width = 100, $height = 40, $font = null);
        //获取验证码的内容
        $phrase = $builder->getPhrase();
    
        //把内容存入session
        Session::flash('milkcaptcha', $phrase);
        //生成图片
        header("Cache-Control: no-cache, must-revalidate");
        header('Content-Type: image/jpeg');
        $builder->output();
    }
    
    
}
