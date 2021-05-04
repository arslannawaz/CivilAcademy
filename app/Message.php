<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable =['student_id','trainer_id','sender','message','status'];

}
