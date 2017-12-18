<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.finaly.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: jack final <final_m@foxmail.com>
// +----------------------------------------------------------------------
namespace App\Exceptions;
/**
 * 生成唯一编号
 * 
 * **/
class CreateKey
{
    /**
     * 
     * @param string $date
     */
    public static function getKey( $id, $date = '' )
    {
        if(!$date)$date = date('ymdHis');
        //return $date;
        return ''.$date.str_pad( $id, 8, '0', STR_PAD_LEFT ).'';
    }
}
