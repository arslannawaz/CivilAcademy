<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\CommonFrontContent;
use App\CommonFrontImage;
use App\CommonFrontSocialLink;
use App\HomeInstituteSection;
use Exception;

class CommonController extends Controller
{



    public function getCommonFrontImage($image_for)
    {
        $images = CommonFrontImage::where('image_for',$image_for)->first();
        if($images)
            return $images;
        else
            return [];
    }

    public function getMultipleCommonFrontImage($image_for)
    {
        $images = CommonFrontImage::where('image_for',$image_for)->get();
        if($images)
            return $images;
        else
            return [];
    }

    public function getCommonSocialLink($key)
    {
          $obj = CommonFrontSocialLink::where('key',$key)->first();
          if($obj)
            return $obj;
          else
            return [];
    }

    public function getCommonContent($data_for)
    {

        $data['title_en'] = '';
        $data['title_ar'] = '';
        $data['content_en'] = '';
        $data['content_ar'] = '';
        $header_content =  CommonFrontContent::where('data_for',$data_for)->first();
        if($header_content)
        {
            $data['title_en'] = $header_content->title_en;
            $data['title_ar'] = $header_content->title_ar;
            $data['content_en'] = $header_content->content_en;
            $data['content_ar'] = $header_content->content_ar;
        }

        return $data;
    }


    // save common content data
    public function saveCommonFrontContent($data)
    {

        $setting = CommonFrontContent::where('data_for',$data['data_for'])->first();
        if($setting)
        {
            $setting->update($data);
        }
        else
        {
            $setting = new CommonFrontContent();
            $setting->create($data);
        }
    }

        // save common content data
    public function saveCommonFrontImage($data)
    {
       $setting = CommonFrontImage::where('image_for',$data['image_for'])->first();
       if($setting)
       {
           $setting->update($data);
       }
       else
       {
           $setting = new CommonFrontImage();
           $setting->create($data);
       }
    }

    public function saveMultipleCommonFrontImage($data)
    {
        $setting = new CommonFrontImage();
        $setting->create($data);
    }

    // save common content data
    public function saveCommonFrontSocialLink($data)
    {
        $setting = CommonFrontSocialLink::where('key',$data['key'])->first();
        if($setting)
        {
            $setting->update($data);
        }
        else
        {
            $setting = new CommonFrontSocialLink();
            $setting->create($data);
        }
    }

    public function saveHomeInstiContent($data)
    {

        $setting = HomeInstituteSection::where('data_for',$data['data_for'])->first();
        if($setting)
        {
            $setting->update($data);
        }
        else
        {
            $setting = new HomeInstituteSection();
            $setting->create($data);
        }
    }

    public function getHomeInstiContent($data_for)
    {

        $data['title_en'] = '';
        $data['title_ar'] = '';
        $data['content_en'] = '';
        $data['content_ar'] = '';
        $data['tagline_en'] = '';
        $data['tagline_ar'] = '';
        $data['image'] = '';
        $image = CommonFrontImage::where('image_for',$data_for)->first();
        if($image){
            $data['image']=$image->image;
        }
        $header_content =  HomeInstituteSection::where('data_for',$data_for)->first();
        if($header_content)
        {
            $data['title_en'] = $header_content->title_en;
            $data['title_ar'] = $header_content->title_ar;
            $data['content_en'] = $header_content->content_en;
            $data['content_ar'] = $header_content->content_ar;
            $data['tagline_en']=$header_content->tagline_en;
            $data['tagline_ar']=$header_content->tagline_ar;
        }
        return $data;
    }
}
