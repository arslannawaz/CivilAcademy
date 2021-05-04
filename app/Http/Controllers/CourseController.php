<?php

namespace App\Http\Controllers;

use App\Course;
use App\CourseImage;
use App\CourseLocation;
use App\BookCourse;
use App\User;
use App\BookingPayment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $course=Course::with('courseCategory','images','locations','organizedBy')->get();
        return ok($course);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create()
    {
    }

    public function removeCourseLocation($location_id)
    {
        return ok(CourseLocation::findorfail($location_id));
    }

    public function deleteCourseLocation($id)
    {
        $loc = CourseLocation::find($id);
        if($loc)
        {
            $loc->delete();
            return ok('Location deleted successfully');
        }
        return error('Location not found');
    }
    public function updateCourseLocations($course_locations)
    {

        if($course_locations != null &&  $course_locations != '' && count($course_locations) > 0 )
        {
            foreach($course_locations as $c_loc)
            {

                if($c_loc->id == -1)
                {

                    CourseLocation::create([
                        'course_id'=>$c_loc->course_id,
                        'country'=> $c_loc->country,
                        'address'=> $c_loc->address,
                        ]);
                }
                else{

                    $loc =CourseLocation::find($c_loc->id);
                    $loc->update([
                        'course_id'=>$c_loc->course_id,
                        'country'=> $c_loc->country,
                        'address'=> $c_loc->address,
                        ]);
                }

            }
        }

    }

    public function saveCourseLocations($course_locations ,$course_id)
    {
        if( $course_locations != null &&  $course_locations != '' && count($course_locations) > 0 )
        {
            foreach($course_locations as $c_loc)
            {
                CourseLocation::create([
                    'course_id'=>$course_id,
                    'country'=> $c_loc['country'],
                    'address'=> $c_loc['address'],
                    'area'=> $c_loc['area'],
                    'state'=> $c_loc['state'],
                ]);
            }
        }
    }

    public function saveCourseImages($images, $course_id)
    {
        $img_array = [];
        if($images)
        {
            foreach($images as $img)
            {
                $img_name = saveImage($img , '/uploads/img/courses/gallary/');
                $c_img = CourseImage::create(['course_id'=>$course_id,'image'=>$img_name]);
                array_push($img_array, ['id'=>$c_img->id,'course_id'=>$course_id,'image'=>$img_name]);
            }
        }
        return $img_array;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'title_en' => 'required|string|max:255',
            'title_ar' => 'required|string|max:255',
            'category_id' => 'required|numeric',
            'price' => 'required|numeric',
            //'status' => 'required|boolean',
        ]);

        if($validator->fails()){
            return error($validator->errors()->toJson());
        }

        //$img_name ='/uploads/img/courses/logo/default.png';
        // if($request->hasFile('logo'))
        // {
        //     $img_name = saveImage($request->logo , '/uploads/img/courses/logo/');
        // }

        $img_name='https://image.shutterstock.com/image-vector/vector-creative-illustration-online-elearning-260nw-1171739044.jpg';
        if($request->logo)
        {
            $img_name = $request->logo;
        }

        //$images =   $request->file('images');
        $locations = $request->locations;
        $requestData = $request->except('images','locations');
        $requestData['logo'] = $img_name;
        $course = Course::create($requestData);

        //save course gallary
        //$this->saveCourseImages($images, $course->id);
        if($request->images){
            for ($i=0; $i<count($request->images); $i++){
                $c_img = CourseImage::create([
                    'course_id'=>$course->id,
                    'image'=>$request->images[$i]
                ]);
            }
        }

        // save lcoations
        $this->saveCourseLocations($locations, $course->id);

        return ok('course saved successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $course = Course::find($id);
        if(!isset($course)){
            return response()->json("Course not found",404);
        }

        $course->images;
        $course->locations;
        $course->reviews;
        $course->coursecategory;
        $course->organizedBy;
        return ok(['course'=>$course]);
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return ok(Course::find($id));
    }

    public function updateCourseImages(Request $request)
    {
        return ok($this->saveCourseImages($request->images, $request->course_id));
    }



    public function deleteCourseImage($id)
    {
        $c_img =  CourseImage::find($id);
        deleteImage($c_img->image);
        $c_img->delete();
        return ok('course image is deleted successfully');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Course  $course
     * @return \Illuminate\Http\Response
     */


    public function update(Request $request , $course_id)
    {

        $validator = Validator::make($request->all(), [
            'category_id' => 'required|numeric',
            'price' => 'required|numeric',
        ]);

        if($validator->fails()){
            return error($validator->errors()->toJson());
        }

        $course = Course::find($course_id);
        if(!isset($course)){
            return error("Course not found");
        }

        // if($request->hasFile('logo'))
        // {
        //     remove other file without default
        //     if($course->logo != '/uploads/img/courses/logo/default.png')
        //     deleteImage($course->logo);
        //     $img_name = saveImage($request->logo , '/uploads/img/courses/logo/');
        // }

        $requestData = $request->except('locations','images');
        if($request->logo){
            $requestData['logo'] = $request->logo;
        }
        if($request->locations){
            $locations = $request->locations;
            $courselocaion=CourseLocation::where('course_id',$course->id);
            $courselocaion->delete();
            if($locations != null &&  $locations != '' && count($locations) > 0 )
                {
                    foreach($locations as $c_loc)
                    {
                        CourseLocation::create([
                            'course_id'=>$course->id,
                            'country'=> $c_loc['country'],
                            'address'=> $c_loc['address'],
                            'area'=> $c_loc['area'],
                            'state'=> $c_loc['state'],
                        ]);
                    }
                }
        }

        if($request->images){
            $images = $request->images;
            $courseimage=CourseImage::where('course_id',$course->id);
            $courseimage->delete();
            
                    for($i=0;$i<count($images);$i++)
                    {
                        $c_img = CourseImage::create([
                            'course_id'=>$course->id,
                            'image'=>$images[$i]
                        ]);
                    }
        }

        $course->update($requestData);
        return ok('course updated successfully');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Course  $course
     * @return \Illuminate\Http\Response
     */

    public function destroy(Course $course)
    {
        //
    }

    public function toggleStatus($id)
    {
        $course = Course::find($id);
        $course->status = !$course->status;
        $course->save();
        return ok('Course status is updated');
    }


    public function getAllCoursesList()
    {
        $course=Course::with('courseCategory','images','locations','organizedBy')->where('status',1)->get();
        $collection = $course->map(function ($item) {
            $bookedCourses=BookCourse::where(['course_id'=>$item->id,'student_id'=>auth()->user()->id])->get();
            if(count($bookedCourses)){
                $item->iscoursebooked = "yes";
            }
            else{
                $item->iscoursebooked="no";
            }         
            return $item;
        });

        return ok($collection);
    }

    public function getAllCoursesByCategory($id)
    {
        $course = Course::with('courseCategory','images','locations','organizedBy')->where(['category_id'=>$id,'status'=>1])->get();
        return ok($course);
    }

    public function addCourse(Request $request)
    {
        $this->validate($request, [
            'course_name' => 'required|string|max:255',
            'category_id' => 'required|numeric',
            'description_en' => 'required',
            'fee_detail' =>'required', 
        ]);

        $user=auth()->user();
        $course = Course::create([
            'title_en' => $request->course_name,
            'category_id' => $request->category_id,
            'organized_by' => $user->id,
            'status' => 0,
            'logo' => $request->image,
            'description_en' => $request->description_en,
            'price' => $request->fee_detail,
        ]);

        CourseImage::create([
            'course_id'=>$course->id,
            'image'=>$request->image,
        ]);

        CourseLocation::create([
            'course_id'=>$course->id,
            'country'=> $request->country,
            'address'=> $request->address,
        ]);

        $users=User::where(['role'=>'student'])->get();
        foreach($users as $user){
            $notification_message = "We have added a new course";
            $notification=notifiedWithEvent($user->id,auth()->user()->id,$notification_message,'course',$course->id);
        }

        return ok(["message"=>"Course added successfully!"]);
    }


    public function updateCourseByTrainer(Request $request , $course_id)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|numeric',
            'price' => 'required|numeric',
        ]);

        if($validator->fails()){
            return error($validator->errors()->toJson());
        }

        $course = Course::find($course_id);
        if(!isset($course)){
            return error("Course not found");
        }

        $requestData = $request->except('country','images','address');
        if($request->logo){
            $requestData['logo'] = $request->logo;
        }
        if($request->country){
            $courselocaion=CourseLocation::where('course_id',$course->id);
            $courselocaion->delete(); 
            CourseLocation::create([
                'course_id'=>$course->id,
                'country'=> $request->country,
                'address'=> $request->address,
            ]);
        }

        if($request->images){
            $images = $request->images;
            $courseimage=CourseImage::where('course_id',$course->id);
            $courseimage->delete();
                CourseImage::create([
                    'course_id'=>$course->id,
                    'image'=>$images,
                ]);
        }
        $course->update($requestData);
        return ok('course updated successfully');
    }


    public function myCourse()
    {
        $course=Course::with('courseCategory','images','locations')->where(['organized_by'=>auth()->user()->id])->get();

        $collection = $course->map(function ($item) {
            if($item->status===0){
                $item->status = "pending";
            }
            if($item->status===1){
                $item->status = "approved";
            }       
            return $item;
        });

        return ok($collection);
    }

    public function filter(Request $request){
    
        $courses = Course::where('title_en', "like", "%" . $request->name . "%");
        if($request->status=='1'):
            $courses=$courses->where('status','=',1);
        endif;

        if($request->status=='0'):
            $courses=$courses->where('status','=',0);
        endif;

        return ok($courses->get());
    }

    public function getCourseByLocation(Request $request){
    
        $courses = Course::with('courseCategory','images','locations','organizedBy')
                ->where('status','=', 1)
                ->where('title_en', "like", "%" . $request->title . "%")
                 ->join('course_locations as loc', 'courses.id', '=', 'loc.course_id')
                //->where(function($q) use ($request) {
                        //if ($request->country) {
                        ->where('loc.country','=', $request->country)
                        //}
                   // })
                    ->select('courses.*')
                 ->get();   
        return ok($courses);
    }

}
