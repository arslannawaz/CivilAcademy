<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\EventCalender;
use App\BookCourse;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EventCalenderController extends Controller
{
    public function getAllEvents()
    {
        $date=EventCalender::select('created_at')->get();
        $check=collect();
        $event=[];

        $collection = $date->map(function($item) use($check) {
            $item = $item->created_at->toDateString();
            if(!$check->contains($item)){
                $check[]=$item;
            }
            return $item;
        });
        
        $collection1 = $check->map(function($item1) use ($event) {
            $event_calender = EventCalender::with('addedBy')->whereDate('created_at',$item1)->get();
            $event['date'] = $item1;
            $event['event'] = $event_calender;
            return $event;
        });
        
        return ok($collection1);
    }

    public function getEventDetail($id)
    {
        $event_calender=EventCalender::find($id);
        if(!isset($event_calender)){
            return error("No event found!");
        }

        if($event_calender->event_title=='course'){
            $bookedCourses=BookCourse::with('courseTrainer:id,first_name,last_name,email',
            'bookingCourse:id,title_en',
            'bookingStudent:id,first_name,last_name,email')->find($event_calender->event_id);

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
            return error("something went wrong!");
        }
        return error("something went wrong!");
    }
}

