<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReplyReview extends Model
{
    protected $fillable =['user_id','review_id','rating','comment'];

    public function trainerReview(){
        return $this->belongsTo('App\User','user_id', 'id')->select('id','first_name','last_name','image');
    }

    public function replyFrom(){
        return $this->belongsTo('App\User','user_id', 'id')->select('id','first_name','last_name','image');
    }

    public function checkReview(){
        return $this->belongsTo('App\CourseReview','review_id', 'id');
    }
}
