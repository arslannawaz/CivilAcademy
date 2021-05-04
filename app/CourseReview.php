<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CourseReview extends Model
{
    //
    protected $fillable =
    ['user_id','course_id','rating','comment','type','review_to'];

    public function course()
    {
        return $this->belongsTo('App\Course','course_id');
    }

    public $timestamps = false;

    public function userReview(){
        return $this->belongsTo('App\User','user_id', 'id')->select('id','first_name','last_name','image');
    }

    public function replyReview(){
        return $this->hasOne('App\ReplyReview','review_id', 'id');
    }

}
