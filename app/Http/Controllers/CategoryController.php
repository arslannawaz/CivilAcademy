<?php

namespace App\Http\Controllers;

use App\Category;
use App\Course;
use App\CategoryRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $categories = Category::all();
        return ok($categories);
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
            'title_en' => 'required|string|max:255|unique:categories',
            'title_ar' => 'required|string|max:255',
        ]);

        if($validator->fails()){
            return error($validator->errors()->toJson());
        }

        // $img_name ='/uploads/img/categories/default.png';
        // if($request->hasFile('image'))
        // {
        //     $img_name = saveImage($request->image , '/uploads/img/categories/');
        // }

        $img_name ='https://www.w3schools.com/howto/img_avatar.png';
        if($request->image){
            $img_name=$request->image;
        }

        $requestData = $request->all();
        $requestData['image'] = $img_name;
        $category = Category::create($requestData);
        return ok($category);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */

    public function show($id)
    {

        $categories = Category::find($id);
        return ok($categories);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $categories = Category::find($id);
        return ok($categories);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'title_en' => 'required|string|max:255',
            'title_ar' => 'required|string|max:255',
        ]);

        if($validator->fails())
        {
            return error($validator->errors()->toJson());
        }

        $category = Category::find($id);

        // if($request->hasFile('image'))
        // {
        //     if($category->image != '/uploads/img/categories/default.png')
        //     deleteImage('/uploads/img/categories/'.$category->image);
        //     $img_name = saveImage($request->image , '/uploads/img/categories/');
        // }

        $requestData = $request->all();
        if($request->image){
            $requestData['image']=$request->image;
        }
        $category->update($requestData);

        return ok($category);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category=Category::find($id);
        if(!isset($category)){
            return response()->json(["message"=>"category not found"],404);
        }
        
        $course=Course::where('category_id',$category->id)->get();
        if($course->isEmpty()){
            $category->delete();
            return response()->json(['message'=>'Category has been deleted successfully'],201); 
        }
        else{
            return response()->json(["message"=>"You cannot delete this category because we have courses against this"],404);
        }  
    }

    public function toggleStatus($id)
    {
        $category=Category::find($id);
        if(!isset($category)){
            return response()->json(["message"=>"category not found"],404);
        }
        $category->status = !$category->status;
        $category->save();
        return ok('Category status has been updated');
    }

    public function getAllStudentCategory()
    {
        $categories = Category::where('status',1)->get();
        return ok($categories);
    }

    public function makeCategoryRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title_en' => 'required|string|max:255|unique:category_requests|unique:categories',
            'title_ar' => 'required|string|max:255',
        ]);

        if($validator->fails()){
            return error($validator->errors()->toJson());
        }

        // $img_name ='/uploads/img/categories/default.png';
        // if($request->hasFile('image'))
        // {
        //     $img_name = saveImage($request->image , '/uploads/img/categories/');
        // }

        $img_name ='https://www.w3schools.com/howto/img_avatar.png';
        if($request->image){
            $img_name=$request->image;
        }
        
        $requestData = $request->all();
        $requestData['image'] = $img_name;
        $requestData['trainer_id'] = auth()->user()->id;
        $category = CategoryRequest::create($requestData);
        return ok($category);
    }

    public function getCategoryRequestList()
    {
        $categories = CategoryRequest::with('byTrainer')->where('status',0)->get();
        return ok($categories);
    }

    public function changeStatusCategoryRequest(Request $request, $id)
    {
        $this->validate($request, [
            'status' => 'required|numeric',
        ]);

        $category=CategoryRequest::find($id);
        if(!isset($category)){
            return error(["message"=>"category request not found"]);
        }

        if($request->status==1){
            $category->status = $request->status;
            $category->save();
            $requestData=$category->toArray();
            Category::create($requestData);
            $message = "Your category request for has been Approved";
            $notification=notified($category->trainer_id,auth()->user()->id,$message);
            return ok(['message'=>'Category requst has been approved successfully']); 
        }
        if($request->status==3){
            $category->delete();
            $message = "Your category request for has been Rejected";
            $notification=notified($category->trainer_id,auth()->user()->id,$message);
            return ok(['message'=>'Category request has been rejected']); 
        }

    }


    public function filter(Request $request){
    
        $categories = Category::where('title_en', "like", "%" . $request->name . "%");

        if($request->status=='1'):
            $categories=$categories->where('status','=',1);
        endif;

        if($request->status=='0'):
            $categories=$categories->where('status','=',0);
        endif;

        return ok($categories->get());
    }
}
