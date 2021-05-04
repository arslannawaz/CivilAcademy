<?php

namespace App\Http\Controllers;

use App\Promotion;
use App\User;
use Illuminate\Http\Request;

class AdminPromotionController extends Controller
{
    public function savePromotion(Request $request){

        $this->validate($request, [
            'gender' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'user_role' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'subject_en' => 'required|string|max:255',
            'message_en' => 'required',
            'title_ar' => 'required|string|max:255',
            'subject_ar' => 'required|string|max:255',
            'message_ar' => 'required',
        ]);

        Promotion::create($request->all());
        return ok('Promotion has been added');
    }

    public function show($id){
        $promotion=Promotion::find($id);
        if(!isset($promotion)){
            return error(["message"=>"promotion not found"]);
        }
        return ok($promotion);
    }

    public function list(){
        $promotion=Promotion::all();
        return ok($promotion);
    }

    public function delete($id){
        $promotion=Promotion::find($id);
        if(!isset($promotion)){
            return error(["message"=>"promotion not found"]);
        }
        $promotion->delete();
        return ok("Promotion has been deleted");
    }

    public function update(Request $request, $id){

        $this->validate($request, [
            'gender' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'user_role' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'subject_en' => 'required|string|max:255',
            'message_en' => 'required',
            'title_ar' => 'required|string|max:255',
            'subject_ar' => 'required|string|max:255',
            'message_ar' => 'required',
        ]);

        $promotion=Promotion::find($id);
        if(!isset($promotion)){
            return error(["message"=>"promotion not found"]);
        }

        $promotion->update($request->all());
        return ok("Promotion has been updated");
    }

    public function toggleStatus($id){
        $promotion=Promotion::find($id);
        if(!isset($promotion)){
            return error(["message"=>"promotion not found"]);
        }
        $promotion->status = !$promotion->status;
        $promotion->save();
        return ok('Promotion status has been updated');
    }

    public function sendPromotion($id){
        $promotion=Promotion::find($id);
        if(!isset($promotion)){
            return error(["message"=>"promotion not found"]);
        }

        $promotion->is_send = 1;
        $promotion->save();

        if($promotion->user_role=='All' && $promotion->gender=='All'){
            $user=User::where(['country'=>$promotion->country])->get();
            return ok("Promotion has been sent");
        }

        if($promotion->user_role=='All' && $promotion->gender!='All'){
            $user=User::where(['gender'=>$promotion->gender,'country'=>$promotion->country])->get();
            return ok("Promotion has been sent");
        }

        if($promotion->user_role!='All' && $promotion->gender=='All'){
            $user=User::where(['role'=>$promotion->user_role,'country'=>$promotion->country])->get();
            return ok("Promotion has been sent");
        }

        if($promotion->user_role!='All' && $promotion->gender!='All'){
            $user=User::where(['gender'=>$promotion->gender,'country'=>$promotion->country,
            'role'=>$promotion->user_role])->get();
            return ok("Promotion has been sent");
        }
    }

    public function filter(Request $request){
    
        $promotion = Promotion::where('title_en', "like", "%" . $request->title . "%");

        if($request->subject):
            $promotion=$promotion->where('subject_en','LIKE',"%" . $request->subject . "%");
        endif;

        if($request->status=='1'):
            $promotion=$promotion->where('status','=',1);
        endif;

        if($request->status=='0'):
            $promotion=$promotion->where('status','=',0);
        endif;
        return ok($promotion->get());
    }
}
