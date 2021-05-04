<?php

namespace App\Http\Controllers;

use App\Student;
use App\StudentCourse;
use App\User;
use App\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Exception;
class StudentController extends UserController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        return ok (User::where('role','student')->with(array("studentCourses"=>function($q){
            return $q->select('id','title_en','title_ar');
        }))->get());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validator = $this->validateData($request);
        if($validator->fails()){
                return error($validator->errors()->toJson());
            }

        $request->merge(['role' => 'student']);
        $user = $this->saveUser($request);
       StudentCourse::create([
           'course_id'=>$request->course_id,
           'student_id'=> $user->id,
           'course_learning'=>$request->course_learning,
           'test_type'=>$request->test_type,
           ]);

        return ok('Student Saved Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = User::where('role','student')->where('id',$id)->first();

        $data['student_course']= StudentCourse::where('student_id',$id)->first();

        return Ok($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function edit(Student $student)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Student  $student
     * @return \Illuminate\Http\Response
     */


    public function update(Request $request , $id)
    {

        try
        {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:255',
                'phone' => 'required|numeric',
                'course_learning' => 'required',
                'test_type' => 'required',
                'email' => 'required|string|email|max:255',
            ]);

            if($validator->fails()){
                    return error($validator->errors()->toJson());
                }

            $user= User::find($id);
            $img_name =$user->image;
            if($request->hasFile('image'))
            {
                if($request->image != '/uploads/img/students/default.png')
                deleteImage($request->image);
                $img_name = saveImage($request->image , '/uploads/img/students/');
            }


            $user->update([
                'image' =>$img_name,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'country' => $request->country,
                'gender' => $request->gender,
                'dob' => $request->dob,
            ]);



            $std_crc = StudentCourse::where('course_id',$request->course_id)->where('student_id',$id)->first();
            if($std_crc)
            {
                $std_crc->update([
                    'course_id'=>$request->course_id,
                    'student_id'=> $id,
                    'course_learning'=>$request->course_learning,
                    'test_type'=>$request->test_type,
                ]);
            }

            return ok('Student update Successfully');
        }
        catch(Exception $ex)
        {
            return error($ex);
        }

    }


    // update password
    public function updatePassword(Request $request)
    {

        // return $request;
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string|max:255',
            'password' => 'required|string',
            // 'password_confirmation' => 'required|string|confirmed',
        ]);

        // $student = Student::find();

        if($validator->fails()){
                return response()->json($validator->errors()->toJson(), 400);
            }


        $user = User::find($request->student_id);
        if($user)
        {

            if (!(Hash::check($request->get('current_password'), $user->password))) {
                // The passwords matches
                return error("Your current password does not matches with the password you provided. Please try again.");
            }

            if(strcmp($request->get('current_password'), $request->get('password')) == 0){
                //Current password and new password are same
                return error("New Password cannot be same as your current password. Please choose a different password.");
            }

            //Change Password
            // $user = Auth::user();
            $user->password = bcrypt($request->password);
            $user->save();

        }
        return ok("Password changed successfully !");
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //$std = Student::find($id);
        $user  = User::find($id);
        $user->delete();
        //$std->delete();
        return ok('student is remvoed ');
    }

    public function getProfile()
    {
        $user=auth()->user();
        //$user['student_course']= StudentCourse::where('student_id',$user->id)->get();
        $user->studentCourses;
        return Ok($user);
    }

    public function updatePass(Request $request)
    {
        $this->validate($request, [
            'current_password' => 'required|string',
            'password' => 'required|string|min:6',
            'c_password'=>'required|same:password',
        ]);

        $user=auth()->user();

        if(Hash::check($request->current_password,$user->password)) {
            $user->password = bcrypt($request->password);
            $user->save();
            return response()->json(["message"=>"Password changed successfully!"],201);
        } else {
            return response()->json(["message"=>"password do not match"],404);
        }   
    }

    public function updateProfile(Request $request)
    {

        $user=auth()->user();
        $this->validate($request, [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required','email','string','max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => [
                'required','numeric',
                Rule::unique('users')->ignore($user->id),
            ],
        ]);
        $requestData = $request->all();
        $user->update($requestData);
        return response()->json(["message"=>"Profile updated successfully!"],201);
    }

    public function updateStudentCourse(Request $request, $id)
    {
        $usercourse = StudentCourse::find($id);
        if(!isset($usercourse)){
            return response()->json(["message"=>"Student Course not found"],404);
        }
        $requestData = $request->all();
        $usercourse->update($requestData);
        return Ok($usercourse);
    }

    public function getStudentList()
    {
        $users=User::where('role','student')->get();
        return ok($users);
    }

    public function findStudentById($id)
    {
        $user=User::find($id);
        if(!isset($user)){
            return response()->json(["message"=>"user not found"],404);
        }
        $user->studentCourses;
        return Ok($user);
    }

    public function updateStudentProfileByAdmin(Request $request,$id)
    {
        $user=User::find($id);
        if(!isset($user)){
            return error(["message"=>"user not found"]);
        }

        $this->validate($request, [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required','email','string','max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => [
               'required','numeric',
               Rule::unique('users')->ignore($user->id),
            ],
        ]);

        $requestUserData = $request->except('course_id','car_type','driver_level');
        $user->update($requestUserData);

        if($request->course_id){
            //for ($i=0; $i<count($request->course_id); $i++){
                $studentcourse=StudentCourse::where('student_id',$user->id)->first();
                $studentcourse->update([
                    'course_id'=>$request->course_id,
                    'course_learning'=>$request->car_type,
                    'test_type'=>$request->driver_level,
                ]);
            //}
        }
        return ok(["message"=>"Student Profile updated successfully!"]);
    }


    public function filter(Request $request){
    
        $users = User::where('role','student');
        if($request->email):
            $users=$users->where('email', "like", "%" . $request->email . "%");
        endif;

        if($request->nationality):
            $users=$users->where('nationality', "like", "%" . $request->nationality . "%");
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
            $users=$users->where('role', '=',  'student')
            ->where(function($query) use ($value) {
                    $query->where('first_name', "like", "%" . $value . "%");
                    $query->orWhere('phone', 'LIKE', "%".$value."%");
            });
        endif;

        return ok($users->get());
    }

}
