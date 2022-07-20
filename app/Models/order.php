<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class order extends Model
{
    protected $guarded =[];
    public function user()
    {
        return $this->belongsTo('App\Models\User','user_id','id');
    }

    public function courier()
    {
        return $this->belongsTo('App\Models\User','courier_man','id');
    }
     public function address()
    {
        return $this->belongsTo('App\Models\user_address','address_id','id');
    }
}
