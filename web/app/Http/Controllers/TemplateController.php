<?php

namespace App\Http\Controllers;

use App\Models\SenderProfile;
use App\Models\Setting;
use App\Models\Template;
use GuzzleHttp\Client;
use Illuminate\Http\Request;


class TemplateController extends Controller
{
    public function SyncTemplates(Request $request){
        try {
            $shop = getShop($request->get('shopifySession'));
            if ($shop) {
                $setting = Setting::where('id',$shop->setting_id)->first();

                if ($setting) {
                    $sender_profile = SenderProfile::where('setting_id', $shop->setting_id)->where('selected',1)->first();
                    if ($sender_profile) {
                        $client = new Client();

                        $url = "https://api-alimtalk.cloud.toast.com/alimtalk/v2.3/appkeys/".$setting->app_key."/senders/" . $sender_profile->senderKey . "/templates";
                        $response = $client->request('GET', $url, [
                            'headers' => [
                                'Content-Type' => 'application/json;charset=UTF-8',
                                'X-Secret-Key' => $setting->secret_key,
                            ],
                        ]);
                        $body = $response->getBody();

                        $data = json_decode($body, false);

                        if ($data->header && $data->header->isSuccessful == true) {

                            if (isset($data->templateListResponse)) {
                                foreach ($data->templateListResponse->templates   as $index=> $template) {

                                    $db_template=Template::where('shop_id',$shop->id)->where('sender_profile_id',$sender_profile->id)->where('templateCode',$template->templateCode)->first();
                                    if($db_template==null) {
                                        $db_template = new Template();
                                    }
                                    $db_template->plusFriendId = $template->plusFriendId;
                                    $db_template->senderKey = $template->senderKey;
                                    $db_template->plusFriendType = $template->plusFriendType;
                                    $db_template->templateCode = $template->templateCode;
                                    $db_template->kakaoTemplateCode = $template->kakaoTemplateCode;
                                    $db_template->templateName = $template->templateName;
                                    $db_template->templateMessageType = $template->templateMessageType;
                                    $db_template->templateEmphasizeType = $template->templateEmphasizeType;
                                    $db_template->templateContent = $template->templateContent;
                                    $db_template->templateExtra = $template->templateExtra;
                                    $db_template->templateAd = $template->templateAd;
                                    $db_template->templateTitle = $template->templateTitle;
                                    $db_template->templateSubtitle = $template->templateSubtitle;
                                    $db_template->templateHeader = $template->templateHeader;
                                    $db_template->templateItem = $template->templateItem;
                                    $db_template->templateItemHighlight = $template->templateItemHighlight;
                                    $db_template->templateRepresentLink = $template->templateRepresentLink;
                                    $db_template->templateImageName = $template->templateImageName;
                                    $db_template->templateImageUrl = $template->templateImageUrl;
                                    $db_template->buttons = json_encode($template->buttons);
                                    $db_template->quickReplies = json_encode($template->quickReplies);
                                    $db_template->comments = json_encode($template->comments);
                                    $db_template->status = $template->status;
                                    $db_template->statusName = $template->statusName;
                                    $db_template->securityFlag = $template->securityFlag;
                                    $db_template->categoryCode = $template->categoryCode;
                                    $db_template->createDate = $template->createDate;
                                    $db_template->updateDate = $template->updateDate;
                                    $db_template->shop_id = $shop->id;
                                    $db_template->sender_profile_id = $sender_profile->id;
                                    $db_template->setting_id = $setting->id;
                                    $db_template->save();
                                }
                            }

                            return [
                                'message' => 'Template Saved Successfully',
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
            }
        }catch (\Exception $exception){
            return  [
                'error' => $exception->getMessage().' '.$exception->getLine(),
                'success' => false
            ];
        }

    }


    public function RegisterTemplate(Request $request){

        try {
            $shop = getShop($request->get('shopifySession'));
            if ($shop) {
                $setting = Setting::where('id',$shop->setting_id)->first();
                if ($setting) {
                    $sender_profile = SenderProfile::where('setting_id', $shop->setting_id)->where('selected',1)->first();
                    if ($sender_profile){
                        $client = new Client();
                    $url = "https://api-alimtalk.cloud.toast.com/alimtalk/v2.3/appkeys/" . $setting->app_key . "/senders/" . $sender_profile->senderKey . "/templates";

                    $dataPayload = [
                        'templateCode' => '1002', // Replace with actual template code (up to 20 characters)
                        'templateName' => 'Testing1', // Replace with actual template name (up to 150 characters)
                        'templateContent' => 'Testing the sms', // Replace with actual template content (up to 1000 characters)

                    ];


                    $response = $client->request('POST', $url, [
                        'headers' => [
                            'Content-Type' => 'application/json;charset=UTF-8',
                            'X-Secret-Key' => $setting->secret_key,
                        ],
                        'json' => $dataPayload,
                    ]);

                    $body = $response->getBody();

                        $data = json_decode($body, false);
                        if ($data->header && $data->header->isSuccessful == true) {

                            $data = [
                                'message' => 'Template Created Successfully',
                                'success' => true
                            ];

                        }else{
                            $data = [
                                'message' => $data->header->resultMessage,
                                'success' => false
                            ];
                        }
                        return response()->json($data);
                }
                }
            }
        }catch (\Exception $exception){
            $data = [
                'error' => $exception->getMessage().' '.$exception->getLine(),
                'success' => false
            ];
        }
        return response()->json($data);
    }
}
