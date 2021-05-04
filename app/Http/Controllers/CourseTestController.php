<?php

namespace App\Http\Controllers;

use App\CourseTest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseTestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($course_id)
    {
        $tests = CourseTest::where('course_id',$course_id)->get();
        return ok($tests);
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
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'test_type' => 'required|string|max:255',
            'course_id' => 'required|numeric',
            'passing_marks' => 'required|numeric',
            'question_limit' => 'required|numeric',
            'duration' => 'required|numeric',
        ]);

        if($validator->fails()){
            return error($validator->errors()->toJson());
            }

        $test = CourseTest::create($request->all());
        return ok($test);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CourseTest  $courseTest
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $test = CourseTest::find($id);
        if(!isset($test)){
            return response()->json(["message"=>"Test not found"],404);
        }
        return ok($test);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CourseTest  $courseTest
     * @return \Illuminate\Http\Response
     */
    public function edit(CourseTest $courseTest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CourseTest  $courseTest
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $test_id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'test_type' => 'required|string|max:255',
            'course_id' => 'required|numeric',
            'passing_marks' => 'required|numeric',
            'question_limit' => 'required|numeric',
            'duration' => 'required|numeric',
        ]);

        if($validator->fails()){
            return error($validator->errors()->toJson());
            }

        $test = CourseTest::find($test_id);

        $test->update($request->all());
        return ok($test);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CourseTest  $courseTest
     * @return \Illuminate\Http\Response
     */


    public function toggleStatus($id)
    {
        $test = CourseTest::find($id);
        $test->status = !$test->status;
        $test->save();
        return ok('Test status is updated');
    }

    public function destroy($id)
    {
        CourseTest::find($id)->delete();
        return ok('Test is deleted');
    }


    public function getAllTestByCourse($id)
    {
        $tests = CourseTest::where(['course_id'=>$id,'status'=>1])->get();
        return ok($tests);
    }

    public function filterByAdmin(Request $request){
        $tests = CourseTest::where('course_id',$request->course_id)        
        ->where('title', "like", "%" . $request->name . "%");
        return ok($tests->get());
    }

}
