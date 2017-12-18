<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
class UserGroup extends Model
{
    /**
     * 根据得分获取用户组
     * **/
    public static function belongsToGroup($score){
        $key = 'belongsToGroup'.$score;
        $result = Cache::get($key);
        if($result)
        {
            return $result;
        }
        
        $list = UserGroup::all();
        
        foreach ($list as $row)
        {
            if( $score < $row->maxscore && $score > $row->minscore){
                Cache::put($key, $row, 86400);
                return $row;
            }
        }
    }
}
