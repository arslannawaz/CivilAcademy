<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BookingPayment extends Model
{
    protected $fillable = ['booking_id','price','payment_mode','status','transaction_id'];

}
