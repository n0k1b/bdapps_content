<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class purchase extends Model
{
    //use HasFactory;

    protected $guarded = [];
    public function supplier()
    {
        return $this->belongsTo('App\Models\supplier','supplier_id','id');
    }

    public function product()
    {
        return $this->belongsTo('App\Models\product','product_id','id');
    }
}
