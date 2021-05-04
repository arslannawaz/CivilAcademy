<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SpecialOffer extends Model
{
    protected $fillable = [
        'before_price',
        'after_price',
        'discount',
        'trainer_id',
        'course_id',
        'promotion_code',
        'promotion_start_date',
        'promotion_end_date',
        'title_en',
        'title_ar',
        'description_en',
        'description_ar',
        'status',
        'created_at',
        'updated_at'
    ];

    public function images()
    {
        return $this->hasMany('App\SpecialOfferImage','special_offer_id');
    }

    public function course()
    {
        return $this->belongsTo('App\Course','course_id','id');
    }

}
