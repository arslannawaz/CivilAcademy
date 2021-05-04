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

class PaymentController extends Controller
{
    /**
     * Last error message(s)
     * @var array
     */
    protected $_errors = array();

    /**
     * API Credentials
     * Use the correct credentials for the environment in use (Live / Sandbox)
     * @var array
     */
    protected $_credentials = array(
        // 'USER' => "sb-rrvby3553309_api1.business.example.com",
        // 'PWD' => 'M8A79DX25WLPY9FR',
        // 'SIGNATURE' => 'AwAhdgEb0gBt51gCCUt1Y3r2VpWoAO1q-3Or781Xy-5J98cpahrebwCL',

        'USER' => "sb-sgavo5009383_api1.business.example.com",
        'PWD' => "4GHGPX8B896LHJTK",
        'SIGNATURE' => "ANdbH4ZXsKVWs0zGFd.rQAWV.sIlA3V810se1nJpN6rmrB-1g4dhsPmo",
    );

    /**
     * API endpoint
     * Live - https://api-3t.paypal.com/nvp
     * Sandbox - https://api-3t.sandbox.paypal.com/nvp
     * @var string
     */
    protected $_endPoint = 'https://api-3t.sandbox.paypal.com/nvp';

    /**
     * API Version
     * @var string
     */
    protected $_version = '86.0';

    /**
     * Make API request
     *
     * @param string $method string API method to request
     * @param array $params Additional request parameters
     * @return array / boolean Response array / boolean false on failure
     */

    public function request($method,$params = array())
    {
        //if(!isset($this->check)){
            $request = Request();

            $this->validate($request, [
                'time' => 'required|string',
                'date' => 'required|string',
                'description' => 'required|string',
                'payment_mode' => 'required',
                'course_id' => [
                    'required','string',
                    Rule::unique('book_courses')->where(function ($query) use($request) {
                        $user_id=auth()->user()->id;
                        $query->where('course_id', $request->course_id)
                        ->where('student_id', $user_id);
                    })
                ],
            ]);

            $course = Course::find($request->course_id);
            if(!isset($course)){
                return response()->json(["message"=>"Course not found"],404);
            }
        //}

        $this->_errors = array();
        if (empty($method)) { //Check if API method is not empty
            $this->_errors = array('API method is missing');
            return false;
        }

        //Our request parameters
        $requestParams = array(
                'METHOD' => $method,
                'VERSION' => $this->_version
            ) + $this->_credentials;

        //Building our NVP string
        $request = http_build_query($requestParams + $params);

        //cURL settings
        $curlOptions = array(
            CURLOPT_URL => $this->_endPoint,
            CURLOPT_VERBOSE => 1,

            /*
             * If you are using API Signature rather then certificates, leave the code below commented out
             */
            //  CURLOPT_SSL_VERIFYPEER => true,
            //  CURLOPT_SSL_VERIFYHOST => 2,
            // CURLOPT_CAINFO => dirname(__FILE__) . '/cacert.pem', //CA cert file
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $request
        );


        $ch = curl_init();
        curl_setopt_array($ch, $curlOptions);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);        //  Skip peer certificate verification   - Comment this if you are using Certificates instead of API Signature
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);        // Skip host certificate verification    - Comment this as well if you are using Certificates instead of API Signature
        //Sending our request - $response will hold the API response
        $response = curl_exec($ch);

        //Checking for cURL errors
        if (curl_errno($ch)) {
            $this->_errors = curl_error($ch);
            curl_close($ch);
            return false;
            //Handle errors
        } else {
            curl_close($ch);
            $responseArray = array();
            parse_str($response, $responseArray); // Break the NVP string to an array
            return $responseArray;
        }
    }



}



$requestParams = array(
    'IPADDRESS' => $_SERVER['REMOTE_ADDR'],          // Get our IP Address
    'PAYMENTACTION' => 'Sale'
);
$cardnumber =  str_replace(" ","",Request('number'));
$exp1 =  str_replace("/","",Request('expiry'));
$exp2  = str_replace(" ","",$exp1);

$creditCardDetails = array(

    'ACCT' => $cardnumber,
    'EXPDATE' => $exp2,          // Make sure this is without slashes (NOT in the format 07/2017 or 07-2017)
    'CVV2' => Request('cvv')
);

$payerDetails = array(
    'FIRSTNAME' => Request('name'),
);

$orderParams = array(
    'AMT' => Request('totalprice'),              // This should be equal to ITEMAMT + SHIPPINGAMT
    'CURRENCYCODE' => 'USD'       // USD for US Dollars
);

$item = array(
    'L_NAME0' => Request('Civil'),
    'L_DESC0' => 'Course Booking',
    'L_AMT0' => Request('totalprice')

);


$paypal = new PaymentController();
$response = $paypal->request('DoDirectPayment',
$requestParams + $creditCardDetails + $payerDetails + $orderParams + $item);




if ($response == true) {

    $response = json_encode($response);
    $response = json_decode($response, true);
    if ($response['ACK'] == 'Success') { // Payment successful

        $request = Request();
        $course = Course::find($request->course_id);
        $booking_img ='https://www.osservatorioantitrust.eu/en/wp-content/uploads/2015/04/book-2.jpg';
        $booking_video = 'https://www.osservatorioantitrust.eu/en/wp-content/uploads/2015/04/book-2.jpg';

        if($request->image)
        {
            $booking_img = $request->image;
        }

        if($request->video)
        {
            $booking_video = $request->video;
        }

        $courseBooking = BookCourse::create([
            'course_id' => $request->course_id,
            'time' => $request->time,
            'date' => $request->date,
            'description' => $request->description,
            'image' => $booking_img,
            'video' => $booking_video,
            'student_id' => auth()->user()->id,
            'trainer_id' => $course->organized_by,
        ]);

        $bookingPayment = BookingPayment::create([
            'booking_id' => $courseBooking->id,
            'price' => $course->price,
            'payment_mode' => $request->payment_mode,
            'transaction_id' => $response['TRANSACTIONID'],
        ]);

        $message = "Your course ".$courseBooking->bookingCourse->title_en." has been Booked by " .$courseBooking->bookingStudent->first_name;
        $notification=notifiedWithEvent($courseBooking->trainer_id,auth()->user()->id,$message,'course_booking',$courseBooking->id);

        $message_event = "We have new Booking for course";
        $event_calender=saveEventCalender("course",$courseBooking->id,$message_event,auth()->user()->id);

        //return ok("Booking has been made successfully");

        $responseurl = 'Your Transaction has been Successfull, Booking has been made!';
        $responseur2 = $response['ACK'];
        
        echo response()->json(['transaction_id'=>$response['TRANSACTIONID'],'message'=> $responseurl,'response' => $responseur2 ,'booking_id' => $courseBooking->id]);
      

    } else {
        $responseur2 = $response['ACK'];
        $responseurl = $response['L_LONGMESSAGE0'];
        echo response()->json(['message'=> $responseurl,'response' => $responseur2]);
    }
}
