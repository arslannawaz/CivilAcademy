<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TestQuestion extends Model
{
    //
    protected $fillable = ['image','course_test_id','question','option_a'
    ,'option_b','option_c','option_d','answer','status','created_at','updated_at','audio'];

}
