<?php

namespace App\Http\Controllers;

use App\SpecialOffer;
use App\SpecialOfferImage;
use App\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class SpecialOfferController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        return ok(SpecialOffer::with('images')->get());
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

    public function saveSpecialOfferImages($images= null , $special_offer_id)
    {
        $img_array = [];
        if($images)
        {
            foreach($images as $img)
            {
                $img_name = saveImage($img , '/uploads/img/special_offer/');
                $c_img = SpecialOfferImage::create(['special_offer_id'=>$special_offer_id,'image'=>$img_name]);
                array_push($img_array, ['id'=>$c_img->id,'special_offer_id'=>$special_offer_id,'image'=>$img_name]);
            }
        }
        return $img_array;
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'discount' => 'numeric|required',
            'promotion_start_date' => 'required',
            'promotion_end_date' => 'required',
            'course_id' => 'required',
            'title_en' => 'required',
            'before_price' => 'required',
            'after_price' => 'required',
        ]);

        $course = Course::find($request->course_id);
        if(!isset($course)){
            return response()->json("Course not found",404);
        }

        $offer=SpecialOffer::where('course_id',$course->id)->where('promotion_end_date','>=',date('Y-m-d'))->get();

        if($offer->isEmpty()){
            $requestData=$request->except('image');
            $requestData['trainer_id']=$course->organized_by;
            // $requestData['before_price']=$course->price;
            // $discount = ($course->price*$request->discount)/100;
            // $requestData['after_price']=$course->price-$discount;
            $specialOffer = SpecialOffer::create($requestData);
            SpecialOfferImage::create(['special_offer_id'=>$specialOffer->id,'image'=>$request->image]);
            return ok("Promotion has been Added");
        }
        return ok("Promotion for this course already exists!");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SpecialOffer  $specialOffer
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        $special_offer=SpecialOffer::find($id);
        if(!isset($special_offer)){
            return response()->json(["message"=>"Promotion not found"],404);
        }
        $special_offer->images;
        $special_offer->course;
        return ok($special_offer);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SpecialOffer  $specialOffer
     * @return \Illuminate\Http\Response
     */
    public function edit(SpecialOffer $specialOffer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SpecialOffer  $specialOffer
     * @return \Illuminate\Http\Response
     */


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'discount' => 'numeric',
            'promotion_start_date' => 'required',
            'promotion_end_date' => 'required',
            'title_en' => 'required',
            'before_price' => 'required',
            'after_price' => 'required',
            'course_id' => 'required',
        ]);

        $specialOffer=SpecialOffer::find($id);
        if(!isset($specialOffer)){
            return response()->json(["message"=>"Promotion not found"],404);
        }

        $course = Course::find($request->course_id);
        if(!isset($course)){
            return response()->json("Course not found",404);
        }

        $requestData=$request->except('image');
        $requestData['trainer_id']=$course->organized_by;

        // $requestData['before_price']=$course->price;
        // $discount = ($course->price*$request->discount)/100;
        // $requestData['after_price']=$course->price-$discount;
        $offer=SpecialOffer::where('course_id',$course->id)->where('promotion_end_date','>=',date('Y-m-d'))->get();
        if(($offer->isEmpty())||($specialOffer->course_id==$request->course_id)){
            $specialOffer->update($requestData);
            if($request->image){
                $specialOfferImage=SpecialOfferImage::where(['special_offer_id'=>$specialOffer->id]);
                $specialOfferImage->delete();
                SpecialOfferImage::create(['special_offer_id'=>$specialOffer->id,'image'=>$request->image]);
            }
            return ok("Promotion has been Updated");
        }
        return error("Promotion for this course already exists!");
    }


    public function deleteSpecialOfferImage($id)
    {
        $obj = SpecialOfferImage::find($id);
        if($obj)
        {
                $images = SpecialOfferImage::where('special_offer_id',$obj->id)->get();
                if($images)
                $images->delete();

                $obj->delete();
                return ok('deleted record successfuly');
            }
            return error('please send correct id');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SpecialOffer  $specialOffer
     * @return \Illuminate\Http\Response
     */

    public function destroy($id)
    {
        $obj = SpecialOffer::find($id);
        if($obj)
        {
            $images = SpecialOfferImage::where('special_offer_id',$obj->id);
            if($images)
            $images->delete();

            $obj->delete();
            return ok('deleted record successfuly');
        }
        return error('Offer not found');
    }

    public function toggleStatus($id)
    {
        $special_offer=SpecialOffer::find($id);
        if(!isset($special_offer)){
            return response()->json(["message"=>"Promotion not found"],404);
        }
        $special_offer->status = !$special_offer->status;
        $special_offer->save();
        return ok('Promotion status is updated');
    }


    public function addPromotion(Request $request)
    {
        $this->validate($request, [
            'discount' => 'numeric',
            'promotion_start_date' => 'required',
            'promotion_end_date' => 'required',
            'course_id' => 'required',
        ]);

        $course = Course::find($request->course_id);
        if(!isset($course)){
            return response()->json("Course not found",404);
        }

        $offer=SpecialOffer::where('course_id',$course->id)->where('promotion_end_date','>=',date('Y-m-d'))->get();

        if($offer->isEmpty()){
            $requestData=$request->except('image');
            $requestData['trainer_id']=auth()->user()->id;
            $requestData['before_price']=$course->price;
            $discount = ($course->price*$request->discount)/100;
            $requestData['after_price']=$course->price-$discount;
            $specialOffer = SpecialOffer::create($requestData);
            SpecialOfferImage::create(['special_offer_id'=>$specialOffer->id,'image'=>$request->image]);
            return ok("Promotion has been Added");
        }
        return ok("Promotion for this course already exists!");
    }

    public function getTrainerPromotionList()
    {
        return ok(SpecialOffer::with('images','course')->where('trainer_id',auth()->user()->id)
        ->where('promotion_end_date','>=',date('Y-m-d'))
        ->orderBy('id','DESC')->get());
    }

    public function getAllPromotionList()
    {
        return ok(SpecialOffer::with('images','course')
        ->where('promotion_end_date','>=',date('Y-m-d'))->where('status',1)
        ->orderBy('id','DESC')->get());
    }

    public function filter(Request $request){
    
        $offer = SpecialOffer::where('title_en', "like", "%" . $request->title . "%");
        if($request->status=='1'):
            $offer=$offer->where('status','=',1);
        endif;

        if($request->status=='0'):
            $offer=$offer->where('status','=',0);
        endif;
        return ok($offer->get());
    }

}
