<?php

namespace App\Http\Controllers;

use App\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class SettingController extends Controller
{
    //


    public function getSiteSetting()
    {
        return ok(SiteSetting::all());
    }

    public function upadteSiteSetting(Request $request)
    {
        if($request->trainer_subscription){
            $this->saveContent(['charge_value'=>$request->trainer_subscription,'charge_key'=>'trainer']);
        }

        if($request->trainer_status){
            $this->saveContent(['status'=>$request->trainer_status,'charge_key'=>'trainer']);
        }

        if($request->student_subscription){
            $this->saveContent(['charge_value'=>$request->student_subscription,'charge_key'=>'student']);
        }

        if($request->student_status){
            $this->saveContent(['status'=>$request->student_status,'charge_key'=>'student']);
        }

        if($request->admin_commission){
            $this->saveContent(['charge_value'=>$request->admin_commission,'charge_key'=>'admin']);
        }

        if($request->admin_status){
            $this->saveContent(['status'=>$request->admin_status,'charge_key'=>'admin']);
        }

        if($request->promotion_charge){
            $this->saveContent(['charge_value'=>$request->promotion_charge,'charge_key'=>'promotion']);
        }

        if($request->promotion_status){
            $this->saveContent(['status'=>$request->promotion_status,'charge_key'=>'promotion']);
        }

        return ok('Setting Updated');
    }

    public function saveContent($data)
    {

        $setting = SiteSetting::where('charge_key',$data['charge_key'])->first();
        if($setting)
        {
            $setting->update($data);
        }
        else
        {
            $setting = new SiteSetting();
            $setting->create($data);
        }
    }
}
