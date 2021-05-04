<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommonFrontImage extends Model
{
    protected $fillable = ['image','image_for'];
    public $timestamps = false;

}
