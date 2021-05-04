<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    //


    protected $fillable = ['charge_key','charge_value','status'];

    public $timestamps = false;

}
