<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class PlanController extends Controller
{
    public function sendResponse($result, $message)
    {
        $response = [
            'status' => true,
            'message' => $message,
            'data'    => $result,
        ];

        return response()->json($response, 200);
    }

    public function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'status' => false,
            'message' => $errorMessages,
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $error;
        }

        return response()->json($response, $code);
    }

    public function ifPlanIdExists($shopDomain)
    {
        $userId = DB::table('users')->where(['name'=>$shopDomain])->value('id');
        $charges = DB::table('charges')->where(['user_id'=>$userId])->orderBy('created_at','DESC')->first();
        $customerExists = DB::table('customer')->where(['shop'=>$shopDomain])->first();
        if($charges){
            if($customerExists){
                $customer = "1";
            }else{
                $customer = "0";
            }
            $planIdExists = [
                'plan' => "1",
                'customer' => $customer
            ];
            $planData = DB::table('plans')->where(['id'=>$charges->plan_id])->orderBy('created_at','DESC')->first();
            if($planData->id == "1"){
                $buttons = [
                    'basic_button'  => "ACTIVE",
                    'try_button'    => "UPGRADE",
                    'basic_url'     => "#",
                    'try_url'       => route('billing', ['plan' => 2, 'shop' => $shopDomain ]),
                ];
            }elseif($planData->id == "2"){
                $buttons = [
                    'basic_button'  => "DOWNGRADE",
                    'try_button'    => "ACTIVE",
                    'basic_url'     => route('billing', ['plan' => 1, 'shop' => $shopDomain ]),
                    'try_url'       => "#",
                ];
            }
        }else{
            if($customerExists){
                $customer = "1";
            }else{
                $customer = "0";
            }
            $planIdExists = [
                'plan' => "0",
                'customer' => $customer
            ];
            $buttons = [
                'basic_button'  => "ACTIVATE",
                'try_button'    => "ACTIVATE",
                'basic_url'     => route('billing', ['plan' => 1, 'shop' => $shopDomain ]),
                'try_url'       => route('billing', ['plan' => 2, 'shop' => $shopDomain ]),
            ];
        }
        // $basicPlan = route('billing', ['plan' => 1, 'shop' => $shopDomain ]);
        // $tryOnPlan = route('billing', ['plan' => 2, 'shop' => $shopDomain ]);
        return self::sendResponse(['charges'=>$buttons,'planIdExists'=>$planIdExists], 'Plans');
        // echo $basicPlan;exit;
            // return $charges;
    }
}
