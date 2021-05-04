<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CourseTest extends Model
{

    protected $fillable = ['course_id','title','status','passing_marks','duration',
                        'question_limit','test_type','created_at','updated_at'];

    // public $timestamps =false;
    //

}
