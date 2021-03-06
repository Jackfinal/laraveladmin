<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Article extends Model
{
    //
    use SoftDeletes;
    
    public function category()
    {
        return $this->belongsTo('App\Models\Category','post_type','id');
    }
}
