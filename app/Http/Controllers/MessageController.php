<?php

namespace App\Http\Controllers;
use App\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function sendMessage(Request $request){

        $message=Message::create([
            'trainer_id' => $request->to,
            'message' => $request->message,
            'student_id' => auth()->user()->id,
            'sender' => auth()->user()->role,
        ]);

        $notification_message = "You received a message from ".auth()->user()->first_name;
        $notification=notified($request->to,auth()->user()->id,$notification_message);
        return ok($message);
    }

    public function getChat($trainer_id){
        $message=Message::where(['student_id'=>auth()->user()->id,'trainer_id'=>$trainer_id])->orderBy('id','DESC')->get();
        return ok($message);
    }

    public function sendMessageByTrainer(Request $request){

        $message=Message::create([
            'student_id' => $request->to,
            'message' => $request->message,
            'trainer_id' => auth()->user()->id,
            'sender' => auth()->user()->role,
        ]);

        $notification_message = "You received a message from ".auth()->user()->first_name;
        $notification=notified($request->to,auth()->user()->id,$notification_message);
        return ok($message);
    }

    public function getChatByTrainer($student_id){
        $message=Message::where(['trainer_id'=>auth()->user()->id,'student_id'=>$student_id])->orderBy('id','DESC')->get();
        return ok($message);
    }

}
