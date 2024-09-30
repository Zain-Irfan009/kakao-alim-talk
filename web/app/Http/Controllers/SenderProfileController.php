<?php

namespace App\Http\Controllers;

use App\Models\SenderProfile;
use App\Models\Setting;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SenderProfileController extends Controller
{
    public function SyncSenderProfile(Request $request){

        try {
            $shop = getShop($request->get('shopifySession'));
            if ($shop) {
            $setting = Setting::where('id',$shop->setting_id)->first();
            if ($setting) {
                $client = new Client();
                $url = "https://api-alimtalk.cloud.toast.com/alimtalk/v2.3/appkeys/".$setting->app_key."/senders";
                $response = $client->request('GET', $url, [
                    'headers' => [
                        'Content-Type' => 'application/json;charset=UTF-8',
                        'X-Secret-Key' => $setting->secret_key,
                    ],
                ]);
                $body = $response->getBody();

                $data = json_decode($body, false);


                if ($data->header && $data->header->isSuccessful == true) {
                    if (isset($data->senders)) {
                        foreach ($data->senders as $sender) {
                            $sender_profile=SenderProfile::where('setting_id',$setting->id)->where('senderKey',$sender->senderKey)->first();
                            if($sender_profile==null) {
                                $sender_profile = new SenderProfile();
                            }
                            $sender_profile->plusFriendId = $sender->plusFriendId;
                            $sender_profile->senderKey = $sender->senderKey;
                            $sender_profile->categoryCode = $sender->categoryCode;
                            $sender_profile->status = $sender->status;
                            $sender_profile->statusName = $sender->statusName;
                            $sender_profile->kakaoStatus = $sender->kakaoStatus;
                            $sender_profile->kakaoStatusName = $sender->kakaoStatusName;
                            $sender_profile->kakaoProfileStatus = $sender->kakaoProfileStatus;
                            $sender_profile->kakaoProfileStatusName = $sender->kakaoProfileStatusName;
                            $sender_profile->profileSpamLevel = $sender->profileSpamLevel;
                            $sender_profile->profileMessageSpamLevel = $sender->profileMessageSpamLevel;
                            $sender_profile->dormant = $sender->dormant;
                            $sender_profile->block = $sender->block;
                            $sender_profile->alimtalk = json_encode($sender->alimtalk);
                            $sender_profile->friendtalk = json_encode($sender->friendtalk);
                            $sender_profile->createDate = $sender->createDate;
                            $sender_profile->initialUserRestriction = $sender->initialUserRestriction;
                            $sender_profile->setting_id = $setting->id;
                            $sender_profile->save();
                        }
                    }
                    return [
                        'message' => 'Sender Profile Saved Successfully',
                        'success' => true
                    ];
                }else{
                    return [
                        'error' => $data->header->resultMessage,
                        'success' => false
                    ];
                }
            }

            }
        }catch (\Exception $exception){
            return [
                'error' => $exception->getMessage(),
                'success' => false
            ];
        }


    }

    public function SenderProfile(Request $request){
        try {
            $shop = getShop($request->get('shopifySession'));
            if ($shop) {
                $setting = Setting::where('id',$shop->setting_id)->first();
                if ($setting) {
                $sender_profiles=SenderProfile::where('setting_id',$setting->id)->get();

                    $data = [
                        'data' => $sender_profiles,
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


    public function ChangeSenderProfile(Request $request,$id){
        try {
            $shop = getShop($request->get('shopifySession'));
            if ($shop) {
                $setting = Setting::where('id',$shop->setting_id)->first();
                if ($setting) {
                    SenderProfile::where('setting_id',$setting->id)->update(['selected'=>0]);

                    $sender_profile=SenderProfile::where('id',$request->id)->first();
                    if($sender_profile){
                        $sender_profile->selected=1;
                        $sender_profile->save();
                    }
                    $data = [
                        'message' => 'Sender Profile Change Successfully',
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
