<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class homepage_product_list extends Model
{
    protected $guarded =[];
    public function product()
    {
        return $this->belongsTo('App\Models\product','product_list','id');
    }
    public function homepage_section()
    {
        return $this->belongsTo('App\Models\homepage_section','homepage_section_id','id');
    }

}
