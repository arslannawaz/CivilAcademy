<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $fillable = ['gender','country','user_role','status','is_send',
    'title_en','subject_en','message_en','title_ar','subject_ar','message_ar'];
}
