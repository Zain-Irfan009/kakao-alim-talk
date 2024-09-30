<?php

namespace App\Http\Controllers;

use App\Models\SenderProfile;
use App\Models\Setting;
use App\Models\Template;
use Illuminate\Http\Request;

class SettingController extends Controller
{

    public function AddKakaoSetting(Request $request){
        try {
            $shop = getShop($request->get('shopifySession'));
            if ($shop) {
                $setting=new Setting();
                $setting->type='Custom';
                $setting->app_key=$request->app_key;
                $setting->secret_key=$request->secret_key;
                $setting->shop_id=$shop->id;
                $setting->save();

                $shop->setting_id=$setting->id;
                $shop->save();

                $sender_profile_controller=new SenderProfileController();
                $syncResult = $sender_profile_controller->SyncSenderProfile($request);

                if (!$syncResult['success']) {
                    $setting->delete();
                    $default_setting=Setting::where('type','default')->first();
                    if($default_setting) {
                        $shop->setting_id = $default_setting->id;
                        $shop->save();
                    }
                    throw new \Exception($syncResult['error']); // Stop execution if there is an error
                }
                $get_sender_profile=SenderProfile::where('setting_id',$setting->id)->first();
                if($get_sender_profile){
                    $get_sender_profile->selected=1;
                    $get_sender_profile->save();
                }

                $template_controller=new TemplateController();
                $syncTemplateResult=$template_controller->SyncTemplates($request);
                if (!$syncTemplateResult['success']) {
                    $get_sender_profile=SenderProfile::where('setting_id',$setting->id)->first();
                    if($get_sender_profile){
                        $get_sender_profile->selected=0;
                        $get_sender_profile->save();
                    }
                    $setting->delete();
                    $default_setting=Setting::where('type','default')->first();
                    if($default_setting) {
                        $shop->setting_id = $default_setting->id;
                        $shop->save();
                    }
                    throw new \Exception($syncTemplateResult['error']); // Stop execution if there is an error
                }

                $data = [
                    'message' =>'Setting Save Successfully',
                    'success' => true
                ];
            }
        }catch (\Exception $exception){
            $data = [
                'error' => $exception->getMessage(),
                'success' => false
            ];
        }
        return response()->json($data);
    }
//    public function UpdateKakaoSetting(Request $request){
//        try {
//            $shop = getShop($request->get('shopifySession'));
//            if ($shop) {
//                $setting=Setting::where('id',$request->id)->first();
//                if($setting) {
//                    $setting->type = 'Custom';
//                    $setting->app_key = $request->app_key;
//                    $setting->secret_key = $request->secret_key;
//                    $setting->save();
//
//                    $shop->setting_id=$setting->id;
//                    $shop->save();
//
//                    $sender_profile_controller=new SenderProfileController();
//                    $syncResult = $sender_profile_controller->SyncSenderProfile($request);
//
//                    if (!$syncResult['success']) {
//                        $setting->delete();
//                        $default_setting=Setting::where('type','default')->first();
//                        if($default_setting) {
//                            $shop->setting_id = $default_setting->id;
//                            $shop->save();
//                        }
//                        throw new \Exception($syncResult['error']); // Stop execution if there is an error
//                    }
//                    $get_sender_profile=SenderProfile::where('setting_id',$setting->id)->first();
//                    if($get_sender_profile){
//                        $get_sender_profile->selected=1;
//                        $get_sender_profile->save();
//                    }
//
//                    $template_controller=new TemplateController();
//                    $syncTemplateResult=$template_controller->SyncTemplates($request);
//                    if (!$syncTemplateResult['success']) {
//                        $get_sender_profile=SenderProfile::where('setting_id',$setting->id)->first();
//                        if($get_sender_profile){
//                            $get_sender_profile->selected=0;
//                            $get_sender_profile->save();
//                        }
//                        $setting->delete();
//                        $default_setting=Setting::where('type','default')->first();
//                        if($default_setting) {
//                            $shop->setting_id = $default_setting->id;
//                            $shop->save();
//                        }
//                        throw new \Exception($syncTemplateResult['error']); // Stop execution if there is an error
//                    }
//
//
//
//                    $data = [
//                        'message' => 'Setting Updated Successfully',
//                        'success' => true
//                    ];
//                }
//            }
//        }catch (\Exception $exception){
//            $data = [
//                'error' => $exception->getMessage(),
//                'success' => false
//            ];
//        }
//        return response()->json($data);
//    }
    public function DeleteKakaoSetting(Request $request,$id){
        try {
            $shop = getShop($request->get('shopifySession'));
            if ($shop) {
                $setting=Setting::where('id',$id)->first();
                if($setting) {
                    Template::where('setting_id',$setting->id)->delete();
                    SenderProfile::where('setting_id',$setting->id)->delete();
                    $setting->delete();

                    $setting_data=Setting::where('type','Custom')->first();
                    if($setting_data==null){
                        $default_setting=Setting::where('type','Default')->first();
                        $shop->setting_id=$default_setting->id;
                    }
                    else{
                        $shop->setting_id=$setting_data->id;
                    }
                    $shop->save();

                    $get_sender_profile=SenderProfile::where('setting_id',$shop->setting_id)->first();
                    if($get_sender_profile){
                        $get_sender_profile->selected=1;
                        $get_sender_profile->save();
                    }

                    $data = [
                        'message' => 'Setting Deleted Successfully',
                        'success' => true
                    ];
                }
            }
        }catch (\Exception $exception){
            $data = [
                'error' => $exception->getMessage(),
                'success' => false
            ];
        }
        return response()->json($data);
    }
}
