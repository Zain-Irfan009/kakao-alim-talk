<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Setting;
use Illuminate\Http\Request;

class LogController extends Controller
{

    public function DashboardLogs(Request $request){

        try {
            $shop = getShop($request->get('shopifySession'));
            if ($shop) {
                // Get the start and end dates from the request
                $startDate = $request->get('start_date');
                $endDate = $request->get('end_date');

                // Prepare the base query for each sms_type
                $latest_messages = Log::where('shop_id', $shop->id);
                $queryTotalMessages = Log::where('shop_id', $shop->id);
                $queryNewOrder = Log::where('shop_id', $shop->id)->where('sms_type', 'new_order');
                $queryOrderUpdate = Log::where('shop_id', $shop->id)->where('sms_type', 'order_update');
                $queryOrderFulfilled = Log::where('shop_id', $shop->id)->where('sms_type', 'order_fulfilled');
                $queryOrderCancel = Log::where('shop_id', $shop->id)->where('sms_type', 'order_cancel');

                // If start and end dates are provided, apply the date filter
                if ($startDate && $endDate) {
                    $startDate = \Carbon\Carbon::parse($startDate)->startOfDay();
                    $endDate = \Carbon\Carbon::parse($endDate)->endOfDay();

                    $latest_messages->whereBetween('created_at', [$startDate, $endDate]);
                    $queryTotalMessages->whereBetween('created_at', [$startDate, $endDate]);
                    $queryNewOrder->whereBetween('created_at', [$startDate, $endDate]);
                    $queryOrderUpdate->whereBetween('created_at', [$startDate, $endDate]);
                    $queryOrderFulfilled->whereBetween('created_at', [$startDate, $endDate]);
                    $queryOrderCancel->whereBetween('created_at', [$startDate, $endDate]);
                }

                // Count the results for each sms_type
                $latest_messages=$latest_messages->orderBy('id','desc')->take(10)->get();
                $total_messages = $queryTotalMessages->count();
                $order_new = $queryNewOrder->count();
                $order_update = $queryOrderUpdate->count();
                $order_fulfilled = $queryOrderFulfilled->count();
                $order_cancel = $queryOrderCancel->count();


                $logs = $queryTotalMessages->selectRaw('DATE(created_at) as date, COUNT(*) as value')
                    ->groupBy('date')
                    ->orderBy('date', 'asc')
                    ->get();

                // Format the logs data to match the chart data structure
                $chart_data = [];
                foreach ($logs as $log) {
                    $chart_data[] = [
                        'date' => \Carbon\Carbon::parse($log->date)->format('M d, Y'),
                        'value' => (int) $log->value,
                    ];
                }

                $data = [
                    'success' => true,
                    'total_messages' => $total_messages,
                    'new_order' => $order_new,
                    'order_update' => $order_update,
                    'order_fulfilled' => $order_fulfilled,
                    'order_cancel' => $order_cancel,
                    'chart_data' => $chart_data,
                    'latest_messages'=>$latest_messages
                ];
            }
        } catch (\Exception $exception) {
            $data = [
                'error' => $exception->getMessage(),
                'success' => false
            ];
        }
        return response()->json($data);
    }

    public function Logs(Request $request){
        try {
            $shop = getShop($request->get('shopifySession'));
            if ($shop) {
                $logs=Log::query();
                if ($request->value != null) {
                    $logs = $logs->where('sms', 'like', '%' . $request->value . '%');
                }
                $logs=$logs->where('shop_id',$shop->id)->paginate(20);
                $data = [
                    'success' => true,
                    'data' => $logs,
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
