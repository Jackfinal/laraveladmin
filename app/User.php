<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Report;
use Illuminate\Support\Facades\Cache;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','mobile'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    
    public function reports()
    {
        return $this->hasMany(Report::class,'uid', 'id');
    }
    
    /**
     *  send sms
     * @param number $acceptor_tel
     * @param string $content
     * @param string $type: register,forgotpassword
     * @return string
     */
    protected static function sendSms($acceptor_tel, $content, $codeNumber, $type)
    {
        $send_url = "http://access.xx95.net:8886/Connect_Service.asmx/SendSms";
        $str = 'epid=AHHF1295391&User_Name=zaxw&password=1290057e45b9a2a0&phone='.$acceptor_tel.'&content='.$content.'';
        $client = new \GuzzleHttp\Client();
        $res = $client->request('POST', $send_url, [
            'form_params' => [
                'epid' => 'AHHF1295391',
                'User_Name' => 'zaxw',
                'password' => '1290057e45b9a2a0',
                'phone' => $acceptor_tel,
                'content' => $content
            ]
            
        ]);
        //存入防止多次发短信
        Cache::put($acceptor_tel, $codeNumber, 1);
        //存入验证码
        $key = $acceptor_tel.$type;
        Cache::put($key, $codeNumber, 10);
        //var_dump($res);die;
        return $res->getBody();
    }
}
