<?php

// use Image;
use Illuminate\Support\Facades\File;
use \Illuminate\Http\Response as Res;

// header('Access-Control-Allow-Origin: *');
// header('Access-Control-Allow-Methods: *');
// header('Access-Control-Allow-Headers: *');

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 1000");
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
header("Access-Control-Allow-Methods: PUT, POST, GET, OPTIONS, DELETE");

function saveImage($image , $path )
{
    $img_name = time().'.'.$image->extension();
    $destinationPath = public_path($path);
    $img = Image::make($image->path());
    $img->resize(300, 300, function ($constraint) {
        $constraint->aspectRatio();
    })->save($destinationPath.$img_name );
     $image->move($destinationPath, $img_name);
     return $img_name =  $path . $img_name;
}

function notified($to, $from, $message)
{
      $notification = App\Notification::create([
          'to' => $to,
          'from' => $from,
          'message' => $message,
      ]);
      return $notification;
}

function notifiedWithEvent($to, $from, $message, $event, $event_id)
{
      $notification = App\Notification::create([
          'to' => $to,
          'from' => $from,
          'message' => $message,
          'event_id' => $event_id,
          'event' => $event,
      ]);
      return $notification;
}


function saveEventCalender($event_title, $event_id, $event_des,$updated_by)
{
      $event_calender = App\EventCalender::create([
          'event_title' => $event_title,
          'event_id' => $event_id,
          'event_description' => $event_des,
          'updated_by' => $updated_by
      ]);
      return $event_calender;
}



function deleteImage($path)
{
    File::delete(public_path($path));
}



/*Response Helpers*/
function ok($data = false){
    if($data){
          return response()->json([
                'status' => 'success',
                'data'   => $data
          ]);
    }

    return response()->json([
          'status' => 'success'
    ]);
}

/*Response Helpers*/
function transactionResponse($transaction_id,$message){
            return response()->json([
                  'status' => 'success',
                  'message'   => $message,
                  'transaction_id'   => $transaction_id,
            ]);
}

/*Response Helpers*/
function transactionResponseError($message, $code=402){
      return response()->json([
            'status' => 'false',
            'message'   => $message,
            'code' => $code,
      ]);
}

function error($code = false){
    if($code){
          return response()->json([
                'status' => 'error',
                'code'   => $code
          ]);
    }

    return response()->json([
          'status' => 'error',
    ]);
}

?>



