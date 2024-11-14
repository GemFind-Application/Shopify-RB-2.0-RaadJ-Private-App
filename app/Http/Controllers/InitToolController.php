<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CssConfigure;
use Illuminate\Http\Request;
use DB;

class InitToolController extends Controller
{
    public function initToolApi(Request $request)
    {
        // header('Access-Control-Allow-Origin: *');
        $settingData = DB::table('ringbuilder_config')->where(['shop' => $request->shop_domain])->get()->first();
        $css_configuration = CssConfigure::where(['shop' => $request->shop_domain])->first();
        // echo '<pre>';print_r($settingData);exit;
        $jewelCloudApi = 'http://api.jewelcloud.com/api/RingBuilder/GetDiamondsJCOptions?DealerID=' . $settingData->dealerid;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $jewelCloudApi);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        $response = curl_exec($curl);
        $server_output = json_decode($response, true);



        //echo '<pre>';print_r($server_output);exit;

        foreach ($server_output as $value) {
            foreach ($value as $val) {
                if ($val['internalUseLink']) {
                    $internalUseLink = $val['internalUseLink'];
                } else {
                    $internalUseLink = "0";
                }
                if ($val['scheduleViewing']) {
                    $scheduleViewing = $val['scheduleViewing'];
                } else {
                    $scheduleViewing = "0";
                }
                if ($val['show_In_House_Diamonds_First']) {
                    $show_In_House_Diamonds_First = $val['show_In_House_Diamonds_First'];
                } else {
                    $show_In_House_Diamonds_First = "0";
                }
                if ($val['show_Advance_options_as_Default_in_Diamond_Search']) {
                    $show_Advance_options_as_Default_in_Diamond_Search = $val['show_Advance_options_as_Default_in_Diamond_Search'];
                } else {
                    $show_Advance_options_as_Default_in_Diamond_Search = "0";
                }
                if ($val['show_Certificate_in_Diamond_Search']) {
                    $show_Certificate_in_Diamond_Search = $val['show_Certificate_in_Diamond_Search'];
                } else {
                    $show_Certificate_in_Diamond_Search = "0";
                }
                if ($val['show_Request_Certificate']) {
                    $show_Request_Certificate = $val['show_Request_Certificate'];
                } else {
                    $show_Request_Certificate = "0";
                }
                if ($val['show_Diamond_Prices']) {
                    $show_Diamond_Prices = $val['show_Diamond_Prices'];
                } else {
                    $show_Diamond_Prices = "0";
                }
                if ($val['markup_Your_Own_Inventory']) {
                    $markup_Your_Own_Inventory = $val['markup_Your_Own_Inventory'];
                } else {
                    $markup_Your_Own_Inventory = "0";
                }
                if ($val['show_Pinterest_Share']) {
                    $show_Pinterest_Share = $val['show_Pinterest_Share'];
                } else {
                    $show_Pinterest_Share = "0";
                }
                if ($val['show_Twitter_Share']) {
                    $show_Twitter_Share = $val['show_Twitter_Share'];
                } else {
                    $show_Twitter_Share = "0";
                }
                if ($val['show_Facebook_Share']) {
                    $show_Facebook_Share = $val['show_Facebook_Share'];
                } else {
                    $show_Facebook_Share = "0";
                }
                if ($val['show_Facebook_Like']) {
                    $show_Facebook_Like = $val['show_Facebook_Like'];
                } else {
                    $show_Facebook_Like = "0";
                }
                if ($val['show_AddtoCart_Buttom']) {
                    $show_AddtoCart_Buttom = $val['show_AddtoCart_Buttom'];
                } else {
                    $show_AddtoCart_Buttom = "0";
                }
                if ($val['drop_A_Hint']) {
                    $dropHint = $val['drop_A_Hint'];
                } else {
                    $dropHint = $settingData->enable_hint;
                }
                if ($val['email_A_Friend']) {
                    $emailFriend = $val['email_A_Friend'];
                } else {
                    $emailFriend = $settingData->enable_email_friend;
                }
            }
        }
        if (isset($_SERVER['HTTPS'])) {
            $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
        } else {
            $protocol = 'http';
        }
        if ($settingData->type_1 == "2" || $settingData->type_1 == "3") {
            $settingData->is_api = "false";
        } else {
            $settingData->is_api = "true";
        }
        $settingData->type_1 = (string)$settingData->type_1;
        $settingData->internalUseLink = (string)$internalUseLink;
        $settingData->scheduleViewing = (string)$scheduleViewing;
        $settingData->show_In_House_Diamonds_First = (string)$show_In_House_Diamonds_First;
        $settingData->show_Advance_options_as_Default_in_Diamond_Search = (string)$show_Advance_options_as_Default_in_Diamond_Search;
        $settingData->show_Certificate_in_Diamond_Search = (string)$show_Certificate_in_Diamond_Search;
        $settingData->show_Request_Certificate = (string)$show_Request_Certificate;
        $settingData->show_Diamond_Prices = (string)$show_Diamond_Prices;
        $settingData->markup_Your_Own_Inventory = (string)$markup_Your_Own_Inventory;
        $settingData->show_Pinterest_Share = (string)$show_Pinterest_Share;
        $settingData->show_Twitter_Share = (string)$show_Twitter_Share;
        $settingData->show_Facebook_Share = (string)$show_Facebook_Share;
        $settingData->show_Facebook_Like = (string)$show_Facebook_Like;
        $settingData->show_AddtoCart_Buttom = (string)$show_AddtoCart_Buttom;
        $settingData->enable_hint = (string)$dropHint;
        $settingData->price_row_format = (string)$settingData->price_row_format;
        $settingData->enable_email_friend = (string)$emailFriend;
        $settingData->store_id = (string)$settingData->store_id;
        $settingData->store_location_id = (string)$settingData->store_location_id;
        $settingData->dealerpassword = (string)$settingData->dealerpassword;
        $settingData->from_email_address = (string)$settingData->from_email_address;
        $settingData->shop_access_token = (string)$settingData->shop_access_token;
        $settingData->server_url = (string)$protocol . '://' . request()->getHost();
        $settingData->currency = $this->getCurrency($settingData->dealerid);
        $settingData->currencyFrom = $this->getCurrencyFrom($settingData->dealerid);

        $styleSettingColors = $this->getStyleSetting($settingData->dealerid);
        $hoverEffect = $styleSettingColors['hoverEffect'];
        $columnHeaderAccent = $styleSettingColors['columnHeaderAccent'];
        $linkColor = $styleSettingColors['linkColor'];
        $callToActionButton = $styleSettingColors['callToActionButton'];


        // echo "<pre>"; print_r($callToActionButton); exit();

        //linkColor
        if (isset($linkColor) && !empty($linkColor)) {
            $settingData->link_colour = $linkColor;
        } else {
            $settingData->link_colour = '#999';
        }

        //hoverEffect
        if (isset($hoverEffect) && !empty($hoverEffect)) {
            $settingData->hover_colour = $hoverEffect;
        } else {
            $settingData->hover_colour = '#92cddc';
        }

        //Sliders
        $settingData->slider_colour = $css_configuration ? $css_configuration->slider : '#828282';

        if (isset($columnHeaderAccent) && !empty($columnHeaderAccent)) {
            $settingData->header_colour = $columnHeaderAccent;
        } else {
            $settingData->header_colour = '#000000';
        }

        //callToActionButton
        if (isset($callToActionButton) && !empty($callToActionButton)) {
            $settingData->button_colour = $callToActionButton;
        } else {
            $settingData->button_colour = '#000022';
        }


        if (!empty($settingData)) {
            $msg['message'] = 'Init Tool Data Successfully';
            $msg['status']  = 'success';
            $msg['data'] = [$settingData];
            return response()->json($msg);
        } else {
            return response()->json(['message' => 'User Profile Fail', 'status' => 'fail', 'data' => []]);
        }
    }
    public function getCurrency($dealerid)
    {
        $settingApi = 'http://api.jewelcloud.com/api/RingBuilder/GetDiamond?DealerID=' . $dealerid;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $settingApi);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        $response = curl_exec($curl);
        $server_output = json_decode($response, true);


        if (!empty($server_output['diamondList'])) {
            if ($server_output['diamondList'][0]['showPrice'] == true) {
                if ($server_output['diamondList'][0]['currencyFrom'] == 'USD') {
                    $currency = '$';
                } else {
                    $currency = $server_output['diamondList'][0]['currencySymbol'];
                }
            } else {
                $currency = '$';
            }
        } else {
            $currency = '$';
        }

        return $currency;
    }

    public function getCurrencyFrom($dealerid)
    {
        $settingApi = 'http://api.jewelcloud.com/api/RingBuilder/GetDiamond?DealerID=' . $dealerid;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $settingApi);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        $response = curl_exec($curl);
        $server_output = json_decode($response, true);

        if (!empty($server_output['diamondList'])) {
            if ($server_output['diamondList'][0]['showPrice'] == true) {
                $currencyFrom = $server_output['diamondList'][0]['currencyFrom'];
            } else {
                $currencyFrom = '$';
            }
        } else {
            $currencyFrom = '$';
        }

        return $currencyFrom;
    }

    public function getStyleSetting($dealerid)
    {
        $settingApi = 'http://api.jewelcloud.com/api/RingBuilder/GetStyleSetting?DealerID=' . $dealerid;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $settingApi);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        $response = curl_exec($curl);
        $server_output = json_decode($response, true);



        //hoverEffect
        if ($server_output[0][0]['hoverEffect']) {
            $hoverEffectColor = $server_output[0][0]['hoverEffect'];
            $hoverEffect = empty($hoverEffectColor[0]['color2']) ? $hoverEffectColor[0]['color1'] : $hoverEffectColor[0]['color2'];
        }

        //columnHeaderAccent
        if ($server_output[0][0]['columnHeaderAccent']) {
            $columnHeaderAccentColor = $server_output[0][0]['columnHeaderAccent'];
            $columnHeaderAccent = empty($columnHeaderAccentColor[0]['color2']) ? $columnHeaderAccentColor[0]['color1'] : $columnHeaderAccentColor[0]['color2'];
        }

        //linkColor
        if ($server_output[0][0]['linkColor']) {
            $linkColors = $server_output[0][0]['linkColor'];
            $linkColor = empty($linkColors[0]['color2']) ? $linkColors[0]['color1'] : $linkColors[0]['color2'];
        }

        //callToActionButton
        if ($server_output[0][0]['callToActionButton']) {
            $callToActionButtonColor = $server_output[0][0]['callToActionButton'];
            $callToActionButton = empty($callToActionButtonColor[0]['color2']) ? $callToActionButtonColor[0]['color1'] : $callToActionButtonColor[0]['color2'];
        }

        $styleSettingColors = [
            'hoverEffect' => $hoverEffect,
            'columnHeaderAccent' => $columnHeaderAccent,
            'linkColor' => $linkColor,
            'callToActionButton' => $callToActionButton,
        ];

        return $styleSettingColors;
    }

    // public function initToolApi1(Request $request)
    // {
    //     $settingData = DB::table('ringbuilder_config')->where(['shop'=>$request->shop_domain])->get()->first();
    //     // echo '<pre>';print_r($settingData);exit;
    //     $jewelCloudApi ='http://api.jewelcloud.com/api/RingBuilder/GetDiamondsJCOptions?DealerID='.$settingData->dealerid;
    //     $curl = curl_init();
    //     curl_setopt($curl, CURLOPT_URL, $jewelCloudApi);
    //     curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($curl, CURLOPT_HEADER, false);
    //     $response = curl_exec($curl);
    //     $server_output = json_decode($response,true);
    //     foreach ($server_output as $value) {
    //         foreach ($value as $val) {
    //             if($val['internalUseLink']){
    //                 $internalUseLink = $val['internalUseLink'];
    //             }else{
    //                 $internalUseLink = "0";
    //             }
    //             if($val['scheduleViewing']){
    //                 $scheduleViewing = $val['scheduleViewing'];
    //             }else{
    //                 $scheduleViewing = "0";
    //             }
    //             if($val['show_In_House_Diamonds_First']){
    //                 $show_In_House_Diamonds_First = $val['show_In_House_Diamonds_First'];
    //             }else{
    //                 $show_In_House_Diamonds_First = "0";
    //             }
    //             if($val['show_Advance_options_as_Default_in_Diamond_Search']){
    //                 $show_Advance_options_as_Default_in_Diamond_Search = $val['show_Advance_options_as_Default_in_Diamond_Search'];
    //             }else{
    //                 $show_Advance_options_as_Default_in_Diamond_Search = "0";
    //             }
    //             if($val['show_Certificate_in_Diamond_Search']){
    //                 $show_Certificate_in_Diamond_Search = $val['show_Certificate_in_Diamond_Search'];
    //             }else{
    //                 $show_Certificate_in_Diamond_Search = "0";
    //             }
    //             if($val['show_Request_Certificate']){
    //                 $show_Request_Certificate = $val['show_Request_Certificate'];
    //             }else{
    //                 $show_Request_Certificate = "0";
    //             }
    //             if($val['show_Diamond_Prices']){
    //                 $show_Diamond_Prices = $val['show_Diamond_Prices'];
    //             }else{
    //                 $show_Diamond_Prices = "0";
    //             }
    //             if($val['markup_Your_Own_Inventory']){
    //                 $markup_Your_Own_Inventory = $val['markup_Your_Own_Inventory'];
    //             }else{
    //                 $markup_Your_Own_Inventory = "0";
    //             }
    //             if($val['show_Pinterest_Share']){
    //                 $show_Pinterest_Share = $val['show_Pinterest_Share'];
    //             }else{
    //                 $show_Pinterest_Share = "0";
    //             }
    //             if($val['show_Twitter_Share']){
    //                 $show_Twitter_Share = $val['show_Twitter_Share'];
    //             }else{
    //                 $show_Twitter_Share = "0";
    //             }
    //             if($val['show_Facebook_Share']){
    //                 $show_Facebook_Share = $val['show_Facebook_Share'];
    //             }else{
    //                 $show_Facebook_Share = "0";
    //             }
    //             if($val['show_Facebook_Like']){
    //                 $show_Facebook_Like = $val['show_Facebook_Like'];
    //             }else{
    //                 $show_Facebook_Like = "0";
    //             }
    //             if($val['show_AddtoCart_Buttom']){
    //                 $show_AddtoCart_Buttom = $val['show_AddtoCart_Buttom'];
    //             }else{
    //                 $show_AddtoCart_Buttom = "0";
    //             }
    //             if($val['drop_A_Hint']){
    //                 $dropHint = $val['drop_A_Hint'];
    //             }else{
    //                 $dropHint = $settingData->enable_hint;
    //             }
    //             if($val['email_A_Friend']){
    //                 $emailFriend = $val['email_A_Friend'];
    //             }else{
    //                 $emailFriend = $settingData->enable_email_friend;
    //             }
    //         }
    //     }
    //     if(isset($_SERVER['HTTPS'])){
    //         $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
    //     }
    //     else{
    //         $protocol = 'http';
    //     }
    //         $settingData->is_api = "true";
    //     $settingData->type_1 = (string)$settingData->type_1;
    //     $settingData->internalUseLink = (string)$internalUseLink;
    //     $settingData->scheduleViewing = (string)$scheduleViewing;
    //     $settingData->show_In_House_Diamonds_First = (string)$show_In_House_Diamonds_First;
    //     $settingData->show_Advance_options_as_Default_in_Diamond_Search = (string)$show_Advance_options_as_Default_in_Diamond_Search;
    //     $settingData->show_Certificate_in_Diamond_Search = (string)$show_Certificate_in_Diamond_Search;
    //     $settingData->show_Request_Certificate = (string)$show_Request_Certificate;
    //     $settingData->show_Diamond_Prices = (string)$show_Diamond_Prices;
    //     $settingData->markup_Your_Own_Inventory = (string)$markup_Your_Own_Inventory;
    //     $settingData->show_Pinterest_Share = (string)$show_Pinterest_Share;
    //     $settingData->show_Twitter_Share = (string)$show_Twitter_Share;
    //     $settingData->show_Facebook_Share = (string)$show_Facebook_Share;
    //     $settingData->show_Facebook_Like = (string)$show_Facebook_Like;
    //     $settingData->show_AddtoCart_Buttom = (string)$show_AddtoCart_Buttom;
    //     $settingData->enable_hint = (string)$dropHint;
    //     $settingData->enable_email_friend = (string)$emailFriend;
    //     $settingData->store_id = (string)$settingData->store_id;
    //     $settingData->store_location_id = (string)$settingData->store_location_id;
    //     $settingData->dealerpassword = (string)$settingData->dealerpassword;
    //     $settingData->from_email_address = (string)$settingData->from_email_address;
    //     $settingData->shop_access_token = (string)$settingData->shop_access_token;
    //     $settingData->server_url = (string)$protocol.'://'.request()->getHost();

    //     if(!empty($settingData)){
    //         $msg['message'] = 'Init Tool Data Successfully';
    //         $msg['status']  = 'success';
    //         $msg['data'] = [$settingData];
    //         return response()->json($msg);
    //     }else{
    //         return response()->json(['message'=>'User Profile Fail','status'=>'fail','data'=>[]]);
    //     }
    // }

    public static function appUninstallJob(Request $request)
    {
        DB::table('users')->where('name', $request->myshopify_domain)->delete();
        DB::table('ringbuilder_config')->where('shop', $request->myshopify_domain)->delete();
        DB::table('css_configuration')->where('shop', $request->myshopify_domain)->delete();
        DB::table('customer')->where('shop', $request->myshopify_domain)->delete();
    }
}
