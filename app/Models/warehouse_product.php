<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class warehouse_product extends Model
{
    protected $guarded =[];
    public function product()
    {
        return $this->belongsTo('App\Models\product','product_id','id');
    }
    public function warehouse()
    {
        return $this->belongsTo('App\Models\warehouse','warehouse_id','id')->withDefault();
    }

}
