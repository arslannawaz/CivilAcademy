<?php

namespace App\Http\Controllers;

use App\Mail\SendMail;
use Illuminate\Http\Request;
use App\User;
use App\StudentCourse;
use App\Trainer;
use App\Course;
use App\CourseImage;
use App\CourseLocation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Image;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
class AuthController extends UserController
{
    //
    public function index()
    {
        $users = User::all();
        return ok($users);
    }

    public function register(Request $request)
    {

        $this->validate($request, [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|numeric|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $img_name ='https://www.w3schools.com/howto/img_avatar.png';

        $requestData=$request->except('password');
        $requestData['role']='user';
        $requestData['image']=$img_name;
        $requestData['password']=bcrypt($request->password);
        $user = User::create($requestData);

        //$this->sendMail($request);
       return  ok("Admin User has been created");
    }

    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);
        if (!$token = auth()->attempt($credentials)) {
            return error("invalid username or password");
        }
        $user = auth()->user();
        if($user->status==1){
            return ok(['access_token'=>$token ,'user'=>$user]);
        }
        return error("Sorry! Your account is inactive");
    }

    public function loginWithFb(Request $request)
    {
        $this->validate($request, [
            'fb_email' => 'required|string',
            'fb_token' => 'required|string',
        ]);

        $user=User::where('email',$request->fb_email)->first();
        if(!($user)){
            return error('Email Address do not exist!');
        }

        if (!$token = Auth::login($user)) {
            return error("invalid login attempt");
        }
        $user = auth()->user();
        return ok(['access_token'=>$token ,'user'=>$user]);
    }


    public function logout() {
        Auth::guard('api')->logout();
        return ok(['message'=>'Successfully logged out']);
    }

    public function getUser() {
        $user=auth()->user();
        return ok($user);         
    }

    public function show($id)
    {
        $user = User::find($id);
        return ok($user);
    }


    public function edit($id)
    {
        $user = User::find($id);
        return ok($user);
    }

    public function mailSend($request)
    { 
        Mail::to($request->email)->send(new SendMail($request));
        return view('contact',compact('request'));   
    }

    public function sendMailToCivil($request)
    {
        //Mail::to($data->email)->send(new SendMail($data));
        //return view('email',compact('request'));

        $mailheader = "MIME-Version: 1.0" . "\r\n";
        $mailheader .= "From: ".$request->email."\r\n";
        $mailheader .= "Reply-To: ".$request->email."\r\n";
        $mailheader .= "Content-type: text/html; charset=iso-8859-1\r\n" .
            "X-Mailer: PHP/" . phpversion();
        $ToEmail = 'info@mazcoaching.com';
        $EmailSubject = 'New User Account Details';
        $MESSAGE_BODY = '<table style=" background:#F4F4F4 ; text-align : center">

            <tr>
                <th colspan="2" style="padding:10px;">
                    <b>New User Account Details</b>
                </th>
            </tr>
            <tr>
                <td style="padding:10px;">
                    <b>Name:</b>
                </td>
                <td style="padding:10px;">'.$request->first_name.' '.$request->last_name.'</td>
            </tr>
            <tr>
                <td style="padding:10px;">
                    <b>Email:</b>
                </td>
                <td style="padding:10px;">'.$request->email.'</td>
            </tr>
            <tr>
                <td style="padding:10px;">
                    <b>Contact:</b>
                </td>
                <td style="padding:10px;">'.$request->phone.'</td>
            </tr>
        </table>';
        mail($ToEmail, $EmailSubject, $MESSAGE_BODY, $mailheader);
    }


    public function sendMailToUser($request)
    {
        $mailheader = "MIME-Version: 1.0" . "\r\n";
        $mailheader .= "From: info@mazcoaching.com\r\n";
        $mailheader .= "Reply-To: info@mazcoaching.com\r\n";
       // $mailheader .= "Return-Path: <info@mazcoaching.com>\r\n"; 
        $mailheader .= "Content-type: text/html; charset=iso-8859-1\r\n" .
            "X-Mailer: PHP/" . phpversion();

        $ToEmail = $request->email;
        $EmailSubject = 'Reply From Civil Academy';
        $MESSAGE_BODY = '<body style="background-color: #f4f4f4; margin: 0 !important; padding: 0 !important;">
        
        <div style="display: none; font-size: 1px; color: #fefefe; line-height: 1px; font-family: Lato, Helvetica, Arial, sans-serif; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden;"> We are thrilled to have you here! Get ready to dive into your new account. </div>


        <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td bgcolor="#FFA73B" align="center">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
                    <tr>
                        <td align="center" valign="top" style="padding: 40px 10px 40px 10px;"> </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td bgcolor="#FFA73B" align="center" style="padding: 0px 10px 0px 10px;">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
                    <tr>
                        <td bgcolor="#ffffff" align="center" valign="top" style="padding: 40px 20px 20px 20px; border-radius: 4px 4px 0px 0px; color: #111111; font-family: Lato, Helvetica, Arial, sans-serif; font-size: 48px; font-weight: 400; letter-spacing: 4px; line-height: 48px;">
                            <h1 style="font-size: 48px; font-weight: 400; margin: 2;">Welcome!</h1> <img src=" https://img.icons8.com/clouds/100/000000/handshake.png" width="125" height="120" style="display: block; border: 0px;" />
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td bgcolor="#f4f4f4" align="center" style="padding: 0px 10px 0px 10px;">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
                    <tr>
                        <td bgcolor="#ffffff" align="left" style="padding: 20px 30px 40px 30px; color: #666666; font-family: Lato, Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
                        <p style="margin: 0;">We are excited to have you get started. First, you need to login your account with username: <strong>'.$request->email.'</strong>

                        password: <strong>'.$request->password.'</strong>

                        </p>
                        </td>
                    </tr>
                    <tr>
                        <td bgcolor="#ffffff" align="left">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td bgcolor="#ffffff" align="center" style="padding: 20px 30px 60px 30px;">
                                        <table border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td align="center" style="border-radius: 3px;" bgcolor="#FFA73B">
                                                    <a href="https://civil-admin.web.app/login" target="_blank" style="font-size: 20px; font-family: Helvetica, Arial, sans-serif; color: #ffffff; text-decoration: none; color: #ffffff; text-decoration: none; padding: 15px 25px; border-radius: 2px; border: 1px solid #FFA73B; display: inline-block;">Login</a></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td bgcolor="#f4f4f4" align="center" style="padding: 30px 10px 0px 10px;">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
                    <tr>
                        <td bgcolor="#FFECD1" align="center" style="padding: 30px 30px 30px 30px; border-radius: 4px 4px 4px 4px; color: #666666; font-family: Lato, Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
                            <h2 style="font-size: 20px; font-weight: 400; color: #111111; margin: 0;">Need more help?</h2>
                            <p style="margin: 0;"><a href="#" target="_blank" style="color: #FFA73B;">We&rsquo;re here to help you out</a></p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

    </table>
        </body>';
        mail($ToEmail, $EmailSubject, $MESSAGE_BODY, $mailheader);
    }

    public function update(Request $request , $id)
    {

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'phone' => 'required|numeric',
            'email' => 'required|string|email|max:255',
        ]);

        if($validator->fails()){
                return response()->json($validator->errors()->toJson(), 400);
            }

        $user = User::find($id);

        $img_name = $user->image;

        if($request->hasFile('image'))
        {
            deleteImage('/uploads/img/users/'.$user->image);
            $img_name = saveImage($request->image , '/uploads/img/users/');
        }


        // $user->image = $img_name;
        // $user->first_name = $request->first_name;
        // $user->first_name = $request->first_name;
        // $user->phone = $request->phone;
        // $user->country_id = $request->country_id;
        // $user->email = $request->email;

        $user->update([
            'image' =>$img_name,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'country' => $request->country,
            'email' => $request->email,
        ]);

    //   $this->sendMail($request);

    //   $token = auth()->login($user);
      return ok('record updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        // return $request;
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string|max:255',
            'password' => 'required|string',
            // 'password_confirmation' => 'required|string|confirmed',
        ]);

        if($validator->fails()){
                return response()->json($validator->errors()->toJson(), 400);
            }

        if (!(Hash::check($request->get('current_password'), Auth::user()->password))) {
            // The passwords matches
            return error("Your current password does not matches with the password you provided. Please try again.");
        }

        if(strcmp($request->get('current_password'), $request->get('password')) == 0){
            //Current password and new password are same
            return error("New Password cannot be same as your current password. Please choose a different password.");
        }



        //Change Password

        $user = Auth::user();
        $user->password = bcrypt($request->password);
        $user->save();

        return ok("Password changed successfully !");
    }


    public function registerStudent(Request $request)
    {
        $this->validate($request, [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|numeric|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'c_password'=>'required|same:password',
        ]);


        //$img_name ='/uploads/img/users/default.png';

        // if($request->hasFile('image'))
        // {
        //     $img_name = saveImage($request->image , '/uploads/img/users/');
        // }

        $img_name ='https://www.w3schools.com/howto/img_avatar.png';
        if($request->image){
            $img_name=$request->image;
        }

        $user = User::create([
            'image' =>$img_name,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'role' => 'student',
            'phone' => $request->phone,
            'gender' => $request->gender,
            'country' => $request->country,
            'email' => $request->email,
            'dob' => $request->dob,
            'nationality' => $request->nationality,
            'password' => bcrypt($request->password),
        ]);

        // $courseid = explode(',',$request->course_id);
        // $cartype  = explode(',',$request->car_type);
        // $driverlevel = explode(',',$request->driver_level);
        // for ($i=0; $i<count($cartype); $i++){
        //     StudentCourse::create([
        //         'course_id'=>$courseid[$i],
        //         'student_id'=> $user->id,
        //         'course_learning'=>$cartype[$i],
        //         'test_type'=>$driverlevel[$i],
        //     ]);
        // }

        if($request->course_id){
                StudentCourse::create([
                    'course_id'=>$request->course_id,
                    'student_id'=> $user->id,
                    'course_learning'=>$request->car_type,
                    'test_type'=>$request->driver_level,
                ]);
        }

        //$this->sendMailToCivil($request);
        //$this->sendMailToUser($request);
        //$this->mailSend($request);
        return ok('Student Created Successfully');
    }


    public function registerTrainer(Request $request)
    {
        $this->validate($request, [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|numeric|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'c_password'=>'required|same:password',
            'trade_license' => 'required',
            'certificate' => 'required',
            'trn_certificate' => 'required',
            'emirates_id' =>'required',
        ]);

        //$img_name ='/uploads/img/users/default.png';

        // if($request->hasFile('company_logo'))
        // {
        //     $img_name = saveImage($request->company_logo , '/uploads/img/users/');
        // }

        $img_name ='https://www.w3schools.com/howto/img_avatar.png';
        if($request->company_logo){
            $img_name=$request->company_logo;
        }

        $user = User::create([
            'image' =>$img_name,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'role' => 'trainer',
            'phone' => $request->phone,
            'country' => $request->country,
            'email' => $request->email,
            'dob' => $request->dob,
            'nationality' => $request->nationality,
            'password' => bcrypt($request->password),
        ]);
        
        // $img_trade_license = saveImage($request->trade_license , '/uploads/img/users/');
        // $img_certificates = saveImage($request->certificate , '/uploads/img/users/');
        // $img_trn_certificate = saveImage($request->trn_certificate , '/uploads/img/users/');
        // $img_emirates_id = saveImage($request->emirates_id , '/uploads/img/users/');

        $trainer = Trainer::create([
            'user_id' => $user->id,
            'organization' => $request->company_name,
            'experience' => $request->experience,
            'trade_licence' => $request->trade_license,
            'certificates' => $request->certificate,
            'trn_certificate' => $request->trn_certificate,
            'emirate_id' => $request->emirates_id,
        ]);

        if($request->course_name){
            for ($i=0; $i<count($request->course_name); $i++){

                $course_locations = $request->locations[$i];

                // $img_name ='/uploads/img/courses/logo/default.png';
                // if($request->hasFile('course_logo'))
                // {
                //     $img_name = saveImage($request->logo[$i] , '/uploads/img/courses/logo/');
                // }

                $course = Course::create([
                    'title_en' => $request->course_name[$i],
                    'category_id' => 1,
                    'organized_by' => $user->id,
                    'status' => 0,
                    'logo' => $request->course_logo[$i],
                ]);

                $c_img = CourseImage::create([
                    'course_id'=>$course->id,
                    'image'=>$request->course_logo[$i]
                ]);

                if($course_locations != null &&  $course_locations != '' && count($course_locations) > 0 )
                {
                        CourseLocation::create([
                            'course_id'=>$course->id,
                            'country'=> $course_locations['country'],
                            'address'=> $course_locations['address'],
                        ]);
                }
            }
        }

        //$this->sendMailToCivil($request);
        //$this->sendMailToUser($request);
        return ok('Trainer Created Successfully');
    }

}
