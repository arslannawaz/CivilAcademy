<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CourseImage extends Model
{

    protected $fillable = ['course_id','image'];

    public function course()
    {
        return $this->belongsTo('App\Course','course_id');
    }


    public $timestamps = false;



}
