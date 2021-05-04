<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoryRequest extends Model
{
    protected $fillable = ['image','title_en','title_ar','trainer_id','description_en','description_ar','status'];

    public function byTrainer(){
        return $this->belongsTo('App\User','trainer_id', 'id')->select('id','first_name','last_name');
    }
}
