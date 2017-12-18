<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Encore\Admin\Traits\AdminBuilder;
use Encore\Admin\Traits\ModelTree;

class Category extends Model
{
    //
    use SoftDeletes;
    use ModelTree, AdminBuilder;
    
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    
        $this->setParentColumn('parent_id');
        $this->setOrderColumn('listorder');
        $this->setTitleColumn('name');
    }
}
