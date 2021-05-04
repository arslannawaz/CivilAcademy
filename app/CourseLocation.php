<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CourseLocation extends Model
{

    protected $fillable = ['course_id','country','address','area','state'];

}
