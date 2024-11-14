<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use DB;

class SmtpController extends Controller
{
    function saveConfiguration(Request $request){
        $requestData = $request->all();
        foreach ($requestData as $value) {
            if($value['host'] == ''){
                return response()->json(['message'=>'Smtp Host field is required','status'=>'fail']);
            }
            if($value['port'] == ''){
                return response()->json(['message'=>'Smtp Post field is required','status'=>'fail']);
            }
            if($value['username'] == ''){
                return response()->json(['message'=>'Smtp Username field is required','status'=>'fail']);
            }
            if($value['password'] == ''){
                return response()->json(['message'=>'Smtp Password field is required','status'=>'fail']);
            }
            // echo '<pre>';print_r($value);
            $domainExists = DB::table('smtp_config')->where(['shop_name'=> $value['shopDomain']])->first();
            if($domainExists){
                $updateData = [
                    'protocol'      => $value['encryption'],
                    'smtphost'      => $value['host'],
                    'smtpport'      => $value['port'],
                    'smtpusername'  => $value['username'],
                    'smtppassword'  => $value['password'],
                    'shop_name'     => $value['shopDomain'],
                    'created_at'    => date('Y-m-d h:i:s'),
                    'updated_at'    => date('Y-m-d h:i:s'),
                ];
                $saveSmtpData =  DB::table('smtp_config')->update($updateData);
            }else{
                $insertData = [
                    'protocol'      => $value['encryption'],
                    'smtphost'      => $value['host'],
                    'smtpport'      => $value['port'],
                    'smtpusername'  => $value['username'],
                    'smtppassword'  => $value['password'],
                    'shop_name'     => $value['shopDomain'],
                    'created_at'    => date('Y-m-d h:i:s'),
                    'updated_at'    => date('Y-m-d h:i:s'),
                ];
                $saveSmtpData =  DB::table('smtp_config')->insert($insertData);
            }
        }
        return response()->json(['message'=>'Smtp Configuration added successfully','status'=>'success']);
        // exit;
    }

    public static function getSmtpConfiguration($shopDomain)
    {
        $getSmtpData = DB::table('smtp_config')->where(['shop_name'=> $shopDomain])->first();
        if($getSmtpData){
            $getSmtpData->port = (string)$getSmtpData->smtpport;
            return $getSmtpData;
        }else{
            $getSmtpData = [
                'protocol'=>"",
                'smtphost'=>"",
                'smtpport'=>"",
                'smtpusername'=>"",
                'smtppassword'=>"",
                'shop_name'=>"",
            ];
            return $getSmtpData;
        }
    }
}
