<?php

namespace App\Models;
use Illuminate\Support\Facades\Cache;
class Index
{
    
    public static function menu()
    {
        $key = '_home_menu_';
        $menu = array();
        
        $menu = Cache::get($key);
        if($menu) return $menu;
        $menu_string = config('home_nav');
        if(strstr($menu_string, chr(10)))
        {
            $menu_arr = explode(chr(10), $menu_string);
            foreach ($menu_arr as $row)
            {
                if(strstr($menu_string, '-'))
                {
                    $temp_arr = explode('-', $row);
                    $menu[] = $temp_arr;
                }
            }
            Cache::put($key, $menu, 604800);
        }
        return $menu;
        
    }
    
    public static function getList($query, $tables = 'articles')
    {
        //\DB::connection()->enableQueryLog();\DB::getQueryLog()
        $table = \DB::table($tables);
        if(!empty($query))
        {
            $array = self::convertQuery($query);
            //排除部分没有使用软删除的表
            if(!isset($array['deleted_at']))
            {
                $table->whereNull('deleted_at');
            }
            unset($array['deleted_at']);
            
            foreach ($array as $key=>$row)
            {
                switch ($key)
                {
                    case 'table':
                        $tables = $row;
                        $table = \DB::table($row);
                        break;
                    case 'limit':
                        $table->limit($row);
                        break;
                    case 'order':
                        if(strstr(',', $row))
                        {
                            $temp = explode(',', $row);
                            $table->orderBy($temp[0],$temp[1]);
                        }
                        break;
                    default:
                        if($row == 'notnull')
                        {
                            $table->whereNotNull($key);
                        }else {
                            $table->where($key, $row);
                        }
                        break;
                }
            }
            
        }
        
        return $table->select()->orderBy('id','desc')->get();
    }
    
    public static function convertQuery($query)
    {
      $queryParts = explode('&', $query);
      $params = array();
      foreach ($queryParts as $param) {
        $item = explode('=', $param);
        $params[$item[0]] = $item[1];
      }
      return $params;
    }
}
