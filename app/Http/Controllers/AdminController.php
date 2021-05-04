<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\Category;
use App\Course;
use App\Document;
use App\Remarks;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function dashboard(){
        $categories=Category::get()->count();
        $users=User::get()->count();
        $courses=Course::get()->count();

        return response()->json([
            "message"=>"Success",
            "total_categories"=>$categories,
            "total_users"=>$users,
            "total_course"=>$courses,
        ],201);
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
                    <b>Password:</b>
                </td>
                <td style="padding:10px;">'.$request->password.'</td>
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
        //Mail::to($data->email)->send(new SendMail($data));
        //return view('email',compact('request'));

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
                                                    <a href="https://practical-liskov-124637.netlify.app" target="_blank" style="font-size: 20px; font-family: Helvetica, Arial, sans-serif; color: #ffffff; text-decoration: none; color: #ffffff; text-decoration: none; padding: 15px 25px; border-radius: 2px; border: 1px solid #FFA73B; display: inline-block;">Login</a></td>
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

    public function createAdminUser(Request $request)
    {
        $this->validate($request, [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|numeric|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
        ]);

        $img_name ='https://www.w3schools.com/howto/img_avatar.png';
        if($request->image){
            $img_name=$request->image;
        }

        $random = str_shuffle('abcdefghjklmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ234567890!$%^&!$%^&');
        $user_password = substr($random, 0, 10);

        $requestData=$request->except('password');
        $requestData['role']='user';
        $requestData['image']=$img_name;
        $requestData['password']=bcrypt($user_password);
        $user = User::create($requestData);

        $request['password']=$user_password;
        // $this->sendMailToCivil($request);
        // $this->sendMailToUser($request);
        return ok("Admin User has been created");
    }

    public function listUser(){
        $users=User::where('role','user')->get();
        return ok($users);
    }

    public function findUser($id){
        $users=User::find($id);
        if(!isset($users)){
            return response()->json(["message"=>"user not found"],404);
        }
        return ok($users);
    }

    public function toggleStatus($id)
    {
        $users=User::find($id);
        if(!isset($users)){
            return response()->json(["message"=>"user not found"],404);
        }
        $users->status = !$users->status;
        $users->save();
        return ok('User status has been updated');
    }

    public function updateUserPassword(Request $request,$id)
    {
        $user=User::find($id);
        if(!isset($user)){
            return response()->json(["message"=>"user not found"],404);
        }

        $this->validate($request, [
            'current_password' => 'required|string',
            'password' => 'required|string|min:6',
            'c_password'=>'required|same:password',
        ]);

        if(Hash::check($request->current_password,$user->password)) {
            $user->password = bcrypt($request->password);
            $user->save();

            $data=collect();
            $data->email=$user->email;
            $data->password=$request->password;
            $data->phone=$user->phone;
            $data->first_name=$user->first_name;
            $data->last_name=$user->last_name;
            //$this->sendMailToCivil($data);
            //$this->sendMailToUser($data);
            return response()->json(["message"=>"Password changed successfully!"],201);
        } else {
            return response()->json(["message"=>"password do not match"],404);
        }   
    }

    public function deleteUser($id)
    {
        $users=User::find($id);
        if(!isset($users)){
            return response()->json(["message"=>"user not found"],404);
        }
        $users->delete();
        return ok('User has been deleted successfully!');
    }

    public function updateUser(Request $request,$id)
    {
        $user=User::find($id);
        if(!isset($user)){
            return response()->json(["message"=>"user not found"],404);
        }        
        
        $this->validate($request, [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required','email','string','max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => [
                'required','string',
                Rule::unique('users')->ignore($user->id),
            ],
        ]);

        $requestData = $request->all();
        if($request->image){
            $requestData['image']=$request->image;
        }
        $user->update($requestData);
        return ok(["message"=>"User updated successfully!"]);
    }

    public function filter(Request $request){
    
        $users = User::where('role', '=',  'user');
        if($request->email):
            $users=$users->where('email', "like", "%" . $request->email . "%");
        endif;

        if($request->status=='1'):
            $users=$users->where('status','=',1);
        endif;

        if($request->status=='0'):
            $users=$users->where('status','=',0);
        endif;

        if($request->date_from): 
            $users->whereDate('created_at','>=', $request->date_from)
            ->whereDate('created_at', '<=', $request->date_to); 
        endif;

        if($request->name_phone):
            $value=$request->name_phone;
            $users=$users->where('role', '=',  'user')
            ->where(function($query) use ($value) {
                    $query->where('first_name', "like", "%" . $value . "%");
                    $query->orWhere('phone', 'LIKE', "%".$value."%");
            });
        endif;

        // ->where(function ($query) use ($request) {
        //     $query->where('first_name', "like", "%" . $request->name_phone . "%");
        //     $query->orWhere('phone', "like", "%" . $request->name_phone . "%");
        //     $query->orWhere('email', "like", "%" . $request->email . "%");
        // })->get(); 

        return ok($users->get());
    }

    public function saveDocument(Request $request)
    {
        $this->validate($request, [
            'url' => 'required',
            'title' => 'required',
        ]);
        $document=Document::create($request->all());
        return ok('Document Uploaded Successfully!');
    }

    public function getAllDocument()
    {
        $document=Document::all();
        return ok($document);
    }

    public function addRemark(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required',
            'remarks' => 'required',
        ]);

        $user = User::find($request->user_id);
        if(!isset($user)){
            return error("user not found",404);
        }

        $remark=Remarks::create($request->all());

        $message = "Remarks Added: ".$remark->remarks;
        $notification=notifiedWithEvent($remark->user_id,auth()->user()->id,$message,'remark',$remark->id);

        return ok('Remarks Added Successfully!');
    }

}
