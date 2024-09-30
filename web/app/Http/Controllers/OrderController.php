<?php

namespace App\Http\Controllers;

use App\Models\LineItem;
use App\Models\Log;
use App\Models\Order;
use App\Models\SenderProfile;
use App\Models\Session;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Shopify\Clients\Rest;

class OrderController extends Controller
{

    public function SyncOrder(Request $request, $next = null)
    {
        $user = getShop($request->get('shopifySession'));
        $session = Session::where('shop', $user->shop)->first();
        $shop = new Rest($session->shop, $session->access_token);
        $result = $shop->get('orders', [], ['limit' => 250,'status'=>'any']);
        $orders = $result->getDecodedBody();

        foreach ($orders['orders'] as $order) {
            $order = json_decode(json_encode($order));

            $this->singleOrder($order, $session);
        }

        $data = [
            'message' => "Order Sync Successfully",
            'data' => $orders

        ];
        return response($data);
    }
    public function singleOrder($order, $shop)
    {



            $newOrder = Order::where('shopify_id', $order->id)->where('shop_id', $shop->id)->first();

            $status = '';
            if ($newOrder == null) {
                $newOrder = new Order();
                $status = 'new_order';
            }else{
                $order_update_date=date_create($order->updated_at)->format('Y-m-d h:i:s');
                if($order_update_date != $newOrder->shopify_updated_at){
                    $status = 'order_update';
                }

            }

            if(count($order->fulfillments) > 0){
                if($newOrder->fulfillments!=null) {
                    $fulfillment_orders = json_decode($newOrder->fulfillments);
                    if (count($fulfillment_orders) != count($order->fulfillments)) {
                        $status = 'order_fulfilled';
                    }
                }else{
                    $status = 'order_fulfilled';
                }

            }

            if ( $order->cancelled_at != null) {
                    $status = 'order_cancel';
            }
            $newOrder->shopify_id = $order->id;
            $newOrder->email = $order->email;
            $newOrder->order_number = $order->name;
            if (isset($order->shipping_address)) {
                $newOrder->shipping_name = $order->shipping_address->name;
                $newOrder->address1 = $order->shipping_address->address1;
                $newOrder->address2 = $order->shipping_address->address2;
                $newOrder->phone = $order->shipping_address->phone;
                $newOrder->city = $order->shipping_address->city;
                $newOrder->zip = $order->shipping_address->zip;
                $newOrder->province = $order->shipping_address->province;
                $newOrder->country = $order->shipping_address->country;
            }

            if (isset($order->billing_address)) {
                $newOrder->billing_shipping_name = $order->billing_address->name;
                $newOrder->billing_address1 = $order->billing_address->address1;
                $newOrder->billing_address2 = $order->billing_address->address2;
                $newOrder->billing_phone = $order->billing_address->phone;
                $newOrder->billing_city = $order->billing_address->city;
                $newOrder->billing_zip = $order->billing_address->zip;
                $newOrder->billing_province = $order->billing_address->province;
                $newOrder->billing_country = $order->billing_address->country;
            }

            $newOrder->financial_status = $order->financial_status;
            $newOrder->fulfillment_status = $order->fulfillment_status;
            if(count($order->fulfillments) > 0) {
                $newOrder->fulfillments = json_encode($order->fulfillments);
            }
            if (isset($order->customer)) {
                $newOrder->first_name = $order->customer->first_name;
                $newOrder->last_name = $order->customer->last_name;
                $newOrder->customer_phone = $order->customer->phone;
                $newOrder->customer_email = $order->customer->email;
                $newOrder->customer_id = $order->customer->id;
            }
            $newOrder->shopify_created_at = date_create($order->created_at)->format('Y-m-d h:i:s');
            $newOrder->shopify_updated_at = date_create($order->updated_at)->format('Y-m-d h:i:s');
            $newOrder->tags = $order->tags;
            $newOrder->note = $order->note;
            if ($order->total_price > 0) {
                $total_price = $order->current_total_price;
            } else {
                $total_price = $order->total_price;
            }
            $newOrder->total_price = $total_price;
            $newOrder->currency = $order->currency;

            if ($order->current_subtotal_price > 0) {
                $sub_total_price = $order->current_subtotal_price;
            } else {
                $sub_total_price = $order->subtotal_price;
            }
            $newOrder->subtotal_price = $sub_total_price;

            $newOrder->total_weight = $order->total_weight;
            $newOrder->taxes_included = $order->taxes_included;
            $newOrder->total_tax = $order->total_tax;
            $newOrder->currency = $order->currency;
            $newOrder->total_discounts = $order->total_discounts;
             if(count($order->refunds) > 0) {
            $newOrder->refunds = json_encode($order->refunds);
              }
            $newOrder->gateway = (count($order->payment_gateway_names) > 0) ? $order->payment_gateway_names[0] : null;
            $newOrder->shop_id = $shop->id;
            $newOrder->save();


            foreach ($order->line_items as $item) {

                $quantity = $item->quantity;
                if (($order->refunds)) {
                    foreach ($order->refunds as $refund_odr) {
                        foreach ($refund_odr->refund_line_items as $refund_item) {
                            if ($refund_item->line_item_id == $item->id) {
                                $quantity = $quantity - $refund_item->quantity;
                            }
                        }
                    }
                }
                if ($quantity > 0) {
                    $new_line = LineItem::where('shopify_id', $item->id)->where('order_id', $newOrder->id)->where('shop_id', $shop->id)->first();
                    if ($new_line == null) {
                        $new_line = new Lineitem();
                    }
                    $new_line->shopify_product_id = $item->product_id;
                    $new_line->shopify_variant_id = $item->variant_id;
                    $new_line->shopify_id = $item->id;
                    $new_line->title = $item->title;
                    $new_line->quantity = $quantity;
                    $new_line->sku = $item->sku;
                    $new_line->variant_title = $item->variant_title;
                    $new_line->title = $item->title;
                    $new_line->vendor = $item->vendor;
                    $new_line->price = $item->price;
                    $new_line->requires_shipping = $item->requires_shipping;
                    $new_line->taxable = $item->taxable;
                    $new_line->name = $item->name;
                    $new_line->properties = json_encode($item->properties, true);
                    $new_line->fulfillable_quantity = $item->fulfillable_quantity;
                    $new_line->fulfillment_status = $item->fulfillment_status;
                    $new_line->order_id = $newOrder->id;
                    $new_line->shop_id = $shop->id;
                    $new_line->shopify_order_id = $order->id;
                    $new_line->save();



                } else {
                    LineItem::where('shopify_id', $item->id)->where('shopify_order_id', $order->id)->delete();
                }

            }


//            if ( $status == 'new_order') {
//                $setting = Setting::where('id', $shop->setting_id)->first();
//                if ($setting){
//                    $sender_profile = SenderProfile::where('setting_id', $setting->id)->where('selected',1)->first();
//                if ($sender_profile) {
//
//                    $url = "https://api-alimtalk.cloud.toast.com/alimtalk/v2.3/appkeys/" . $setting->app_key . "/messages";
//
////                    $requestDate =$newOrder->created_at;
//                    $requestDate = '2024-09-06 22:22:00';
//                    $response = Http::withHeaders([
//                        'Content-Type' => 'application/json;charset=UTF-8',
//                        'X-Secret-Key' => $setting->secret_key,
//                    ])->post($url, [
//                        'senderKey' => $sender_profile->senderKey,
//                        'templateCode' => 'order', // Replace with your actual template code
//                        'requestDate' => $requestDate,
//                        'recipientList' => [
//                            [
//                                'recipientNo' => '+821022523193', // Replace with the actual recipient number (remove leading + and spaces)
//                                'templateParameter' => [
//                                    'name' => $newOrder->shipping_name, // Replace with actual parameter field and data
//                                    'orderid' => $newOrder->order_number,
//                                    'datetime' => $newOrder->created_at,
//                                    'products' => 2,
//                                    'ordertotal' => $newOrder->total_price,
//                                    'paymentmethod' => 'manual',
//                                    'shopname' => $shop->shop,
//                                ],
//
//                            ],
//                        ],
//                    ]);
//                    $body = $response->getBody();
//
//                    $data = json_decode($body, false);
//
//                    if ($data->header && $data->header->isSuccessful == true) {
//
//                        $log = new Log();
//                        $log->shop_id = $shop->id;
//                        $log->sms_type = $status;
////                        $log->shop_id=$shop->id;
//                        $log->save();
//                    }
//                }
//            }
//
//
//            }


        $setting = Setting::where('id', $shop->setting_id)->first();
        $flag=0;
        if ($setting) {

            $sender_profile = SenderProfile::where('setting_id', $setting->id)->where('selected', 1)->first();
            if ($sender_profile) {

                $url = "https://api-alimtalk.cloud.toast.com/alimtalk/v2.3/appkeys/" . $setting->app_key . "/messages";
                //$requestDate =$newOrder->created_at;
                $requestDate = '2024-09-06 22:22:00';
                if ($shop->order_create_enable == 1 && $status == 'new_order') {
                    $response = Http::withHeaders([
                        'Content-Type' => 'application/json;charset=UTF-8',
                        'X-Secret-Key' => $setting->secret_key,
                    ])->post($url, [
                        'senderKey' => $sender_profile->senderKey,
                        'templateCode' => 'shopify001', // Replace with your actual template code
                        'requestDate' => $requestDate,
                        'recipientList' => [
                            [
                                'recipientNo' => '+821022523193', // Replace with the actual recipient number (remove leading + and spaces)
                                'templateParameter' => [
                                    'ordernumber' => $newOrder->order_number,
                                    'totalprice' => $newOrder->total_price,
                                ],

                            ],
                        ],
                    ]);
                    $flag = 1;
                } elseif ($shop->order_cancel_enable == 1 && $status == 'order_cancel') {
                    $refunds_data = json_decode($newOrder->refunds);
                    $refunds_data_get = end($refunds_data);

                    if (isset($refunds_data_get->note)) {

                        $refund_note = $refunds_data_get->note;
                        $response = Http::withHeaders([
                            'Content-Type' => 'application/json;charset=UTF-8',
                            'X-Secret-Key' => $setting->secret_key,
                        ])->post($url, [
                            'senderKey' => $sender_profile->senderKey,
                            'templateCode' => 'shopify003', // Replace with your actual template code
                            'requestDate' => $requestDate,
                            'recipientList' => [
                                [
                                    'recipientNo' => '+821022523193', // Replace with the actual recipient number (remove leading + and spaces)
                                    'templateParameter' => [
                                        'ordernumber' => $newOrder->order_number,
                                        'totalprice' => $newOrder->total_price,
                                        'refunded' => $refund_note,
                                    ],

                                ],
                            ],
                        ]);
                        $flag = 1;
                    }

                } elseif ($shop->order_update_enable == 1 && $status == 'order_update') {

                    if (!$order->fulfillment_status) {
                        $fulfillment_status = 'Unfulfilled';
                    } else {
                        $fulfillment_status = $order->fulfillment_status;
                    }
                    $response = Http::withHeaders([
                        'Content-Type' => 'application/json;charset=UTF-8',
                        'X-Secret-Key' => $setting->secret_key,
                    ])->post($url, [
                        'senderKey' => $sender_profile->senderKey,
                        'templateCode' => 'shopify004', // Replace with your actual template code
                        'requestDate' => $requestDate,
                        'recipientList' => [
                            [
                                'recipientNo' => '+821022523193', // Replace with the actual recipient number (remove leading + and spaces)
                                'templateParameter' => [
                                    'financialstatus' => $newOrder->financial_status,
                                    'fulfillmentstatus' => $fulfillment_status,

                                ],

                            ],
                        ],
                    ]);
                    $flag = 1;

                } elseif ($shop->order_fulfill_enable == 1 && $status == 'order_fulfilled') {

                    $fulfillments_data = json_decode($newOrder->fulfillments);
                    $fulfillments_data_get = end($fulfillments_data);

                    if (isset($fulfillments_data_get->tracking_company) && isset($fulfillments_data_get->tracking_number)) {
                        $tracking_company = $fulfillments_data_get->tracking_company;
                        $tracking_number = $fulfillments_data_get->tracking_number;

                        $response = Http::withHeaders([
                            'Content-Type' => 'application/json;charset=UTF-8',
                            'X-Secret-Key' => $setting->secret_key,
                        ])->post($url, [
                            'senderKey' => $sender_profile->senderKey,
                            'templateCode' => 'shopify002', // Replace with your actual template code
                            'requestDate' => $requestDate,
                            'recipientList' => [
                                [
                                    'recipientNo' => '+821022523193', // Replace with the actual recipient number (remove leading + and spaces)
                                    'templateParameter' => [
                                        'ordernumber' => $newOrder->order_number,
                                        'couriername' => $tracking_company,
                                        'trackingnumber' => $tracking_number,
                                    ],

                                ],
                            ],
                        ]);
                        $flag = 1;
                    }
                }
                if ($flag) {
                    $body = $response->getBody();

                    $data = json_decode($body, false);

                    if ($data->header && $data->header->isSuccessful == true) {

                        $log = new Log();
                        $log->shop_id = $shop->id;
                        $log->sms_type = $status;
                        $log->save();
                    }
                }
            }
        }

    }
}
