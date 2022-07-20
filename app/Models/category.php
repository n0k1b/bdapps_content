<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class category extends Model
{
    protected $guarded =[];
    public function domain()
    {
        return $this->belongsTo('App\Models\domain','domain_id','id');
    }

    public function sub_category()
        {
            return $this->hasMany('App\Models\sub_category','category_id','id');
        }
}
