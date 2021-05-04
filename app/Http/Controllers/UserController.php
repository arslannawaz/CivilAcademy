<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\Category;
use App\Course;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{


    public function validateData($request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'phone' => 'required|numeric|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);
        return $validator;
    }

    public function saveUser($request)
    {
        $img_name ='/uploads/img/users/default.png';

        if($request->hasFile('image'))
        {
            $img_name = saveImage($request->image , '/uploads/img/users/');
        }

        $user = User::create([
            'image' =>$img_name,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'role' => $request->role,
            'phone' => $request->phone,
            'country' => $request->country,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        return $user;

    }

}
