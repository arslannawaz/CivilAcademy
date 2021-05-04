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

class CourseBookingController extends Controller
{
    
    public function __construct()
    {
        $this->gateway = Omnipay::create('PayPal_Pro');
        $this->gateway->setUsername('sb-8kxek5009269_api1.business.example.com');
        $this->gateway->setPassword('GDPJBXL4W5TP7LXL');
        $this->gateway->setSignature('AmgVXO4CO1KCFEMa1xy0vcpU5qZcAGuq4rQDtPh7qW5cWeCq-hVTSYVl');
        $this->gateway->setTestMode(true); // here 'true' is for sandbox. Pass 'false' when go live
    }

    public function makeCourseBooking(Request $request)
    {

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

        $arr_expiry = explode("/", $request->input('expiry'));

        $formData = array(
            'name' => $request->input('name'),
            'number' => $request->input('number'),
            'expiryMonth' => trim($arr_expiry[0]),
            'expiryYear' => trim($arr_expiry[1]),
            'cvv' => $request->input('cvv')
        );

        try {
            // Send purchase request
            $response = $this->gateway->purchase([
                'amount' => $course->price,
                'currency' => 'USD',
                'card' => $formData
            ])->send();

            // Process response
            if ($response->isSuccessful()) {

                // Payment was successful
                $arr_body = $response->getData();
                $amount = $arr_body['AMT'];
                $paypalmsg = $arr_body['ACK'];
                $currency = $arr_body['CURRENCYCODE'];
                $transaction_id = $arr_body['TRANSACTIONID'];

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
                    'transaction_id' => $transaction_id,
                ]);

                $message = "Your course ".$courseBooking->bookingCourse->title_en." has been Booked by " .$courseBooking->bookingStudent->first_name;
                $notification=notifiedWithEvent($courseBooking->trainer_id,auth()->user()->id,$message,'course_booking',$courseBooking->id);

                $message_event = "We have new Booking for course";
                $event_calender=saveEventCalender("course",$courseBooking->id,$message_event,auth()->user()->id);

                $responseurl = 'Your Transaction has been Successfull, Booking has been made!';
                return transactionResponse($transaction_id,$responseurl);
            } else {
                // Payment failed
                return ok('error',$response->getMessage());
            }
        } 
        catch(\Exception $e) {
            return transactionResponseError('INVALID_CARD_DETAIL');
        }
    }


    public function bookAfterPaypalSuccess(Request $request)
    {

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
                ]);

                $message = "Your course ".$courseBooking->bookingCourse->title_en." has been Booked by " .$courseBooking->bookingStudent->first_name;
                $notification=notifiedWithEvent($courseBooking->trainer_id,auth()->user()->id,$message,'course_booking',$courseBooking->id);

                $message_event = "We have new Booking for course";
                $event_calender=saveEventCalender("course",$courseBooking->id,$message_event,auth()->user()->id);

                $responseurl = 'Your Transaction has been Successfull, Booking has been made!';
                return transactionResponse("paypal success",$responseurl);

    }


    public function makeCourseBookingByOffer(Request $request,$id)
    {
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

        $special_offer=SpecialOffer::find($id);
        if(!isset($special_offer)){
            return error("Offer not found",404);
        }

        $course = Course::find($request->course_id);
        if(!isset($course)){
            return error("Course not found",404);
        }

        $arr_expiry = explode("/", $request->input('expiry'));

        $formData = array(
            'name' => $request->input('name'),
            'number' => $request->input('number'),
            'expiryMonth' => trim($arr_expiry[0]),
            'expiryYear' => trim($arr_expiry[1]),
            'cvv' => $request->input('cvv')
        );

        try {
            // Send purchase request
            $response = $this->gateway->purchase([
                'amount' => $special_offer->after_price,
                'currency' => 'USD',
                'card' => $formData
            ])->send();

        // Process response
            if ($response->isSuccessful()) {

            // Payment was successful
            $arr_body = $response->getData();
            $amount = $arr_body['AMT'];
            $paypalmsg = $arr_body['ACK'];
            $currency = $arr_body['CURRENCYCODE'];
            $transaction_id = $arr_body['TRANSACTIONID'];

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
                'price' => $special_offer->after_price,
                'payment_mode' => $request->payment_mode,
                'transaction_id' => $transaction_id,
            ]);

            $message = "Your course ".$courseBooking->bookingCourse->title_en." has been Booked by " .$courseBooking->bookingStudent->first_name;
            $notification=notifiedWithEvent($courseBooking->trainer_id,auth()->user()->id,$message,'course_booking',$courseBooking->id);


            $message_event = "We have new Booking for course";
            $event_calender=saveEventCalender("course",$courseBooking->id,$message_event,auth()->user()->id);

            $responseurl = 'Your Transaction has been Successfull, Booking has been made!';
            return transactionResponse($transaction_id,$responseurl);
            } else {
                // Payment failed
                return ok('error',$response->getMessage());
            }
            } 
            catch(\Exception $e) {
                //return error('error', $e->getMessage());
                return transactionResponseError('INVALID_CARD_DETAIL');
            }
    }


    public function bookOfferAfterPaypalSuccess(Request $request,$id)
    {
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

        $special_offer=SpecialOffer::find($id);
        if(!isset($special_offer)){
            return error("Offer not found",404);
        }

        $course = Course::find($request->course_id);
        if(!isset($course)){
            return error("Course not found",404);
        }


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
                'price' => $special_offer->after_price,
                'payment_mode' => $request->payment_mode,
            ]);

            $message = "Your course ".$courseBooking->bookingCourse->title_en." has been Booked by " .$courseBooking->bookingStudent->first_name;
            $notification=notifiedWithEvent($courseBooking->trainer_id,auth()->user()->id,$message,'course_booking',$courseBooking->id);


            $message_event = "We have new Booking for course";
            $event_calender=saveEventCalender("course",$courseBooking->id,$message_event,auth()->user()->id);

            $responseurl = 'Offer Availed, Transaction has been Successfull, Booking has been made!';
            return transactionResponse("paypal success",$responseurl);
            
    }

    public function myBooking()
    {
        $bookedCourses=BookCourse::with('courseTrainer:id,first_name,last_name,email','bookingCourse')
                    ->where(['student_id'=>auth()->user()->id])->get();

        $collection = $bookedCourses->map(function ($item) {
            if($item->status===0){
                $item->status = "pending";
            }
            if($item->status===1){
                $item->status = "booked";
            }  
            if($item->status===2){
                $item->status = "completed";
            }       
            if($item->status===3){
                $item->status = "cancelled";
            }   
            return $item;
        });
        return ok($collection);
    }

    public function myBookingById($id)
    {
        $bookedCourses=BookCourse::with('courseTrainer:id,first_name,last_name,email',
        'bookingCourse',
        'bookingStudent:id,first_name,last_name,email')
                    ->find($id);

            if($bookedCourses->status===0){
                $bookedCourses->status = "pending";
            }
            if($bookedCourses->status===1){
                $bookedCourses->status = "booked";
            }  
            if($bookedCourses->status===2){
                $bookedCourses->status = "completed";
            }       
            if($bookedCourses->status===3){
                $bookedCourses->status = "cancelled";
            }   
        
        return ok($bookedCourses);
    }

    public function viewStudentBooking()
    {
        $bookedCourses=BookCourse::with('bookingStudent:id,first_name,last_name,email','bookingCourse')
                    ->where(['trainer_id'=>auth()->user()->id])->get();

        $collection = $bookedCourses->map(function ($item) {
            if($item->status===0){
                $item->status = "pending";
            }
            if($item->status===1){
                $item->status = "booked";
            }  
            if($item->status===2){
                $item->status = "completed";
            }       
            if($item->status===3){
                $item->status = "cancelled";
            }   
            return $item;
        });
        return ok($collection);
    }

    public function getStudentList()
    {
        $bookedCourses=BookCourse::with('bookingStudent:id,first_name,last_name,email')
                    ->where(['trainer_id'=>auth()->user()->id])->get();

        $check=collect();
        $list=[];

        $collection = $bookedCourses->map(function($item) use($check) {
            $student = User::find($item->student_id);
            if(!$check->contains($student)){
                $check[]=$student;
            }
            return $student;
        });

        $collection1 = $check->map(function($item1) use ($list) {
            $list['id'] = $item1['id'];
            $list['first_name'] = $item1['first_name'];
            $list['last_name'] = $item1['last_name'];
            $list['email'] = $item1['email'];
            return $list;
        });
        return ok($collection1);
    }

    public function getTrainerList()
    {
        $bookedCourses=BookCourse::with('courseTrainer:id,first_name,last_name,email')
                    ->where(['student_id'=>auth()->user()->id])->get();

        $check=collect();
        $list=[];

        $collection = $bookedCourses->map(function($item) use($check) {
            $student = User::find($item->trainer_id);
            if(!$check->contains($student)){
                $check[]=$student;
            }
            return $student;
        });

        $collection1 = $check->map(function($item1) use ($list) {
            $list['id'] = $item1['id'];
            $list['first_name'] = $item1['first_name'];
            $list['last_name'] = $item1['last_name'];
            $list['email'] = $item1['email'];
            return $list;
        });
        return ok($collection1);
    }

    public function changeBookingStatus(Request $request, $id)
    {
        $this->validate($request, [
            'status' => 'required|numeric',
        ]);

        $bookcourse = BookCourse::find($id);
        if(!isset($bookcourse)){
            return response()->json(["message"=>"Booking not found"],404);
        }
        $requestData = $request->all();
        $bookcourse->update($requestData);

        if($request->status==1){
            $message = "Your booking for ".$bookcourse->bookingCourse->title_en." has been Approved";
            $notification=notifiedWithEvent($bookcourse->student_id,auth()->user()->id,$message,'course_booking',$bookcourse->id);
        }
        if($request->status==2){
            $message = "Your booking for ".$bookcourse->bookingCourse->title_en." has been Completed";
            $notification=notifiedWithEvent($bookcourse->student_id,auth()->user()->id,$message,'course_booking',$bookcourse->id);
        }
        if($request->status==3){
            $message = "Your booking for ".$bookcourse->bookingCourse->title_en." has been Cancelled";
            $notification=notifiedWithEvent($bookcourse->student_id,auth()->user()->id,$message,'course_booking',$bookcourse->id);
        }

        return response()->json(["message"=>"Booking status has been updated"],201);
    }


    public function scheduleBooking(Request $request, $id)
    {
        $this->validate($request, [
            'time' => 'required',
            'date' => 'required',
        ]);

        $bookcourse = BookCourse::find($id);
        if(!isset($bookcourse)){
            return error("Booking not found");
        }
        $requestData = $request->all();
        $bookcourse->update($requestData);
        $message = "Your booking for ".$bookcourse->bookingCourse->title_en." has been scheduled to ".$bookcourse->time. " ".$bookcourse->date;
        $notification=notifiedWithEvent($bookcourse->student_id,auth()->user()->id,$message,'course_booking',$bookcourse->id);
          
        return ok("Booking has been scheduled successfully");
    }


    public function getAllBookingByAdmin()
    {
        $bookedCourses=BookCourse::with(
            'courseTrainer:id,first_name,last_name,email',
            'bookingStudent:id,first_name,last_name,email',
            'bookingCourse:id,title_en,title_ar')
        ->get();

        $collection = $bookedCourses->map(function ($item) {
            if($item->status===0){
                $item->status = "pending";
            }
            if($item->status===1){
                $item->status = "booked";
            }  
            if($item->status===2){
                $item->status = "completed";
            }       
            if($item->status===3){
                $item->status = "cancelled";
            }   
            return $item;
        });
        return ok($collection);
    }

    public function filterByAdmin(Request $request)
    {

       // if($request->date_from){
        $bookedCourses=BookCourse::with('courseTrainer:id,first_name,last_name,email',
            'bookingCourse:id,title_en,title_ar',
            'bookingStudent:id,first_name,last_name,email')
            ->join('users', 'book_courses.trainer_id', '=', 'users.id')
            ->where('users.first_name', 'like', "%" . $request->trainer . "%")
            // ->where('book_courses.date','>=', $request->date_from)
            //  ->where('book_courses.date', '<=', $request->date_to)   
            ->where(function($q) use ($request) {
                if ($request->date_from) {
                     $q->where('book_courses.date','>=', $request->date_from)
                     ->where('book_courses.date', '<=', $request->date_to);  
                }
            })
            ->join('users as s', 'book_courses.student_id', '=', 's.id')
            ->where('s.first_name', 'like', "%" . $request->student . "%")
            ->select('book_courses.*')
            ->get();
        //}
        //else{
        // $bookedCourses=BookCourse::with('courseTrainer:id,first_name,last_name,email',
        //         'bookingCourse:id,title_en,title_ar',
        //         'bookingStudent:id,first_name,last_name,email')
        //         ->join('users', 'book_courses.trainer_id', '=', 'users.id')
        //         ->where('users.first_name', 'like', "%" . $request->trainer . "%")          
        //         ->select('book_courses.*')
        //         ->get();
        // }

        $collection = $bookedCourses->map(function ($item) use ($request) {
            if($item->status===0){
                $item->status = "pending";
            }
            if($item->status===1){
                $item->status = "booked";
            }  
            if($item->status===2){
                $item->status = "completed";
            }       
            if($item->status===3){
                $item->status = "cancelled";
            }   
            return $item;
        });
        return ok($collection);
    }

}
