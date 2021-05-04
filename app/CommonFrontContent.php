<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommonFrontContent extends Model
{

    protected $fillable = ['title_en','title_ar','content_en','content_ar','data_for'];
    public $timestamps = false;

}
