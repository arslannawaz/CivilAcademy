<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommonFrontSocialLink extends Model
{
    protected $fillable =['key','value','link_for'];
    public $timestamps = false;
}
