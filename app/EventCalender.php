<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventCalender extends Model
{
    protected $fillable =['event_title','event_id','updated_by','event_description'];

    public function addedBy(){
        return $this->belongsTo('App\User','updated_by', 'id')->select('id','first_name','last_name');
    }


}
