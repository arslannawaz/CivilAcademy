<?php

namespace App\Http\Controllers;
use App\StudentTest;
use App\StudentQuestionAnswer;
use App\CourseTest;
use App\TestQuestion;
use App\Course;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class TestResultController extends Controller
{
    public function addAnswer(Request $request){

        $student_test=StudentTest::create([
            'test_id' => $request->test_id,
            'student_id' => auth()->user()->id,
        ]);

        for($i=0; $i < count($request->question_id); $i++){
            $status=0;
            $check_answer=TestQuestion::find($request->question_id[$i]);

            if($request->question_answer[$i]==$check_answer->answer){
                $status=1;
            }

            $student_answer=StudentQuestionAnswer::create([
                'student_test_id' => $student_test->id,
                'question_id' => $request->question_id[$i],
                'student_answer' => $request->question_answer[$i],
                'status' => $status,
            ]);
        }
        return response()->json(["message"=>"Success","test_result_id"=>$student_test->id],201);
    }

    public function getTestResult($test_result_id){

        $test_result=StudentTest::with('test')->where('id',$test_result_id)->get();

        if($test_result->isEmpty()){
            return error("result not found");
        }

        $collection = $test_result->map(function ($item) {
            $count_correct=0;
            $count_wrong=0;
                $question_answer=StudentQuestionAnswer::where('student_test_id',$item->id)->get();
                $item->studentAnswer = $question_answer;
                    foreach($question_answer as $ans){
                        $question=TestQuestion::find($ans->question_id);
                        $ans->question=$question;

                        if($ans->status===0){
                            $ans->status = "wrong answer";
                            $count_wrong++;
                        }
                        if($ans->status===1){
                            $ans->status = "correct answer";
                            $count_correct++;
                        }           
                    }
                $item->total_correct_answers=$count_correct;
                $item->total_wrong_answers=$count_wrong;  
                $item->result_percentage=($count_correct/($count_correct+$count_wrong))*100;
                
                if($item->test->passing_marks<=$item->result_percentage){
                    $item->is_passed='passed';
                }
                else{
                    $item->is_passed='failed';
                }
                
            return $item;
        });
        return ok($collection);
    }


    public function getMyTestResults($type){

        $test_result=StudentTest::with('test')
        ->join('course_tests', 'student_tests.test_id', '=', 'course_tests.id')
            ->where('course_tests.test_type','=',$type)
        ->where('student_id',auth()->user()->id)
        ->select('student_tests.*')
        ->get();

        if($test_result->isEmpty()){
            return error("result not found");
        }

        $collection = $test_result->map(function ($item) {
            $count_correct=0;
            $count_wrong=0;
                $question_answer=StudentQuestionAnswer::where('student_test_id',$item->id)->get();
                //$item->studentAnswer = $question_answer;
                    foreach($question_answer as $ans){
                        $question=TestQuestion::find($ans->question_id);
                        $ans->question=$question;

                        if($ans->status===0){
                            $ans->status = "wrong answer";
                            $count_wrong++;
                        }
                        if($ans->status===1){
                            $ans->status = "correct answer";
                            $count_correct++;
                        }           
                    }
                $item->total_correct_answers=$count_correct;
                $item->total_wrong_answers=$count_wrong;  
                $item->result_percentage=($count_correct/($count_correct+$count_wrong))*100;
                
                if($item->test->passing_marks<=$item->result_percentage){
                    $item->is_passed='passed';
                }
                else{
                    $item->is_passed='failed';
                }

                $item->test_type=$item->test->test_type;
                $course=Course::find($item->test->course_id);
                $item->course_name=$course->title_en;
                $item->trainer_name=$course->organizedBy;
                
            return $item;
        });
        return ok($collection);
    }


    public function getMyTestResultById($id){

        $test_result=StudentTest::with('test')->find($id);

        if(!isset($test_result)){
            return error("Tett Result not found");
        }

            $count_correct=0;
            $count_wrong=0;
                $question_answer=StudentQuestionAnswer::where('student_test_id',$id)->get();
                //$item->studentAnswer = $question_answer;
                    foreach($question_answer as $ans){
                        $question=TestQuestion::find($ans->question_id);
                        $ans->question=$question;

                        if($ans->status===0){
                            $ans->status = "wrong answer";
                            $count_wrong++;
                        }
                        if($ans->status===1){
                            $ans->status = "correct answer";
                            $count_correct++;
                        }           
                    }
                $test_result["total_correct_answers"]=$count_correct;
                $test_result["total_wrong_answers"]=$count_wrong;  
                $test_result["result_percentage"]=($count_correct/($count_correct+$count_wrong))*100;
                
                $test=CourseTest::find($test_result->test_id);
                if($test->passing_marks<=$test_result["result_percentage"]){
                    $test_result["is_passed"]='passed';
                }
                else{
                    $test_result["is_passed"]='failed';
                }

                $test_result["test_type"]=$test->test_type;
                $course=Course::find($test->course_id);
                $test_result["course_name"]=$course->title_en;
                $test_result["trainer_name"]=$course->organizedBy;
        return ok($test_result);
    }


    public function getStudentTestResult(){

        $test_result=StudentTest::with('test','studentName:id,first_name,last_name')->get();

        if($test_result->isEmpty()){
            return error("result not found");
        }

        $collection = $test_result->map(function ($item) {
            $count_correct=0;
            $count_wrong=0;
                $question_answer=StudentQuestionAnswer::where('student_test_id',$item->id)->get();
                //$item->studentAnswer = $question_answer;
                    foreach($question_answer as $ans){
                        $question=TestQuestion::find($ans->question_id);
                        $ans->question=$question;

                        if($ans->status===0){
                            $ans->status = "wrong answer";
                            $count_wrong++;
                        }
                        if($ans->status===1){
                            $ans->status = "correct answer";
                            $count_correct++;
                        }           
                    }
                //$item->total_correct_answers=$count_correct;
                //$item->total_wrong_answers=$count_wrong;  
                $item->result_percentage=($count_correct/($count_correct+$count_wrong))*100;
                
                if($item->test->passing_marks<=$item->result_percentage){
                    $item->is_passed='passed';
                }
                else{
                    $item->is_passed='failed';
                }

                $course=Course::find($item->test->course_id);
                $item->course_name=$course->title_en;
                $item->trainer_name=$course->organizedBy;
                unset($item->test);
            return $item;
        });
        return ok($collection);
    }

    public function getFilterStudentTestResult(Request $request){

        $test_result=StudentTest::with('test','studentName:id,first_name,last_name')
        
        ->join('users', 'student_tests.student_id', '=', 'users.id')
        ->where('users.first_name', 'like', "%" . $request->student . "%")
        
        ->join('course_tests', 'student_tests.test_id', '=', 'course_tests.id')
        ->join('courses', 'course_tests.course_id', '=', 'courses.id')
        ->where('courses.title_en', 'like', "%" . $request->course . "%")
        
        ->join('users as t', 'courses.organized_by', '=', 't.id')
        ->where('t.first_name', 'like', "%" . $request->trainer . "%")

        ->where(function($q) use ($request) {
            if ($request->date_from) {
                 $q->whereDate('student_tests.created_at','>=', $request->date_from)
                 ->whereDate('student_tests.created_at', '<=', $request->date_to);  
            }
        })

        ->select('student_tests.*')
        ->get();

        $collection = $test_result->map(function ($item) use($request) {
            $count_correct=0;
            $count_wrong=0;
                $question_answer=StudentQuestionAnswer::where('student_test_id',$item->id)->get();
                //$item->studentAnswer = $question_answer;
                    foreach($question_answer as $ans){
                        $question=TestQuestion::find($ans->question_id);
                        $ans->question=$question;

                        if($ans->status===0){
                            $ans->status = "wrong answer";
                            $count_wrong++;
                        }
                        if($ans->status===1){
                            $ans->status = "correct answer";
                            $count_correct++;
                        }           
                    }
                $item->total_correct_answers=$count_correct;
                $item->total_wrong_answers=$count_wrong;  
                $item->result_percentage=($count_correct/($count_correct+$count_wrong))*100;
                
                if($item->test->passing_marks<=$item->result_percentage){
                    $item->is_passed='passed';
                }
                else{
                    $item->is_passed='failed';
                }

                $course=Course::find($item->test->course_id);
                $item->course_name=$course->title_en;
                $item->trainer_name=$course->organizedBy;
                unset($item->test);

            return $item;
        });
        return ok($collection);
    }

}
