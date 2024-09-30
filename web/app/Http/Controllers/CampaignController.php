<?php

namespace App\Http\Controllers;

use App\Models\Session;
use App\Models\Template;
use Illuminate\Http\Request;

class CampaignController extends Controller
{

    public function CampaignDetail(Request $request){
        try {
            $shop = getShop($request->get('shopifySession'));
            if ($shop) {
                $template=Template::where('shop_id',$shop->id)->where('templateCode','order')->with('has_sender_profile')->first();
                if($template){
                    $data = [
                        'data' => $template,
                        'success' => true
                    ];
                }else{
                    $data = [
                        'message' => 'Template Not Found',
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


    public function Campaigns(Request $request){
        try {
            $shop = getShop($request->get('shopifySession'));
            if ($shop) {
                $session = Session::with('templates')->where('id', $shop->id)->first();
                if ($session) {
                    $data = [
                        'data' => $session,
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
    public function UpdateCampaignStatus(Request $request){
        try {
            $shop = getShop($request->get('shopifySession'));
            if ($shop) {

                if($request->status=='order_create'){
                    $shop->order_create_enable=$request->value;
                }elseif ($request->status=='order_fulfill'){
                    $shop->order_fulfill_enable=$request->value;
                }
                elseif ($request->status=='order_cancel'){
                    $shop->order_cancel_enable=$request->value;
                }
                elseif ($request->status=='order_update'){
                    $shop->order_update_enable=$request->value;
                }
                $shop->save();
                $data = [
                    'message' => 'Status Updated Successfully',
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
}
