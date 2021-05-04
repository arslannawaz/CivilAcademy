<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\Pivot;

class StudentCourse extends Model
{

    protected $fillable =
    ['test_type','course_learning','course_id','student_id','status'];

    public $timestamps = false;
    public $table = 'student_courses';

}
