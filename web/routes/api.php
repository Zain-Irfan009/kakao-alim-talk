<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('index', [\App\Http\Controllers\Controller::class, 'templateRegister']);


Route::get('/', function () {
    return "Hello API";
});

//Route::group(['middleware' => ['shopify.auth']], function () {
//sender-profile
    Route::get('sync-sender-profile', [\App\Http\Controllers\SenderProfileController::class, 'SyncSenderProfile']);
    Route::get('sender-profile', [\App\Http\Controllers\SenderProfileController::class, 'SenderProfile']);
    Route::get('change-sender-profile/{id}', [\App\Http\Controllers\SenderProfileController::class, 'ChangeSenderProfile']);


//template
    Route::get('sync-templates', [\App\Http\Controllers\TemplateController::class, 'SyncTemplates']);

//register new template on Kakao
    Route::get('register-template', [\App\Http\Controllers\TemplateController::class, 'RegisterTemplate']);


// update campaign status
Route::get('campaigns', [\App\Http\Controllers\CampaignController::class, 'Campaigns']);
    Route::get('update-campaign-status', [\App\Http\Controllers\CampaignController::class, 'UpdateCampaignStatus']);
    Route::get('campaign-detail/{id}', [\App\Http\Controllers\CampaignController::class, 'CampaignDetail']);


//logs page
    Route::get('logs', [\App\Http\Controllers\LogController::class, 'Logs']);

//Dashboard logs
    Route::get('dashboard-logs', [\App\Http\Controllers\LogController::class, 'DashboardLogs']);

//order
    Route::get('sync-order', [\App\Http\Controllers\OrderController::class, 'SyncOrder']);

//Setting
    Route::post('add-kakao-setting', [\App\Http\Controllers\SettingController::class, 'AddKakaoSetting']);
    Route::post('update-kakao-setting', [\App\Http\Controllers\SettingController::class, 'UpdateKakaoSetting']);
    Route::delete('delete-kakao-setting/{id}', [\App\Http\Controllers\SettingController::class, 'DeleteKakaoSetting']);
//});


Route::post('/webhooks/app-uninstall', function (Request $request) {
    try {
        $shop=$request->header('x-shopify-shop-domain');
        $shop=\App\Models\Session::where('shop',$shop)->first();
        \App\Models\Order::where('shop_id',$shop->id)->delete();
        \App\Models\LineItem::where('shop_id',$shop->id)->delete();
        \App\Models\Log::where('shop_id',$shop->id)->delete();

        $shop->forceDelete();

    } catch (\Exception $e) {
    }

    return true;
});


Route::post('/webhooks/order-create', function (Request $request) {
    try {

        $order=json_decode($request->getContent());

        $shop=$request->header('x-shopify-shop-domain');
        $shop=\App\Models\Session::where('shop',$shop)->first();
        \App\Jobs\OrderWebhookJob::dispatch($order,$shop);

    } catch (\Exception $e) {

    }
    return true;
});


Route::post('/webhooks/order-update', function (Request $request) {
    try {
        $order=json_decode($request->getContent());

        $shop=$request->header('x-shopify-shop-domain');
        $shop=\App\Models\Session::where('shop',$shop)->first();

        \App\Jobs\OrderWebhookJob::dispatch($order,$shop)->delay(now()->addMinutes(1));

    } catch (\Exception $e) {
    }
    return true;
});
