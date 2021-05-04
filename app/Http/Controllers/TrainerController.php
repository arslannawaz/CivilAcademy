<?php

namespace App\Http\Controllers;

use App\Trainer;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;



class TrainerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Trainer  $trainer
     * @return \Illuminate\Http\Response
     */
    public function show(Trainer $trainer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Trainer  $trainer
     * @return \Illuminate\Http\Response
     */
    public function edit(Trainer $trainer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Trainer  $trainer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Trainer $trainer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Trainer  $trainer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Trainer $trainer)
    {
        //
    }

    public function getProfile()
    {
        $user=auth()->user();
        $user->trainer;
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

        $requestUserData = $request->except('organization','experience','trade_licence','certificates','trn_certificate','emirate_id');
        $requestTrainerData = $request->only('organization','experience','trade_licence','certificates','trn_certificate','emirate_id');
        $user->update($requestUserData);
        $trainer=Trainer::where('user_id',$user->id);
        $trainer->update($requestTrainerData);
        return response()->json(["message"=>"Profile updated successfully!"],201);
    }

    public function getTrainerList()
    {
        $users=User::with('trainer')->where('role','trainer')->get();
        return ok($users);
    }

    public function findTrainerById($id)
    {
        $user=User::find($id);
        if(!isset($user)){
            return response()->json(["message"=>"user not found"],404);
        }
        $user->trainer;
        return Ok($user);
    }

    public function updateTrainerProfile(Request $request,$id)
    {
        $user=User::find($id);
        if(!isset($user)){
            return response()->json(["message"=>"user not found"],404);
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

        $requestUserData = $request->except('residence','course_offered','organization','experience','trade_licence','certificates','trn_certificate','emirate_id');
        $requestTrainerData = $request->only('residence','course_offered','organization','experience','trade_licence','certificates','trn_certificate','emirate_id');
        $user->update($requestUserData);
        $trainer=Trainer::where('user_id',$user->id);
        $trainer->update($requestTrainerData);
        return response()->json(["message"=>"Trainer Profile updated successfully!"],201);
    }

    public function filter(Request $request){
    
        $users = User::where('role','trainer');
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
            $users=$users->where('role', '=',  'trainer')
            ->where(function($query) use ($value) {
                    $query->where('first_name', "like", "%" . $value . "%");
                    $query->orWhere('phone', 'LIKE', "%".$value."%");
            });
        endif;

        return ok($users->get());
    }

}
