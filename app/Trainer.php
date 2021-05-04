<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Trainer extends Model
{
    //

    protected $fillable =['residence','organization','experience',
                'course_offered','user_id','trade_licence','certificates',
                'emirate_id','trn_certificate','created_at','updated_at'];

}
