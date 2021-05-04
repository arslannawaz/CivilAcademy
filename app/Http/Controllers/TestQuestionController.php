<?php

namespace App\Http\Controllers;

use App\TestQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TestQuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {

        $questions = TestQuestion::where('course_test_id',$id)->get();
        return ok($questions->shuffle());
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
            'question' => 'required|string|max:255',
            'option_a' => 'required|string|max:255',
            'option_b' => 'required|string|max:255',
            'option_c' => 'required|string|max:255',
            'option_d' => 'required|string|max:255',
            'answer' => 'required|string|max:255',
            'course_test_id' => 'required|numeric',
        ]);

        if($validator->fails()){
            return error($validator->errors()->toJson());
        }

        // $img_name ='/uploads/img/courses/test/questions/default.png';
        // if($request->hasFile('image'))
        // {
        //     $img_name = saveImage($request->image , '/uploads/img/courses/test/questions/');
        // }

        $img_name='https://image.shutterstock.com/image-vector/vector-creative-illustration-online-elearning-260nw-1171739044.jpg';
        if($request->image)
        {
            $img_name = $request->image;
        }

        $requestData = $request->all();
        $requestData['image'] = $img_name;
        $ques = TestQuestion::create($requestData);

        return ok($ques);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\TestQuestion  $testQuestion
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $testQuestion=TestQuestion::find($id);
        if(!isset($testQuestion)){
            return response()->json(["message"=>"Question not found"],404);
        }
        return ok($testQuestion);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\TestQuestion  $testQuestion
     * @return \Illuminate\Http\Response
     */
    public function edit(TestQuestion $testQuestion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TestQuestion  $testQuestion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request , $id)
    {
        $ques=TestQuestion::find($id);
        if(!isset($ques)){
            return response()->json(["message"=>"Question not found"],404);
        }

        $validator = Validator::make($request->all(), [
            'question' => 'required|string|max:255',
            'option_a' => 'required|string|max:255',
            'option_b' => 'required|string|max:255',
            'option_c' => 'required|string|max:255',
            'option_d' => 'required|string|max:255',
            'answer' => 'required|string|max:255',
            'course_test_id' => 'required|numeric',
        ]);

        if($validator->fails()){
            return error($validator->errors()->toJson());
        }

        //$img_name =$ques->image;
        // if($request->hasFile('image'))
        // {
        //     if($ques->image != '/uploads/img/courses/test/questions/default.png')
        //     deleteImage($ques->image);
        //     $img_name = saveImage($request->image , '/uploads/img/courses/test/questions/');
        // }

        $requestData = $request->all();
        //$requestData['image'] = $img_name;

        $ques->update($requestData);

        return ok($ques);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TestQuestion  $testQuestion
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        TestQuestion::find($id)->delete();
        return ok('Deteled record successfylly!');
    }

    public function getQuestionsByTest($test_id)
    {
        $questions = TestQuestion::where(['course_test_id'=>$test_id,'status'=>1])->select('id','image','question','answer','status')->get();
        $collection = $questions->map(function ($item) {
            $option=array();
            $option_a = TestQuestion::where('id',$item->id)->select('option_a')->first();
            $option_b = TestQuestion::where('id',$item->id)->select('option_b')->first();
            $option_c = TestQuestion::where('id',$item->id)->select('option_c')->first();
            $option_d = TestQuestion::where('id',$item->id)->select('option_d')->first();

            $option=collect([
               ['key'=>0,'value'=>$option_a->option_a],
               ['key'=>1,'value'=>$option_b->option_b],
               ['key'=>2,'value'=>$option_c->option_c],
               ['key'=>3,'value'=>$option_d->option_d],
            ]);
            $item->options=$option;
            return $item;
        });
        return ok($collection);
    }

    public function toggleStatus($id)
    {
        $testQuestion=TestQuestion::find($id);
        if(!isset($testQuestion)){
            return response()->json(["message"=>"Question not found"],404);
        }
        $testQuestion->status = !$testQuestion->status;
        $testQuestion->save();
        return ok('Question status is updated');
    }

    public function filterByAdmin(Request $request){
        $testQuestion = TestQuestion::where('course_test_id',$request->test_id)
        ->where('question', "like", "%" . $request->question . "%");
        if($request->status=='1'):
            $testQuestion=$testQuestion->where('status','=',1);
        endif;

        if($request->status=='0'):
            $testQuestion=$testQuestion->where('status','=',0);
        endif;
        return ok($testQuestion->get());
    }
}
