<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class courier_man extends Model
{
    protected $guarded = [];
    public function user()
    {
        return $this->belongsTo('App\Models\user','user_id','id');
    }

    public function area()
    {
        return $this->belongsTo('App\Models\area','area_id','id');
    }


}
