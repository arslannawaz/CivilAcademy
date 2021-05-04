<?php

namespace App\Http\Controllers;

use App\Notification;
use App\BookCourse;
use App\Course;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function notifyStudent()
    {
        return ok(Notification::with('toUser','fromUser')->where('to',auth()->user()->id)->orderBy('id','DESC')->get());
    }

    public function notifyTrainer()
    {
        return ok(Notification::with('toUser','fromUser')->where('to',auth()->user()->id)->orderBy('id','DESC')->get());
    }

    public function notifyAdmin()
    {
        return ok(Notification::with('toUser','fromUser')->where('to',auth()->user()->id)->orderBy('id','DESC')->get());
    }

    public function getNotificationDetail($id)
    {
        $notification=Notification::find($id);
        if(!isset($notification)){
            return error("No notification found!");
        }

        if($notification->event=='course_booking'){
            $bookedCourses=BookCourse::with('courseTrainer:id,first_name,last_name,email',
            'bookingCourse:id,title_en',
            'bookingStudent:id,first_name,last_name,email')->find($notification->event_id);

            if(isset($bookedCourses)){            
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
            return error("no data found!");
        }

        if($notification->event=='course'){
            $course = Course::find($id);
            if(!isset($course)){
                return error("no data found",404);
            }
            $course->images;
            $course->locations;
            $course->reviews;
            $course->coursecategory;
            $course->organizedBy;
            return ok($course);
        }

        return error("no data found!");
    }

    public function filter(Request $request){
    
        $notifition = Notification::with('toUser','fromUser')
        ->where('to',auth()->user()->id)
        ->where('message', "like", "%" . $request->title . "%");
        if($request->status=='1'):
            $notifition=$notifition->where('read','=',1);
        endif;

        if($request->status=='0'):
            $notifition=$notifition->where('read','=',0);
        endif;
        return ok($notifition->get());
    }
}
