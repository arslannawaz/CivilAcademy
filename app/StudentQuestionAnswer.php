<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentQuestionAnswer extends Model
{
    protected $fillable =['student_test_id','question_id','student_answer','status'];

    public function question()
    {
        return $this->belongsTo('App\TestQuestion','question_id','id');
    }

}
