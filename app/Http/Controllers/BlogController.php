<?php

namespace App\Http\Controllers;

use App\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ok(Blog::all());
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
        ]);
        if($validator->fails()){
                return error($validator->errors()->toJson());
            }

        $img_name ='/uploads/img/setting/blog/default.png';

        if($request->hasFile('image'))
        {
            $img_name = saveImage($request->image , '/uploads/img/setting/blog/');
        }
        $data = $request->all();
        $data['image'] = $img_name;

        $blog =  Blog::create($data);

        return ok($blog);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return ok(Blog::findorfail($id));
    }

    //update
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
        ]);
        if($validator->fails()){
                return error($validator->errors()->toJson());
            }

        $blog =  Blog::find($id);
        $img_name = $blog->image;


        if($request->hasFile('image'))
        {
            if($blog->image != '/uploads/img/setting/blog/default.png')
            deleteImage($blog->image);
            $img_name = saveImage($request->logo , '/uploads/img/setting/blog/');
        }

        $data = $request->all();
        $data['image'] = $img_name;
        if($blog)
        $blog->update($data);

        return ok($blog);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $blog = Blog::find($id);
        if($blog)
        {
            $blog->delete();
            return ok('remvoe successed');
        }
        return error('record not found');
    }

    public function filter(Request $request)
    {
        $blog = Blog::where('title', "like", "%" . $request->title . "%");
        return ok($blog->get());
    }
}
