<?php

namespace App;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    //
    protected $fillable =[
        'logo','price','title_en','title_ar','description_en','description_ar',
        'status','category_id','document_en','document_ar',
        'registeration_procedure_en','registeration_procedure_ar',
        'lecturer_detail_en', 'lecturer_detail_ar',
        'fee_detail_en','fee_detail_ar',
        'test_detail_en','test_detail_ar',
        'payment_refund_policy_en','payment_refund_policy_ar',
        'organized_by'
    ];

    //images
    public function images()
    {
        return $this->hasMany(CourseImage::class,'course_id');
    }

    public function locations()
    {
        return $this->hasMany(CourseLocation::class,'course_id');
    }

    //course reviews or feedback
    public function reviews()
    {
        return $this->hasMany('App\CourseReview','course_id');
    }


    public function students()
    {
        return $this->belongsToMany('App\User', 'student_courses',
        'course_id','student_id');
    }

    public function courseCategory(){
        return $this->belongsTo('App\Category','category_id', 'id');
    }

    public function courseBooking(){
        return $this->hasMany('App\BookCourse','course_id', 'id');
    }

    public function organizedBy(){
        return $this->belongsTo('App\User','organized_by', 'id')->select('id','first_name','last_name');
    }

}
