<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Http;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;


    public function index()
    {
        $appkey = 'IKCBnK9hJbdNYwiB'; // Replace this with your actual appkey
        $secretKey = 'muYaXW0s'; // Replace this with your actual secret key

        $client = new Client();
//        $url = "https://api-alimtalk.cloud.toast.com/alimtalk/v2.3/appkeys/IKCBnK9hJbdNYwiB/senders";
        $url = "https://api-alimtalk.cloud.toast.com/alimtalk/v2.3/appkeys/IKCBnK9hJbdNYwiB/messages";
//        $url = "https://api-alimtalk.cloud.toast.com/alimtalk/v2.3/appkeys/IKCBnK9hJbdNYwiB/senders/f0f93ba3ec6cc8045d4184338c6adae566e40689/templates";
//        $url = "https://api-alimtalk.cloud.toast.com/alimtalk/v2.3/appkeys/IKCBnK9hJbdNYwiB/raw-messages";

        //get request
        try {

            $queryParams = [
                'startRequestDate' => '2024-08-04 00:00', // Adjust the format if needed
                'endRequestDate' => '2024-09-02 23:59',   // Adjust the format if needed
            ];

            // Make the GET request with headers and query parameters
            $response = Http::withHeaders([
                'Content-Type' => 'application/json;charset=UTF-8',
                'X-Secret-Key' => $secretKey,
            ])->get($url, $queryParams);
//            $response = $client->request('GET', $url, [
//                'headers' => [
//                    'Content-Type' => 'application/json;charset=UTF-8',
//                    'X-Secret-Key' => $secretKey,
//                ]
//            ]);

            $body = $response->getBody();
            $data = json_decode($body, true);

            return response()->json($data);



            $dataPayload = [
                'senderKey' => 'f0f93ba3ec6cc8045d4184338c6adae566e40689', // Replace with your actual sender key
//                'templateCode' => '{템플릿 코드}', // Replace with your actual template code
////                'requestDate' => '2018-10-01 00:00', // Replace with the desired request date
//                'recipientList' => [
//                    [
//                        'recipientNo' => '+923344188213', // Replace with the actual recipient number
//                        'content' => '{내용}', // Replace with the actual content
//                        'buttons' => [
//                            [
//                                'ordering' => '{버튼 순서}', // Replace with the actual button order
//                                'type' => '{버튼 타입}', // Replace with the actual button type
//                                'name' => '{버튼 이름}', // Replace with the actual button name
//                                'linkMo' => '{모바일 웹 링크}' // Replace with the actual mobile web link
//                            ]
//                        ]
//                    ]
//                ]
            ];
            //post request
            $response = $client->request('POST', $url, [
                'headers' => [
                    'Content-Type' => 'application/json;charset=UTF-8',
                    'X-Secret-Key' => $secretKey,
                ],
                'json' => $dataPayload,
            ]);

            $body = $response->getBody();
            $data = json_decode($body, true);
            return response()->json($data);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function templateRegister(){

        $appkey = 'IKCBnK9hJbdNYwiB'; // Replace this with your actual appkey
        $secretKey = 'muYaXW0s'; // Replace this with your actual secret key

        $client = new Client();
        $url = "https://api-alimtalk.cloud.toast.com/alimtalk/v2.3/appkeys/IKCBnK9hJbdNYwiB/senders/bcc4f68b73b642629cfe73ff0c55d1752d598cbd/templates";


        $dataPayload = [
            'templateCode' => '1001', // Replace with actual template code (up to 20 characters)
            'templateName' => 'Testing', // Replace with actual template name (up to 150 characters)
            'templateContent' => 'Testing the sms', // Replace with actual template content (up to 1000 characters)

            // Optional fields, include based on your requirements
//            'templateMessageType' => 'BA', // Optional: Type of template message (default: Basic)
//            'templateEmphasizeType' => 'TEXT', // Optional: Type of emphasized template
//            'templateTitle' => 'YourTemplateTitle', // Required if templateEmphasizeType is TEXT
//            'templateSubtitle' => 'YourTemplateSubtitle', // Required if templateEmphasizeType is TEXT

//            'buttons' => [ // Optional: Buttons (up to 5)
//                [
//                    'ordering' => 1, // Button sequence (1~5)
//                    'type' => 'WL', // Button type (WL: Web link, AL: App link, etc.)
//                    'name' => 'ButtonName', // Button name (up to 14 characters)
//                    'linkMo' => 'https://example.com', // Mobile web link (up to 500 characters)
//                ]
//            ],
//
//            'quickReplies' => [ // Optional: Quick reply list (up to 5)
//                [
//                    'ordering' => 1, // Quick reply order
//                    'type' => 'WL', // Quick reply type (WL: Web link, AL: App link, etc.)
//                    'name' => 'QuickReplyName', // Quick reply name (up to 14 characters)
//                    'linkMo' => 'https://example.com', // Mobile web link (up to 500 characters)
//                ]
//            ]


        ];

//                    $response = $client->request('GET', $url, [
//                'headers' => [
//                    'Content-Type' => 'application/json;charset=UTF-8',
//                    'X-Secret-Key' => $secretKey,
//                ]
//            ]);
//
//            $body = $response->getBody();
//            $data = json_decode($body, true);
//
//            return response()->json($data);

        $response = $client->request('GET', $url, [
            'headers' => [
                'Content-Type' => 'application/json;charset=UTF-8',
                'X-Secret-Key' => $secretKey,
            ],
//            'json' => $dataPayload,
        ]);

        $body = $response->getBody();

        $data = json_decode($body, true);
        return response()->json($data);
    }


    public function sendMessage()
    {
        $url = "https://api-alimtalk.cloud.toast.com/alimtalk/v2.3/appkeys/IKCBnK9hJbdNYwiB/messages";

        $appkey = 'IKCBnK9hJbdNYwiB'; // Replace this with your actual appkey
        $secretKey = 'muYaXW0s';

        // Replace with your actual senderKey (must be 40 characters long)
        $senderKey = '1e20c4c2575b59fef47fa2c271d6e4dbc93b4c8f';

        // Set the requestDate to the current date and time or within the next 60 days
//        $requestDate = now()->addDays(1)->format('Y-m-d H:i:s'); // 1 day in the future
$requestDate='2024-09-04 22:22:00';
        $response = Http::withHeaders([
            'Content-Type' => 'application/json;charset=UTF-8',
            'X-Secret-Key' => $secretKey,
        ])->post($url, [
            'senderKey' => $senderKey,
            'templateCode' => 'order', // Replace with your actual template code
            'requestDate' => $requestDate,
            'recipientList' => [
                [
//                    'recipientNo' => '+82443255853', // Replace with the actual recipient number (remove leading + and spaces)
                    'recipientNo' => '+821022523193', // Replace with the actual recipient number (remove leading + and spaces)
                    'templateParameter' => [
                        'name' => 'John Doe', // Replace with actual parameter field and data
                        'orderid'=>2,
                        'datetime'=>'2024-09-04 22:22:00',
                        'products'=>2,
                        'ordertotal'=>20,
                        'paymentmethod'=>'manual',
                        'shopname'=>'kakao',

//                    'product_name'=>'test',
//                        'product_url'=>'www.google.com'
//                    'products'=>2,
                    ],
                ],
            ],
        ]);

        if ($response->successful()) {
            // Handle successful response
            return $response->json(); // or $response->body(), etc.
        } else {
            // Handle error
            return $response->throw()->json(); // or log the error
        }
    }




    public function sendRawMessage()
    {
        $url = "https://api-alimtalk.cloud.toast.com/alimtalk/v2.3/appkeys/IKCBnK9hJbdNYwiB/raw-messages";

        $appkey = 'IKCBnK9hJbdNYwiB'; // Replace this with your actual appkey
        $secretKey = 'muYaXW0s'; // Replace this with your actual secret key

        $response = Http::withHeaders([
            'Content-Type' => 'application/json;charset=UTF-8',
            'X-Secret-Key' => $secretKey,
        ])->post($url, [
            'senderKey' => '1e20c4c2575b59fef47fa2c271d6e4dbc93b4c8f', // Replace with your actual sender key
            'templateCode' => 'Bis_220306', // Replace with your actual template code
            'requestDate' => '2024-09-01 00:00', // Ensure this is set to a valid date
            'recipientList' => [
                [
                    'recipientNo' => '+82105557458', // Replace with the actual recipient number
                    'content' => '32232332', // Replace with the actual message content
                    'buttons' => [
                        [
                            'ordering' => '{버튼 순서}', // Replace with button order (e.g., 1, 2, etc.)
                            'type' => '{버튼 타입}', // Replace with button type (e.g., WL for web link, AL for app link)
                            'name' => '{버튼 이름}', // Replace with the button name
                            'linkMo' => '{모바일 웹 링크}', // Replace with the mobile web link
                        ],
                    ],
                ],
            ],
        ]);

        if ($response->successful()) {
            // Handle successful response
            return $response->json(); // or $response->body(), etc.
        } else {
            // Handle error
            return $response->throw()->json(); // or log the error
        }
    }


    public function getSender()
    {
        $client = new Client();
        $url = "https://api-alimtalk.cloud.toast.com/alimtalk/v2.3/appkeys/IKCBnK9hJbdNYwiB/senders/1e20c4c2575b59fef47fa2c271d6e4dbc93b4c8f";

        $appkey = 'IKCBnK9hJbdNYwiB'; // Replace this with your actual appkey
        $secretKey = 'muYaXW0s'; // Replace this with your actual secret key

            $response = $client->request('GET', $url, [
                'headers' => [
                    'Content-Type' => 'application/json;charset=UTF-8',
                    'X-Secret-Key' => $secretKey,
                ]
            ]);

            $body = $response->getBody();
            $data = json_decode($body, true);
dd($data);
            return response()->json($data);

        if ($response->successful()) {
            // Handle successful response
            return $response->json(); // or $response->body(), etc.
        } else {
            // Handle error
            return $response->throw()->json(); // or log the error
        }
    }


    public function RegisterSender(){

        $appkey = 'IKCBnK9hJbdNYwiB'; // Replace this with your actual appkey
        $secretKey = 'muYaXW0s'; // Replace this with your actual secret key

        $client = new Client();
        $url = "https://api-alimtalk.cloud.toast.com/alimtalk/v2.3/appkeys/IKCBnK9hJbdNYwiB/senders";


        $dataPayload = [
            'plusFriendId' => '@tlx', // Replace with actual template code (up to 20 characters)
            'phoneNo' => '+927766188098', // Replace with actual template name (up to 150 characters)
            'categoryCode' => '01900060001', // Replace with actual template content (up to 1000 characters)
];

        $response = $client->request('POST', $url, [
            'headers' => [
                'Content-Type' => 'application/json;charset=UTF-8',
                'X-Secret-Key' => $secretKey,
            ],
            'json' => $dataPayload,
        ]);

        $body = $response->getBody();


        $data = json_decode($body, true);
        return response()->json($data);

    }

}
