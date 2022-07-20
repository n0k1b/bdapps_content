<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class product_brand extends Model
{
    //use HasFactory;
    protected $guarded = [];
    public function sub_category()
    {
        return $this->belongsTo('App\Models\sub_category','sub_category_id','id');
    }

}
