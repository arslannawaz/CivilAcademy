<?php

namespace App\Http\Controllers;

use App\CommonFrontContent;
use App\CommonFrontImage;
use App\CommonFrontSocialLink;
use App\ContactUs;
use App\HomeInstituteSection;
use Exception;
use Illuminate\Http\Request;

class FrontController extends CommonController
{

    // header methods
    public function getHeaderData()
    {
        $data = $this->getCommonContent('header_detail');
        $data['header'] = $this->getMultipleCommonFrontImage('header_detail');
        $data['logo'] = $this->getCommonFrontImage('header_logo');
        $data['favicon'] = $this->getCommonFrontImage('header_favicon');
        $data['mobile'] =  $this->getCommonSocialLink('mobile');
        $data['twitter'] =  $this->getCommonSocialLink('twitter');
        $data['telephone'] =  $this->getCommonSocialLink('telephone');
        $data['email'] =  $this->getCommonSocialLink('email');
        return ok($data);
    }

    public function saveGallData(Request $request)
    {
        if($request->image){
            $this->saveCommonFrontImage(['image'=>$request->image,'image_for'=>$request->image_for]);
            return ok("Gallery Added");
        }
        return error("Data Invalid");
    }

    public function saveHeaderData(Request $request)
    {

        $images =[];
        if($request->logo){
            $this->saveCommonFrontImage(['image'=>$request->logo,'image_for'=>'header_logo']);
        }

        if($request->favicon){
            $this->saveCommonFrontImage(['image'=>$request->favicon,'image_for'=>'header_favicon']);
        }

        if($request->image){
            foreach($request->image as $file)
            {
                $this->saveMultipleCommonFrontImage(['image'=>$file,'image_for'=>'header_detail']);
            }
        }

        if($request->title_en){
            $this->saveCommonFrontContent(['title_en'=>$request->title_en,'data_for'=>'header_detail']);
        }

        if($request->title_ar){
            $this->saveCommonFrontContent(['title_ar'=>$request->title_ar,'data_for'=>'header_detail']);
        }

        if($request->content_en){
            $this->saveCommonFrontContent(['content_en'=>$request->content_en,'data_for'=>'header_detail']);
        }
        
        if($request->content_ar){
            $this->saveCommonFrontContent(['content_ar'=>$request->content_ar,'data_for'=>'header_detail']);
        }

        if($request->content_ar){
            $this->saveCommonFrontContent(['content_ar'=>$request->content_ar,'data_for'=>'header_detail']);
        }

        if($request->mobile){
            $this->saveCommonFrontSocialLink(['value'=>$request->mobile,'key'=>'mobile']);
        }

        if($request->twitter_url){
            $this->saveCommonFrontSocialLink(['value'=>$request->twitter_url,'key'=>'twitter']);
        }

        if($request->telephone){
            $this->saveCommonFrontSocialLink(['value'=>$request->telephone,'key'=>'telephone']);
        }

        if($request->email){
            $this->saveCommonFrontSocialLink(['value'=>$request->email,'key'=>'email']);
        }
        return ok('Setting has been updated');
    }



    // about methods

    private function getAboutSetting($data_for)
    {
        $data = $this->getCommonContent($data_for);
        $data['images'] = CommonFrontImage::where('image_for',$data_for)->get();
        return $data;
    }

     // header methods
     public function getAboutData()
     {
        // about_profile
        $data=[];
        $data[0] = $this->getCommonContent('about_profile');
        $data[0]['id'] = 1;
        $data[0]['images'] = CommonFrontImage::where('image_for','about_profile')->get();

        $data[1] = $this->getCommonContent('about_mission');
        $data[1]['id'] = 2;
        $data[1]['images'] = CommonFrontImage::where('image_for','about_mission')->get();

        $data[2] = $this->getCommonContent('about_vision');
        $data[2]['id'] = 3;
        $data[2]['images'] = CommonFrontImage::where('image_for','about_vision')->get();

        $data[3] = $this->getCommonContent('about_client');
        $data[3]['id'] = 4;
        $data[3]['images'] = CommonFrontImage::where('image_for','about_client')->get();

        // about_mission
        // $data['about_mission'] = $this->getAboutSetting('about_mission');
        // // about_vision
        // $data['about_vision'] = $this->getAboutSetting('about_vision');
        // // about_client
        // $data['about_client'] = $this->getAboutSetting('about_client');


        return ok($data);
     }

     public function saveAboutData(Request $request)
     {
        if($request->profile_image){
            foreach($request->profile_image as $file)
            {
                $this->saveMultipleCommonFrontImage(['image'=>$file,'image_for'=>'about_profile']);
            }
        }
        if($request->profile_title_en){
            $this->saveCommonFrontContent(['title_en'=>$request->profile_title_en,'data_for'=>'about_profile']);
        }
        if($request->profile_title_ar){
            $this->saveCommonFrontContent(['title_ar'=>$request->profile_title_ar,'data_for'=>'about_profile']);
        }
        if($request->profile_content_en){
            $this->saveCommonFrontContent(['content_en'=>$request->profile_content_en,'data_for'=>'about_profile']);
        }
        if($request->profile_content_ar){
            $this->saveCommonFrontContent(['content_ar'=>$request->profile_content_ar,'data_for'=>'about_profile']);
        }


        if($request->mission_image){
            foreach($request->mission_image as $file)
            {
                $this->saveMultipleCommonFrontImage(['image'=>$file,'image_for'=>'about_mission']);
            }
        }
        if($request->mission_title_en){
            $this->saveCommonFrontContent(['title_en'=>$request->mission_title_en,'data_for'=>'about_mission']);
        }
        if($request->mission_title_ar){
            $this->saveCommonFrontContent(['title_ar'=>$request->mission_title_ar,'data_for'=>'about_mission']);
        }
        if($request->mission_content_en){
            $this->saveCommonFrontContent(['content_en'=>$request->mission_content_en,'data_for'=>'about_mission']);
        }
        if($request->mission_content_ar){
            $this->saveCommonFrontContent(['content_ar'=>$request->mission_content_ar,'data_for'=>'about_mission']);
        }


        if($request->vision_image){
            foreach($request->vision_image as $file)
            {
                $this->saveMultipleCommonFrontImage(['image'=>$file,'image_for'=>'about_vision']);
            }
        }
        if($request->vision_title_en){
            $this->saveCommonFrontContent(['title_en'=>$request->vision_title_en,'data_for'=>'about_vision']);
        }
        if($request->vision_title_ar){
            $this->saveCommonFrontContent(['title_ar'=>$request->vision_title_ar,'data_for'=>'about_vision']);
        }
        if($request->vision_content_en){
            $this->saveCommonFrontContent(['content_en'=>$request->vision_content_en,'data_for'=>'about_vision']);
        }
        if($request->vision_content_ar){
            $this->saveCommonFrontContent(['content_ar'=>$request->vision_content_ar,'data_for'=>'about_vision']);
        }

        if($request->client_image){
            foreach($request->client_image as $file)
            {
                $this->saveMultipleCommonFrontImage(['image'=>$file,'image_for'=>'about_client']);
            }
        }
        if($request->client_title_en){
            $this->saveCommonFrontContent(['title_en'=>$request->client_title_en,'data_for'=>'about_client']);
        }
        if($request->client_title_ar){
            $this->saveCommonFrontContent(['title_ar'=>$request->client_title_ar,'data_for'=>'about_client']);
        }
        if($request->client_content_en){
            $this->saveCommonFrontContent(['content_en'=>$request->client_content_en,'data_for'=>'about_client']);
        }
        if($request->client_content_ar){
            $this->saveCommonFrontContent(['content_ar'=>$request->client_content_ar,'data_for'=>'about_client']);
        }

        return ok('About Setting Updated'); 
     }




     public function getContactUsList()
     {
          return ok(ContactUs::orderBy('id', 'DESC')->get());

     }

     public function getContactUsById($id)
     {
        $contactus=ContactUs::find($id);
        if(!isset($contactus)){
            return error(["message"=>"data not found"]);
        }
        return ok($contactus);
     }

     public function deleteContactUsById($id)
     {
        $contactus=ContactUs::find($id);
        if(!isset($contactus)){
            return error(["message"=>"data not found"]);
        }
        $contactus->delete();
        return ok("Deleted Successfully!");
     }

     public function saveContactUs(Request $request)
     {
         ContactUs::create($request->all());
         return ok('Your message sent');
     }


     public function saveFooterData(Request $request)
     {
         $this->saveCommonFrontSocialLink(['key'=>'footer_facebook','value'=>$request->footer_facebook]);
         $this->saveCommonFrontSocialLink(['key'=>'footer_twitter','value'=>$request->footer_twitter]);
         $this->saveCommonFrontSocialLink(['key'=>'footer_linkedin','value'=>$request->footer_linkedin]);
         $content = $request->except('footer_facebook','footer_twitter','footer_linkedin');
         
         if($request->title_en){
            $this->saveCommonFrontContent(['title_en'=>$request->title_en,'data_for'=>'footer_section']);
        }

        if($request->title_ar){
            $this->saveCommonFrontContent(['title_ar'=>$request->title_ar,'data_for'=>'footer_section']);
        }

        if($request->content_en){
            $this->saveCommonFrontContent(['content_en'=>$request->content_en,'data_for'=>'footer_section']);
        }
        
        if($request->content_ar){
            $this->saveCommonFrontContent(['content_ar'=>$request->content_ar,'data_for'=>'footer_section']);
        }

        return ok('saved footer content');
     }

     public function getFooterData()
     {
        $data = $this->getCommonContent('footer_section');
        $data['footer_facebook'] = $this->getCommonSocialLink('footer_facebook');
        $data['footer_twitter'] = $this->getCommonSocialLink('footer_twitter');
        $data['footer_linkedin'] = $this->getCommonSocialLink('footer_linkedin');
        return ok($data);
     }



     //home methods
     public function getHomeData()
     {

     }

     public function saveHomeFeatureInstitueSection(Request $request)
     {
         return $request;
     }

     public function saveHomeSliderSection(Type $var = null)
     {
         # code...
     }

     public function saveContactDetail(Request $request)
    {

        if($request->call_title_en){
            $this->saveCommonFrontContent(['title_en'=>$request->call_title_en,'data_for'=>'call']);
        }

        if($request->call_title_ar){
            $this->saveCommonFrontContent(['title_ar'=>$request->call_title_ar,'data_for'=>'call']);
        }

        if($request->call_content_en){
            $this->saveCommonFrontContent(['content_en'=>$request->call_content_en,'data_for'=>'call']);
        }
        
        if($request->call_content_ar){
            $this->saveCommonFrontContent(['content_ar'=>$request->call_content_ar,'data_for'=>'call']);
        }

        if($request->location_title_en){
            $this->saveCommonFrontContent(['title_en'=>$request->location_title_en,'data_for'=>'location']);
        }

        if($request->location_title_ar){
            $this->saveCommonFrontContent(['title_ar'=>$request->location_title_ar,'data_for'=>'location']);
        }

        if($request->location_content_en){
            $this->saveCommonFrontContent(['content_en'=>$request->location_content_en,'data_for'=>'location']);
        }
        
        if($request->location_content_ar){
            $this->saveCommonFrontContent(['content_ar'=>$request->location_content_ar,'data_for'=>'location']);
        }

        if($request->address_title_en){
            $this->saveCommonFrontContent(['title_en'=>$request->address_title_en,'data_for'=>'address']);
        }

        if($request->address_title_ar){
            $this->saveCommonFrontContent(['title_ar'=>$request->address_title_ar,'data_for'=>'address']);
        }

        if($request->address_content_en){
            $this->saveCommonFrontContent(['content_en'=>$request->address_content_en,'data_for'=>'address']);
        }
        
        if($request->address_content_ar){
            $this->saveCommonFrontContent(['content_ar'=>$request->address_content_ar,'data_for'=>'address']);
        }

        return ok('Contact Detail has been updated');
    }

    public function getContactDetailData()
     {
        $data=[]; 
        $data['call'] = $this->getCommonContent('call');
        $data['location'] = $this->getCommonContent('location');
        $data['address'] = $this->getCommonContent('address');
        return ok($data);
     }

     public function saveCopyRightData(Request $request)
     {
        if($request->title_en){
            $this->saveCommonFrontContent(['title_en'=>$request->title_en,'data_for'=>'copyright']);
        }

        if($request->title_ar){
            $this->saveCommonFrontContent(['title_ar'=>$request->title_ar,'data_for'=>'copyright']);
        }

        if($request->content_en){
            $this->saveCommonFrontContent(['content_en'=>$request->content_en,'data_for'=>'copyright']);
        }
        
        if($request->content_ar){
            $this->saveCommonFrontContent(['content_ar'=>$request->content_ar,'data_for'=>'copyright']);
        }

        return ok('Copyright data updated');
     }

     public function getCopyRightData()
     {
        $data = $this->getCommonContent('copyright');
        return ok($data);
     }

     public function saveInstaData(Request $request)
     {
        if($request->image){
            foreach($request->image as $file)
            {
                $this->saveMultipleCommonFrontImage(['image'=>$file,'image_for'=>'insta']);
            }
        }

        if($request->title_en){
            $this->saveCommonFrontContent(['title_en'=>$request->title_en,'data_for'=>'insta']);
        }

        if($request->title_ar){
            $this->saveCommonFrontContent(['title_ar'=>$request->title_ar,'data_for'=>'insta']);
        }

        if($request->content_en){
            $this->saveCommonFrontContent(['content_en'=>$request->content_en,'data_for'=>'insta']);
        }
        
        if($request->content_ar){
            $this->saveCommonFrontContent(['content_ar'=>$request->content_ar,'data_for'=>'insta']);
        }

        return ok('Instagram Gallery Data Updated');
     }

     public function getInstaData()
     {
        $data = $this->getCommonContent('insta');
        $data['image'] = $this->getMultipleCommonFrontImage('insta');
        return ok($data);
     }

     public function filterContact(Request $request)
    {
        $contact = ContactUs::where('name', "like", "%" . $request->name . "%");
        if($request->email):
            $contact=$contact->where('email', "like", "%" . $request->email . "%");
        endif;
        return ok($contact->get());
    }

    public function getSliderData()
    {
        $data=[];
        $data['home_slider_content'] = $this->getCommonContent('home-slider');
        $data['home_slider_content']['images'] = $this->getMultipleCommonFrontImage('home-slider');
        $data['home_institutes'] = $this->getHomeInstiContent('insti');
        $feature_content =  HomeInstituteSection::where('data_for','like','feature%')->get();

        for($j=0;$j<count($feature_content);$j++){
            $data['feature'][$j] = $this->getHomeInstiContent('feature'.$j.'');
        }
        return ok($data);
    }


    public function saveSliderData(Request $request)
     {
        if($request->home_image){
            foreach($request->home_image as $home_file)
            {
                $this->saveMultipleCommonFrontImage(['image'=>$home_file,'image_for'=>'home-slider']);
            }
        }

        if($request->home_content_en){
            $this->saveCommonFrontContent(['content_en'=>$request->home_content_en,'data_for'=>'home-slider']);
        }
        
        if($request->home_content_ar){
            $this->saveCommonFrontContent(['content_ar'=>$request->home_content_ar,'data_for'=>'home-slider']);
        }

        if($request->insti_title_en){
            $this->saveHomeInstiContent(['title_en'=>$request->insti_title_en,'data_for'=>'insti']);
        }

        if($request->insti_title_ar){
            $this->saveHomeInstiContent(['title_ar'=>$request->insti_title_ar,'data_for'=>'insti']);
        }

        if($request->insti_content_en){
            $this->saveHomeInstiContent(['content_en'=>$request->insti_content_en,'data_for'=>'insti']);
        }

        if($request->insti_content_ar){
            $this->saveHomeInstiContent(['content_ar'=>$request->insti_content_ar,'data_for'=>'insti']);
        }

        if($request->insti_tagline_en){
            $this->saveHomeInstiContent(['tagline_en'=>$request->insti_tagline_en,'data_for'=>'insti']);
        }

        if($request->insti_tagline_ar){
            $this->saveHomeInstiContent(['tagline_ar'=>$request->insti_tagline_ar,'data_for'=>'insti']);
        }

        for($j=0;$j<count($request->feature_title_en);$j++){

            if($request->feature_image[$j]){
                $this->saveCommonFrontImage(['image'=>$request->feature_image[$j],'image_for'=>'feature'.$j.'']);
            }

            if($request->feature_title_en[$j]){
                $this->saveHomeInstiContent(['title_en'=>$request->feature_title_en[$j],'data_for'=>'feature'.$j.'']);
            }

            if($request->feature_title_ar[$j]){
                $this->saveHomeInstiContent(['title_ar'=>$request->feature_title_ar[$j],'data_for'=>'feature'.$j.'']);
            }

            if($request->feature_tagline_en[$j]){
                $this->saveHomeInstiContent(['tagline_en'=>$request->feature_tagline_en[$j],'data_for'=>'feature'.$j.'']);
            }

            if($request->feature_tagline_ar[$j]){
                $this->saveHomeInstiContent(['tagline_ar'=>$request->feature_tagline_ar[$j],'data_for'=>'feature'.$j.'']);
            }
        }

        return ok('Home Data Updated');
     }

}
