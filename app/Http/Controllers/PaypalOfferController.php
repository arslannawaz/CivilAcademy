<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Course;
use App\CourseImage;
use App\CourseLocation;
use App\BookCourse;
use App\SpecialOffer;
use Illuminate\Support\Facades\DB;
use App\BookingPayment;
use App\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Omnipay\Omnipay;

class PaypalOfferController extends Controller
{
    public $gateway;
    
    public function __construct()
    {
        $this->gateway = Omnipay::create('PayPal_Rest');
        $this->gateway->setClientId('ARG4SgoGAXLQtg73tKmEcJMxZQt0r4nw5hW0qnfXqJcoiOJoR5-WhtpiymZbrVCdwwr0W0oflyIT4aAf');
        $this->gateway->setSecret('EIyxxlFTJF1opbUwX3xig5TQtP6uUqvsSzVNC2nBro6-OyhcbuSGL7K6JUoE8kYihZQVJB0cSZKBdL6s');
        $this->gateway->setTestMode(true); // here 'true' is for sandbox. Pass 'false' when go live
    }


    public function charge($id)
    {

        $special_offer=SpecialOffer::find($id);
        if(!isset($special_offer)){
            return error("Offer not found",404);
        }

        $course = Course::find($special_offer->course_id);
        if(!isset($course)){
            return error("Course not found",404);
        }
   
            try {
                $response = $this->gateway->purchase(array(
                    'amount' => $special_offer->after_price,
                    'currency' => 'USD',
                    'returnUrl' => url('payment-success'),
                    'cancelUrl' => url('payment-error'),
                ))->send();
          
                if ($response->isRedirect()) {
                    $response->redirect(); // this will automatically forward the customer
                } else {
                    // not successful
                    return $response->getMessage();
                }
            } catch(\Exception $e) {
                //return $e->getMessage();
                return transactionResponseError('INVALID_CARD_DETAIL');
            }
        
    }

    public function payment_success(Request $request)
    {
        // Once the transaction has been approved, we need to complete it.
        if ($request->input('paymentId') && $request->input('PayerID'))
        {
            $transaction = $this->gateway->completePurchase(array(
                'payer_id'             => $request->input('PayerID'),
                'transactionReference' => $request->input('paymentId'),
            ));
            $response = $transaction->send();
         
            if ($response->isSuccessful())
            {
                // The customer has successfully paid.
                $arr_body = $response->getData();      
                $transaction_id = $arr_body['id'];  
                $responseurl = 'Your Transaction has been Successfull';

                //return transactionResponse($transaction_id,$responseurl);
                return view('paypal_success',compact('transaction_id','responseurl'));   


            } else {
                return $response->getMessage();
            }
        } else {
            //return error('Transaction has been declined');
            return view('paypal_error');
        }
    }
 
    public function payment_error()
    {
        //return error('User has cancelled the payment.');
        return view('paypal_error');
    }
}