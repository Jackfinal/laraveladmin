<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Report;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use App\Exceptions\CreateKey;
use App\Models\ChinaArea;
use App\Rules\Sms;
use App\Rules\Captcha;
use App\Models\ReportReply;
use App\Models\QuickReply;
use Jenssegers\Agent\Agent as Agent;
class ReportController extends Controller
{
    protected $type = 0;
    public function __construct(Request $request)
    {
        $this->type = $request->id;
        
        if(!in_array($this->type, array('1', '2', '3'), true))
        {
            $this->type = '3';
        }
        if($this->type == '1')
        {
            $this->middleware('auth');
        }
        
    }
    public function show(Request $request)
    {
        $rules = [
            'key' => 'required|size:20',
            /* 'captcha' => [
                'required',
                new Captcha
            ], */
        ];
        $message = [
            'required' => '必须填写',
            'size' => '编号长度不对'
        ];
        $this->validate($request, $rules, $message);
        $info = Report::where('key', $request->key)->first();
        if( $info )
        {
            return view('index.report.detail', compact('info'));
        }else {
            return redirect()->back()->withInput()->withErrors('编号不正确，查询失败，请稍后再试！');
        }
        
    }
    public function getIndex(Request $request)
    {
        $type = $request->id;
        $Auth = new Auth();
        $HarmType = Report::getSelectType('base_type');
        $ReportType = Report::getSelectType('base_report');
        $base_search = Report::getSelectType('base_search');
        $base_download = Report::getSelectType('base_download');
        $base_communication = Report::getSelectType('base_communication');
        $base_account = Report::getSelectType('base_account');
        $base_pan = Report::getSelectType('base_pan');
        //地区列表
        $city = ChinaArea::where( 'parent_id', 3 )->get();
        
        //上一次提交返回错误时候，存在的数据
        $requestData = [];
        if ($request->session()->has('requestData')) {
            //获取数据
            $requestData = $request->session()->get('requestData');
        }
        return view('index.report.view', compact('type', 'city', 'requestData', 'Auth','HarmType','ReportType','base_search','base_download','base_communication','base_account','base_pan'));
    }
    
    public function store(Request $request)
    {
        //闪存请求数据到session ,用于错误返回,记住选项
        $arr = ($request->all());
        unset($arr['smetas']);
        $request->session()->flash('requestData', $arr);
        $this->validate_self($request);
        
        $report = new Report();
        $report->report_type = $request->report_type;
        $report->report_content = $request->report_content;
        $report->harm_type = $request->harm_type;
        $report->type = $this->type;
        
        $fileds = [
            'report_detail_url', 'keyword', 'otherseids','report_appname','report_other_appsource',
            'accountname','toolothername','driveothername','seids','report_app_type','toolname',
            'accountnature','drivename','report_webname','report_columm'
            
        ];
        if($this->type == 2 && !Auth::check())
        {
            $fileds = array_merge($fileds,[
                'real_name','real_email','real_sex','real_mobile','real_provinces','real_city','real_district'
            ]);
        }
        foreach ($fileds as $row)
        {
            if($row == 'real_mobile')
            {
                if(isset($request->$row))$report->$row = $request->mobile;
            }else {
                if(isset($request->$row))$report->$row = $request->$row;
            }
            
        }
        //处理实名举报手机版地区问题
        $Agent = new Agent();
         // agent detection influences the view storage path
        if ($Agent->isMobile()) {
            $mobile_area = $this->do_area($request);
            $report->real_provinces = $mobile_area['r0'];
            $report->real_city = $mobile_area['r1'];
            $report->real_district = $mobile_area['r2'];
        }
        if(Auth::check())
        {
            $report->uid = Auth::id();
            $report->real_name = Auth::user()->name;
            $report->real_email = Auth::user()->email;
            $report->real_mobile = Auth::user()->mobile;
            $report->real_address = Auth::user()->address;
            $report->real_provinces = (int)Auth::user()->provinces;
            $report->real_city = (int)Auth::user()->city;
            $report->real_district = (int)Auth::user()->district;
            $report->real_sex = Auth::user()->sex;
        }
        
        $smeta = $this->uploadFilesSmeta($request);
        if(!empty($smeta))$report->smeta = json_encode($smeta);
        
        $report->report_ip = $request->getClientIp();
        $ret = new ReportReply();
        $ret->content =1;
        
        if($report->save())
        {
            //问题默认回复
            $ReportReply = new ReportReply();
            $ReportReply->report_id = $report->id;
            $ReportReply->content = QuickReply::find(1)->title;
            $ReportReply->save();
            //更新编号
            $report->key = CreateKey::getKey($report->id);
            //var_dump($report->key);die;
            $report->save();
            //var_dump(111);die;
            if($Agent->isMobile())
            {
                return redirect('/mobile/report/main/')->with('report_ok' ,'举报成功，编号为: '.$report->key);
                
            }
            return redirect('/report/view/'.$report->type)->with('report_ok' ,'举报成功，编号为: '.$report->key);
        }else {
            return redirect()->back()->withInput()->withErrors('举报失败，请稍后再试！');
        }
    }
    
    protected function validate_self($request)
    {
        $rules = [
            'report_type' => [
                'required',
                Rule::in(Report::getSelectTypeId('base_report'))
            ],
            'report_content' => 
                'required'
            ,
            /* 'captcha' => [
                'required',
                new Captcha
            ], */
            'report_detail_url' => 
                'sometimes|required'
            ,
            'keyword' => 
                'sometimes|required|max:255'
            ,
            'otherseids' => 
                'sometimes|required|max:255'
            ,
            'report_appname' => 
                'sometimes|required|max:255'
            ,
            'report_other_appsource' => 
                'sometimes|required|max:255'
            ,
            'accountname' => 
                'sometimes|required|max:255'
            ,
            'toolothername' => 
                'sometimes|required|max:255'
            ,
            'driveothername' => 
                'sometimes|required|max:255'
            ,
            'seids' => [
                'sometimes',
                Rule::in(Report::getSelectTypeId('base_search')),
            ],
            'report_app_type' => [
                'sometimes',
                Rule::in(Report::getSelectTypeId('base_download')),
            ],
            'toolname' => [
                'sometimes',
                Rule::in(Report::getSelectTypeId('base_communication')),
            ],
            'accountnature' => [
                'sometimes',
                Rule::in(Report::getSelectTypeId('base_account')),
            ],
            'drivename' => [
                'sometimes',
                Rule::in(Report::getSelectTypeId('base_pan')),
            ],
            'harm_type' => [
                'sometimes',
                Rule::in(array_values(Report::getSelectTypeId('base_type'))),
            ],
            
            'smetas' => 'sometimes|max:10240'
        ];
        $Agent = new Agent();
        if (!$Agent->isMobile()) {
            $rules = array_merge($rules, ['captcha' => [
                'required',
                new Captcha
            ]]);
        }
        if($this->type == 2 && !Auth::check())
        {
            $rules = array_merge( $rules, [
                'code' => [
                    'required',
                    new Sms( $request->mobile, 'report' )
                ]
            ] );
        }
        $message = [
            'report_webname.required' => '网站名称必须填写.',
            'report_detail_url.required' => '网站名称必须填写.',
            'report_content.required' => '网站名称必须填写.',
            'required' => '必须填写.',
            'max' => '字数不能超过: :max.',
            'in' => '类型必须是: :values.',
            'smetas.max' => '文件过大,文件大小不得超出10MB',
        ];
        $this->validate($request, $rules, $message);
        
    }
    
    protected function uploadFilesSmeta($request)
    {
        $result = [];
        if ($request->hasFile('smetas')) {
            //
            $files     = $request->file('smetas');
                $disk = Storage::disk('admin');
                foreach ($files as $file)
                {
                    if ($file->isValid()) {
                        
                        // 获取文件相关信息
                        $originalName = $file->getClientOriginalName();
                        $ext          = $file->getClientOriginalExtension();
                        $realPath     = $file->getRealPath();
                        $type         = $file->getClientMimeType();
                        $filename     = md5(uniqid()) . '.' . $ext;
                    
                        $bool         = $disk->put('report/'.$filename, file_get_contents($realPath));
                    
                        if ($bool) {
                            $result[] = [
                                'url' => url('upload/report/' . $filename),
                                'ext' => $ext,
                                'type' => $type,
                                'originalName' => $originalName,
                                'filename' => $filename
                            ];
                        }
                    }
                }
                
            
            
        }
        return $result;
        
    }
    
    protected function do_area($request)
    {
        if(!is_numeric($request->provinces) && is_string($request->provinces))
        {
            if(strstr($request->provinces, ' '))
            {
                $arr = explode(' ', $request->provinces);
                //查询ID
                $r0 = 3;
                $r1 = ChinaArea::where('name', $arr[1])->first();
                if($r1)$r1= $r1->id;else $r1 = '3401';
                $r2 = ChinaArea::where('name', $arr[2])->first();
                if($r2)$r2= $r2->id;else $r2 = '3404';
                return compact('r0', 'r1', 'r2');
            }
        }
    }
    
}


