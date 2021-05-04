<?php

namespace App\Http\Controllers;

use App\CourseReview;
use App\ReplyReview;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CourseReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index($course_id)
    {
        return ok(CourseReview::with('userReview','replyReview')->where('course_id',$course_id)->get());
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
        $this->validate($request, [
            'user_id' => 'required',
            'rating' => 'required',
            'comment' => 'required',
            'type' => 'required',
            'review_to' => 'required',
        ]);

        CourseReview::create($request->all());
        return ok('Review has been added successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $course_review=CourseReview::find($id);
        if(!isset($course_review)){
            return error("review not found",404);
        }

        $course_review->update([
            'user_id'=>$request->user_id,
            'rating'=>$request->rating,
            'comment'=>$request->comment
        ]);
        return ok('Review updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $course_review=CourseReview::find($id);
        if(!isset($course_review)){
            return error("review not found",404);
        }

        $course_review->delete();
        return ok('Review deleted successfully!');
    }

    public function filterByAdmin(Request $request){
        $course_review = CourseReview::with('userReview')
        ->where('course_id',$request->course_id)   
        ->join('users', 'course_reviews.user_id', '=', 'users.id')
            ->where('users.first_name', 'like', "%" . $request->name . "%")
            ->select('course_reviews.*')
            ->get();

        return ok($course_review);
    }

    public function reviewReply(Request $request)
    {
        $this->validate($request, [
            'review_id' => 'required',
            'user_id' => 'required',
            'comment' => 'required',
        ]);

        $course_review=CourseReview::find($request->review_id);
        if(!isset($course_review)){
            return error("review not found",404);
        }
        
        $user=User::find($request->user_id);
        if(!isset($user)){
            return error("user not found",404);
        }

        ReplyReview::create($request->all());
        return ok('Reply of a review added successfully!');
    }

    public function getReviewByUserId()
    {
        $course_review=CourseReview::with('userReview','replyReview')->where('review_to',auth()->user()->id)->get();
        if($course_review->isEmpty()){
            return error("review not found",404);
        }
        return ok($course_review);
    }

    public function getReviewByTrainerId()
    {
        $course_review=CourseReview::with('userReview')->where('user_id',auth()->user()->id)->get();
        if($course_review->isEmpty()){
            return error("review not found",404);
        }
        $collection = $course_review->map(function ($item) {
            if($item->replyReview){
            $review_reply=ReplyReview::with('replyFrom')->where('review_id',$item->id)->first();
            $item->review_reply=$review_reply;        
            return $item;
            }
        });
        return ok($collection->filter());
    }
}
