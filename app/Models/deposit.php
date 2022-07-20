<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class deposit extends Model
{
    protected $guarded = [];

    public function courier()
    {
        return $this->belongsTo('App\Models\user','courier_man','id');
    }
    public function order()
    {
        return $this->belongsTo('App\Models\order','order_id','id');
    }

    public function deposit_received()
    {
        return $this->belongsTo('App\Models\user','deposit_received_by','id')->withDefault();
    }





}
