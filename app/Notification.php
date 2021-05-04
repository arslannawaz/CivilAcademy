<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = ['to','from','message','route','read','event','event_id'];

    public function toUser(){
        return $this->belongsTo('App\User','to','id')->select('id','first_name','last_name');
    }

    public function fromUser(){
        return $this->belongsTo('App\User','from','id')->select('id','first_name','last_name');
    }
}
