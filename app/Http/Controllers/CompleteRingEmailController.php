<?php

namespace App\Http\Controllers;

use App\Http\Controllers\RingEmailController;
use App\Http\Controllers\DiamondEmailController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use DB;
use Mail;
use App\Models\User;

class CompleteRingEmailController extends Controller
{
    public function getShopJsonData($shop)
    {
        $api_key = env('SHOPIFY_API_KEY');

        // Assuming User model has a 'password' field
        $user = User::where(['name' => $shop])->first();
        $apppassword = $user['password'];
        $url = "https://$api_key:$apppassword@$shop/admin/api/" . env('SHOPIFY_API_VERSION') . '/shop.json';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        if ($server_output === false) {
            $error_message = curl_error($ch);
            return response()->json(['error' => 'cURL request failed: ' . $error_message], 500);
        }
        curl_close($ch);
        $response = json_decode($server_output);
        if (property_exists($response, 'shop') && property_exists($response->shop, 'name')) {
            $shop_name = $response->shop->name;
            return response()->json(['name' => $shop_name]);
        }
        return response()->json(['error' => 'Shop name not found in the response'], 500);
    }

    public function crReqInfoApi(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'name'                  => 'required',
            'email'                 => 'required',
            'phone_no'              => 'required',
            'req_message'           => 'required',
            'contact_preference'    => 'required',
        ]);
        if ($validatedData->fails()) {
            $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
            $validation_error['status']  = 'fail';
            $validation_error['data']    = [];
            return response()->json($validation_error);
        }
        $req_post_data = $request->all();
        $currency = $req_post_data['currency'];
        $reqData = DB::table('ringbuilder_config')
            ->select('*')
            ->where(['shop' => $request->shopurl])
            ->get()
            ->first();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'secret' => $reqData->google_secret_key,
            'response' => $request->input('recaptchaToken'),
        ]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        $response = json_decode(($output));
        curl_close($ch);
        if ($response->success == 'true' || $reqData->google_secret_key == null) {
            $ringData = RingEmailController::getRingById($req_post_data['settingid'], $req_post_data['shopurl'], $req_post_data['islabsettings']);
            $diamondData = DiamondEmailController::getDiamondById($req_post_data['diamondid'], $req_post_data['diamondtype'], $req_post_data['shopurl']);
            $store_logo = $reqData->shop_logo;
            $storeAdminEmail = $reqData->admin_email_address;
            $shopurl = "https://" . $req_post_data['shopurl'];
            if ($req_post_data['price'] != '' && $req_post_data['max_carat'] != '' && $req_post_data['min_carat'] != '' && $req_post_data['metalType'] != '') {
                $price_rb = $req_post_data['price'];
                $max_carat = $req_post_data['max_carat'];
                $min_carat = $req_post_data['min_carat'];
                $metalType = $req_post_data['metalType'];
            } else {
                if ($ringData['ringData']['showPrice'] == true) {
                    $price_rb  = $ringData['ringData']['cost'] ? $currency . number_format($ringData['ringData']['cost']) : '';
                } else {
                    $price_rb = 'Call For Price';
                }
                $max_carat = $ringData['ringData']['centerStoneMaxCarat'] ? $ringData['ringData']['centerStoneMaxCarat'] : '';
                $min_carat = $ringData['ringData']['centerStoneMinCarat'] ? $ringData['ringData']['centerStoneMinCarat'] : '';
                $metalType = $ringData['ringData']['metalType'] ? $ringData['ringData']['metalType'] : '';
            }
            $shopData = $this->getShopJsonData($req_post_data['shopurl']);
            $getCustomerData = DB::table('customer')
                ->where('shop', $req_post_data['shopurl'])
                ->orderBy('id', 'DESC')
                ->first();
            $retaileremail = $storeAdminEmail ? $storeAdminEmail : $getCustomerData->email;
            $vendorEmail = ($storeAdminEmail ? $storeAdminEmail : $ringData['ringData']['vendorEmail']);
            if ($diamondData['diamondData']['fancyColorMainBody']) {
                $color_to_display = $diamondData['diamondData']['fancyColorIntensity'] . ' ' . $diamondData['diamondData']['fancyColorMainBody'];
            } elseif ($diamondData['diamondData']['color'] != '') {
                $color_to_display = $diamondData['diamondData']['color'];
            } else {
                $color_to_display = 'NA';
            }
            if ($diamondData['diamondData']['showPrice'] == true) {
                $price  = $diamondData['diamondData']['fltPrice'] ? $currency . number_format($diamondData['diamondData']['fltPrice']) : '';
            } else {
                $price = 'Call For Price';
            }

            //MAIL TO USER
            $data = [
                'name' => $req_post_data['name'],
                'email' => $req_post_data['email'],
                'phone_no' => $req_post_data['phone_no'],
                'req_message' => $req_post_data['req_message'],
                'contact_preference' => $req_post_data['contact_preference'],
                'ring_url' => $req_post_data['ring_url'],
                'price_rb' => $currency . $price_rb,
                'setting_id' => $ringData['ringData']['settingId'] ? $ringData['ringData']['settingId'] : '',
                'stylenumber' => $ringData['ringData']['styleNumber'] ? $ringData['ringData']['styleNumber'] : '',
                'metaltype' => $metalType,
                'centerStoneMinCarat' => $min_carat,
                'centerStoneMaxCarat' => $max_carat,
                'vendorPhone' => $ringData['ringData']['vendorPhone'],
                'diamond_url' => $req_post_data['diamondurl'] ? $req_post_data['diamondurl'] : '',
                'diamond_id' => $diamondData['diamondData']['diamondId'] ? $diamondData['diamondData']['diamondId'] : '',
                'size' => $diamondData['diamondData']['caratWeight'] ? $diamondData['diamondData']['caratWeight'] : '',
                'cut' => $diamondData['diamondData']['cut'] ? $diamondData['diamondData']['cut'] : '',
                'color' => $color_to_display,
                'clarity' => $diamondData['diamondData']['clarity'] ? $diamondData['diamondData']['clarity'] : '',
                'depth' => $diamondData['diamondData']['depth'] ? $diamondData['diamondData']['depth'] : '',
                'table' => $diamondData['diamondData']['table'] ? $diamondData['diamondData']['table'] : '',
                'measurment' => $diamondData['diamondData']['measurement'] ? $diamondData['diamondData']['measurement'] : '',
                'certificate' => $diamondData['diamondData']['certificate'] ? $diamondData['diamondData']['certificate'] : '',
                'certificateNo' => $diamondData['diamondData']['certificateNo'] ? $diamondData['diamondData']['certificateNo'] : '',
                'certificateUrl' => $diamondData['diamondData']['certificateUrl'] ? $diamondData['diamondData']['certificateUrl'] : '',
                'price' => $price,
                'vendorID' => $diamondData['diamondData']['vendorID'] ? $diamondData['diamondData']['vendorID'] : '',
                'vendorName' => $diamondData['diamondData']['vendorName'] ? $diamondData['diamondData']['vendorName'] : '',
                'vendorEmail' => $retaileremail,
                'vendorContactNo' => $diamondData['diamondData']['vendorContactNo'] ? $diamondData['diamondData']['vendorContactNo'] : '',
                'vendorStockNo' => $diamondData['diamondData']['vendorStockNo'] ? $diamondData['diamondData']['vendorStockNo'] : '',
                'vendorFax' => $diamondData['diamondData']['vendorFax'] ? $diamondData['diamondData']['vendorFax'] : '',
                'vendorAddress' => $diamondData['diamondData']['vendorAddress'] ? $diamondData['diamondData']['vendorAddress'] : '',
                'wholeSalePrice' => $diamondData['diamondData']['wholeSalePrice'] ? $currency . number_format($diamondData['diamondData']['wholeSalePrice']) : '',
                'vendorAddress' => $diamondData['diamondData']['vendorAddress'] ? $diamondData['diamondData']['vendorAddress'] : '',
                'retailerName' => $diamondData['diamondData']['retailerInfo']->retailerName ? $diamondData['diamondData']['retailerInfo']->retailerName : '',
                'retailerID' => $diamondData['diamondData']['retailerInfo']->retailerID ? $diamondData['diamondData']['retailerInfo']->retailerID : '',
                'retailerEmail' => $retaileremail,
                'retailerContactNo' => $diamondData['diamondData']['retailerInfo']->retailerContactNo ? $diamondData['diamondData']['retailerInfo']->retailerContactNo : '',
                'retailerStockNo' => $diamondData['diamondData']['retailerInfo']->retailerStockNo ? $diamondData['diamondData']['retailerInfo']->retailerStockNo : '',
                'retailerFax' => $diamondData['diamondData']['retailerInfo']->retailerFax ? $diamondData['diamondData']['retailerInfo']->retailerFax : '',
                'retailerAddress' => $diamondData['diamondData']['retailerInfo']->retailerAddress ? $diamondData['diamondData']['retailerInfo']->retailerAddress : '',
                'shop_logo' => $store_logo,
                'shop_logo_alt' => $reqData->shop,
                'shopurl' => $shopurl,
            ];

            //Sender Email
            $user['to'] = $req_post_data['email'];
            $user['from'] = $storeAdminEmail ? $storeAdminEmail : $getCustomerData->email;
            $user['store'] = $shopData->original['name'];
            Mail::send('completeRingReqInfoSender', $data, function ($messages) use ($user) {
                $messages->to($user['to']);
                $messages->from($user['from'], $user['store']);
                $messages->replyTo($user['from'], $user['store']);
                $messages->subject('Request For More Info');
            });
            //Retailer Email
            $user['to'] = $vendorEmail;
            Mail::send('completeRingReqInfoRetailer', $data, function ($messages) use ($user) {
                $messages->to($user['to']);
                $messages->from($user['from'], $user['store']);
                $messages->replyTo($user['from'], $user['store']);
                $messages->subject('Request For More Info');
            });
            return response()->json(['success' => true, 'message' => 'Thanks for your submission.']);
        } else {
            // reCAPTCHA verification failed
            return response()->json(['success' => false, 'message' => 'reCAPTCHA verification failed.']);
        }
    }


    public function crScheViewApi(Request $request)
    {

        $validatedData = Validator::make($request->all(), [

            'name'              => 'required',
            'email'             => 'required',
            'phone_no'          => 'required',
            'schl_message'      => 'required',
            'location'          => 'required',
            'availability_date' => 'required',
            'appnt_time'        => 'required',
        ]);

        if ($validatedData->fails()) {
            $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
            $validation_error['status']  = 'fail';
            $validation_error['data']    = [];
            return response()->json($validation_error);
        }
        $sch_view_post_data = $request->all();
        $currency = $sch_view_post_data['currency'];
        $schldData = DB::table('ringbuilder_config')
            ->select('*')
            ->where(['shop' => $request->shopurl])
            ->get()
            ->first();


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'secret' => $schldData->google_secret_key,
            'response' => $request->input('recaptchaToken'),
        ]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $output = curl_exec($ch);
        $response = json_decode(($output));
        curl_close($ch);

        if ($response->success == 'true' || $schldData->google_secret_key == null) {
            $schldData = DB::table('ringbuilder_config')->select('*')->where(['shop' => $request->shopurl])->get()->first();
            $ringData = RingEmailController::getRingById($sch_view_post_data['settingid'], $sch_view_post_data['shopurl'], $sch_view_post_data['islabsettings']);
            $diamondData = DiamondEmailController::getDiamondById($sch_view_post_data['diamondid'], $sch_view_post_data['diamondtype'], $sch_view_post_data['shopurl']);
            $store_logo = $schldData->shop_logo;
            $storeAdminEmail = $schldData->admin_email_address;
            $shopurl = "https://" . $sch_view_post_data['shopurl'];
            if ($sch_view_post_data['price'] != '' && $sch_view_post_data['max_carat'] != '' && $sch_view_post_data['min_carat'] != '' && $sch_view_post_data['metalType'] != '') {
                $price_rb = $sch_view_post_data['price'];
                $max_carat = $sch_view_post_data['max_carat'];
                $min_carat = $sch_view_post_data['min_carat'];
                $metalType = $sch_view_post_data['metalType'];
            } else {
                if ($ringData['ringData']['showPrice'] == true) {
                    $price_rb  = $ringData['ringData']['cost'] ? $currency . number_format($ringData['ringData']['cost']) : '';
                } else {
                    $price_rb = 'Call For Price';
                }
                $max_carat = $ringData['ringData']['centerStoneMaxCarat'] ? $ringData['ringData']['centerStoneMaxCarat'] : '';
                $min_carat = $ringData['ringData']['centerStoneMinCarat'] ? $ringData['ringData']['centerStoneMinCarat'] : '';
                $metalType = $ringData['ringData']['metalType'] ? $ringData['ringData']['metalType'] : '';
            }
            $vendorEmail = ($storeAdminEmail ? $storeAdminEmail : $ringData['ringData']['vendorEmail']);
            $retaileremail = ($storeAdminEmail ? $storeAdminEmail : $diamondData['diamondData']['vendorEmail']);
            if ($diamondData['diamondData']['fancyColorMainBody']) {
                $color_to_display = $diamondData['diamondData']['fancyColorIntensity'] . ' ' . $diamondData['diamondData']['fancyColorMainBody'];
            } elseif ($diamondData['diamondData']['color'] != '') {
                $color_to_display = $diamondData['diamondData']['color'];
            } else {
                $color_to_display = 'NA';
            }
            if ($diamondData['diamondData']['showPrice'] == true) {
                $price  = $diamondData['diamondData']['fltPrice'] ? $currency . number_format($diamondData['diamondData']['fltPrice']) : '';
            } else {
                $price = 'Call For Price';
            }
            $shopData = $this->getShopJsonData($sch_view_post_data['shopurl']);
            $getCustomerData = DB::table('customer')
                ->where('shop', $sch_view_post_data['shopurl'])
                ->orderBy('id', 'DESC')
                ->first();
            $retaileremail = $storeAdminEmail ? $storeAdminEmail : $getCustomerData->email;

            //MAIL TO USER
            $data = [
                'name' => $sch_view_post_data['name'],
                'email' => $sch_view_post_data['email'],
                'phone_no' => $sch_view_post_data['phone_no'],
                'schl_message' => $sch_view_post_data['schl_message'],
                'location' => $sch_view_post_data['location'],
                'availability_date' => $sch_view_post_data['availability_date'],
                'appnt_time' => $sch_view_post_data['appnt_time'],
                'ring_url' => $sch_view_post_data['ring_url'],
                'price_rb' => $currency . $price_rb,
                'setting_id' => $ringData['ringData']['settingId'] ? $ringData['ringData']['settingId'] : '',
                'stylenumber' => $ringData['ringData']['styleNumber'] ? $ringData['ringData']['styleNumber'] : '',
                'metaltype' => $metalType,
                'centerStoneMinCarat' => $min_carat,
                'centerStoneMaxCarat' => $max_carat,
                'vendorPhone' => $ringData['ringData']['vendorPhone'],
                'diamond_url' => $sch_view_post_data['diamondurl'] ? $sch_view_post_data['diamondurl'] : '',
                'diamond_id' => $diamondData['diamondData']['diamondId'] ? $diamondData['diamondData']['diamondId'] : '',
                'size' => $diamondData['diamondData']['caratWeight'] ? $diamondData['diamondData']['caratWeight'] : '',
                'cut' => $diamondData['diamondData']['cut'] ? $diamondData['diamondData']['cut'] : '',
                'color' => $color_to_display,
                'clarity' => $diamondData['diamondData']['clarity'] ? $diamondData['diamondData']['clarity'] : '',
                'depth' => $diamondData['diamondData']['depth'] ? $diamondData['diamondData']['depth'] : '',
                'table' => $diamondData['diamondData']['table'] ? $diamondData['diamondData']['table'] : '',
                'measurment' => $diamondData['diamondData']['measurement'] ? $diamondData['diamondData']['measurement'] : '',
                'certificate' => $diamondData['diamondData']['certificate'] ? $diamondData['diamondData']['certificate'] : '',
                'certificateNo' => $diamondData['diamondData']['certificateNo'] ? $diamondData['diamondData']['certificateNo'] : '',
                'certificateUrl' => $diamondData['diamondData']['certificateUrl'] ? $diamondData['diamondData']['certificateUrl'] : '',
                'price' => $price,
                'vendorID' => $diamondData['diamondData']['vendorID'] ? $diamondData['diamondData']['vendorID'] : '',
                'vendorName' => $diamondData['diamondData']['vendorName'] ? $diamondData['diamondData']['vendorName'] : '',
                'vendorEmail' => $retaileremail,
                'vendorContactNo' => $diamondData['diamondData']['vendorContactNo'] ? $diamondData['diamondData']['vendorContactNo'] : '',
                'vendorStockNo' => $diamondData['diamondData']['vendorStockNo'] ? $diamondData['diamondData']['vendorStockNo'] : '',
                'vendorFax' => $diamondData['diamondData']['vendorFax'] ? $diamondData['diamondData']['vendorFax'] : '',
                'vendorAddress' => $diamondData['diamondData']['vendorAddress'] ? $diamondData['diamondData']['vendorAddress'] : '',
                'wholeSalePrice' => $diamondData['diamondData']['wholeSalePrice'] ? $currency . number_format($diamondData['diamondData']['wholeSalePrice']) : '',
                'vendorAddress' => $diamondData['diamondData']['vendorAddress'] ? $diamondData['diamondData']['vendorAddress'] : '',
                'retailerName' => $diamondData['diamondData']['retailerInfo']->retailerName ? $diamondData['diamondData']['retailerInfo']->retailerName : '',
                'retailerID' => $diamondData['diamondData']['retailerInfo']->retailerID ? $diamondData['diamondData']['retailerInfo']->retailerID : '',
                'retailerEmail' => $retaileremail,
                'retailerContactNo' => $diamondData['diamondData']['retailerInfo']->retailerContactNo ? $diamondData['diamondData']['retailerInfo']->retailerContactNo : '',
                'retailerStockNo' => $diamondData['diamondData']['retailerInfo']->retailerStockNo ? $diamondData['diamondData']['retailerInfo']->retailerStockNo : '',
                'retailerFax' => $diamondData['diamondData']['retailerInfo']->retailerFax ? $diamondData['diamondData']['retailerInfo']->retailerFax : '',
                'retailerAddress' => $diamondData['diamondData']['retailerInfo']->retailerAddress ? $diamondData['diamondData']['retailerInfo']->retailerAddress : '',
                'shop_logo' => $store_logo,
                'shop_logo_alt' => $schldData->shop,
                'shopurl' => $shopurl,

            ];

            //Sender Email
            $user['to'] = $sch_view_post_data['email'];
            $user['from'] = $storeAdminEmail ? $storeAdminEmail : $getCustomerData->email;
            $user['store'] = $shopData->original['name'];
            Mail::send('completeRingScheViewSender', $data, function ($messages) use ($user) {

                $messages->to($user['to']);
                $messages->from($user['from'], $user['store']);
                $messages->replyTo($user['from'], $user['store']);
                $messages->subject('Request To Schedule A Viewing');
            });

            //Retailer Email
            $user['to'] = $vendorEmail;
            Mail::send('completeRingScheViewRetailer', $data, function ($messages) use ($user) {

                $messages->to($user['to']);
                $messages->from($user['from'], $user['store']);
                $messages->replyTo($user['from'], $user['store']);
                $messages->subject('Request To Schedule A Viewing');
            });

            return response()->json(['success' => true, 'message' => 'Thanks for your submission.']);
        } else {
            // reCAPTCHA verification failed
            return response()->json(['success' => false, 'message' => 'reCAPTCHA verification failed.']);
        }

        // return response()->json(['message' => 'Email send successfully', 'status' => 'success']);
    }
}
