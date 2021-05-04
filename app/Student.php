<?php

namespace App;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;


class Student extends Model
{

    protected $fillable =['user_id','course_learning',
    'test_type','dob','created_at','updated_at'];


    // public $timestamps = false;
    public static function store($data)
    {
        return ok(Student::create($data));
    }

     // public $timestamps = false;
     public static function updateStd($data)
     {
         $std = Student::find($data['id']);
         return $std->update($data);
     }

    public function courses()
    {
        return $this->belongsToMany('App\Course', 'student_courses', 'student_id', 'course_id');
    }


}
