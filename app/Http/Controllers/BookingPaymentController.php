<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BookCourse;
use App\BookingPayment;
use Illuminate\Support\Facades\Auth;

class BookingPaymentController extends Controller
{
    

    public function myBookingPayment()
    {
        $bookedCourses=BookCourse::with('bookingCourse:id,title_en,title_ar',
            'courseTrainer:id,first_name,last_name,email')
            ->where(['student_id'=>auth()->user()->id])->get();

        $collection = $bookedCourses->map(function ($item) {
            $payment=BookingPayment::where('booking_id',$item->id)->first();
            if($payment->status===0){
                $payment->status = "pending";
            }
            if($payment->status===1){
                $payment->status = "completed";
            }         
            if($payment->status===3){
                $payment->status = "cancelled";
            }
            $item->transaction_history=$payment;        
            return $item;
        });
        return ok($collection);
    }

    public function myBookingPaymentByBookingId($id)
    {
        $bookedCourses=BookCourse::with('bookingCourse:id,title_en,title_ar',
            'courseTrainer:id,first_name,last_name,email',
            'bookingStudent:id,first_name,last_name,email')
            ->find($id);

            $payment=BookingPayment::where('booking_id',$id)->first();
            if($payment->status===0){
                $payment->status = "pending";
            }
            if($payment->status===1){
                $payment->status = "completed";
            }         
            if($payment->status===3){
                $payment->status = "cancelled";
            }
            $bookedCourses['payment']=$payment;
        return ok($bookedCourses);
    }

    public function myStudentBookingPayment()
    {
        $bookedCourses=BookCourse::with('bookingStudent:id,first_name,last_name,email','bookingCourse:id,title_en,title_ar')
                    ->where(['trainer_id'=>auth()->user()->id])->get();

        $collection = $bookedCourses->map(function ($item) {
            $payment=BookingPayment::where('booking_id',$item->id)->first();
            if($payment->status===0){
                $payment->status = "pending";
            }
            if($payment->status===1){
                $payment->status = "completed";
            }         
            if($payment->status===3){
                $payment->status = "cancelled";
            }
            $item->transaction_history=$payment;        
            return $item;
        });
        return ok($collection);
    }

    public function getTransactionsListByAdmin()
    {
        $bookedCourses=BookCourse::with('bookingStudent:id,first_name,last_name,email',
        'bookingCourse:id,title_en,title_ar',
        'courseTrainer:id,first_name,last_name,email'
        )->get();

        $collection = $bookedCourses->map(function ($item) {
            $payment=BookingPayment::where('booking_id',$item->id)->first();
            if($payment->status===0){
                $payment->status = "pending";
            }
            if($payment->status===1){
                $payment->status = "completed";
            }         
            if($payment->status===3){
                $payment->status = "cancelled";
            }
            $item->transaction_history=$payment;        
            return $item;
        });
        return ok($collection);
    }

    public function changeTransactionStatus(Request $request, $id)
    {
        $this->validate($request, [
            'status' => 'required|numeric',
        ]);

        $transaction = BookingPayment::find($id);
        if(!isset($transaction)){
            return error(["message"=>"Transaction not found"]);
        }
        $requestData = $request->all();
        $transaction->update($requestData);

        $bookcourse=BookCourse::find($transaction->booking_id);

        if($request->status==0){
            $message = "Your transaction for ".$bookcourse->bookingCourse->title_en." has been set to Pending";
            $notification=notified($bookcourse->student_id,auth()->user()->id,$message);
        }
        if($request->status==1){
            $message = "Your transaction for ".$bookcourse->bookingCourse->title_en." has been Completed";
            $notification=notified($bookcourse->student_id,auth()->user()->id,$message);
        }
        if($request->status==3){
            $message = "Your transaction for ".$bookcourse->bookingCourse->title_en." has been Cancelled";
            $notification=notified($bookcourse->student_id,auth()->user()->id,$message);
        }

        return ok(["message"=>"Transaction status has been updated"]);
    }


    public function filterByAdmin(Request $request)
    {
        
        $bookedCourses=BookCourse::with('bookingStudent:id,first_name,last_name,email',
        'bookingCourse:id,title_en,title_ar',
        'courseTrainer:id,first_name,last_name,email','bookingPayment')
        
        ->join('users', 'book_courses.trainer_id', '=', 'users.id')
            ->where('users.first_name', 'like', "%" . $request->trainer . "%")  
            ->join('users as s', 'book_courses.student_id', '=', 's.id')
            ->where('s.first_name', 'like', "%" . $request->student . "%")

            ->join('booking_payments', 'book_courses.id', '=', 'booking_payments.booking_id')
            ->where(function($q) use ($request) {
                if ($request->date_from) {
                     $q->where('booking_payments.created_at','>=', $request->date_from)
                     ->where('booking_payments.created_at', '<=', $request->date_to);  
                }

                if($request->status=='0'):
                    $q->where('booking_payments.status','=',0);
                endif;

                if($request->status=='1'):
                    $q->where('booking_payments.status','=',1);
                endif;

                if($request->status=='3'):
                    $q->where('booking_payments.status','=',3);
                endif;
            })
            ->select('book_courses.*')
        ->get();

        $collection = $bookedCourses->map(function ($item) use($request) {
            $payment=BookingPayment::where('booking_id',$item->id)->first();
            
                if($payment->status===0){
                    $payment->status = "pending";
                }
                if($payment->status===1){
                    $payment->status = "completed";
                }         
                if($payment->status===3){
                    $payment->status = "cancelled";
                }
            
            $item->transaction_history=$payment;
            return $item;
        });
        return ok($collection);

    }

}
