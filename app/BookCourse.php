<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BookCourse extends Model
{
    protected $fillable = ['course_id','student_id','trainer_id','time','date','description','status','image','video'];

    public function bookingStudent(){
        return $this->belongsTo('App\User','student_id', 'id');
    }

    public function courseTrainer(){
        return $this->belongsTo('App\User','trainer_id', 'id');
    }

    public function bookingCourse(){
        return $this->belongsTo('App\Course','course_id', 'id');
    }

    public function bookingPayment(){
        return $this->hasOne('App\BookingPayment','booking_id', 'id');
    }

}
