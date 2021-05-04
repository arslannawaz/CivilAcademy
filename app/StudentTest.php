<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentTest extends Model
{
    protected $fillable = ['test_id','student_id'];

    public function studentAnswer()
    {
        return $this->hasMany('App\StudentQuestionAnswer','student_test_id','id');
    }

    public function test()
    {
        return $this->belongsTo('App\CourseTest','test_id','id');
    }

    public function studentName()
    {
        return $this->belongsTo('App\User','student_id','id');
    }

}
