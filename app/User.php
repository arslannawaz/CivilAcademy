<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;


class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    'image','first_name','last_name', 'phone','country', 'role','email', 'password',
        'dob', 'nationality','gender','residence',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function getJWTIdentifier()
    {
      return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
      return [];
    }

    public function student()
    {
        return $this->hasOne('App\Student','user_id')->with('courses');
    }

    public function studentCourses()
    {
        return $this->belongsToMany('App\Course', 'student_courses',
        'student_id', 'course_id')->withPivot('course_learning','test_type');
    }

    public function trainer()
    {
        return $this->hasOne('App\Trainer','user_id','id');
    }

}
