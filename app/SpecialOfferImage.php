<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SpecialOfferImage extends Model
{
    protected $fillable = ['special_offer_id','image'];
    public $timestamps = false;
}
