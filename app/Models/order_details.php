<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class order_details extends Model
{
    protected $guarded =[];
    public function product()
    {
        return $this->belongsTo('App\Models\product','product_id','id');
    }
}
