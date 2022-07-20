<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class sub_category extends Model
{
    protected $guarded =[];
    public function category()
    {
        return $this->belongsTo('App\Models\category','category_id','id');
    }
    public function product()
        {
            return $this->hasMany('App\Models\product','sub_category_id','id');
        }



}
