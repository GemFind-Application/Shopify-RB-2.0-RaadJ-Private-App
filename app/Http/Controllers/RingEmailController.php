<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use DB;
use Mail;
use App\Models\User;

class RingEmailController extends Controller
{
    public static function getRingById($id, $shop, $isLabSettings)
    {

        $settingsData = DB::table('ringbuilder_config')->select('*')->where(['shop' => $shop])->get()->first();

        $DealerID = 'DealerID=' . $settingsData->dealerid . '&';
        $add_lab_para = '';
        $DID = 'SID=' . $id;
        if ($isLabSettings == 1) {
            $add_lab_para = '&IsLabSetting=1';
        }
        $query_string = $DealerID . $DID . $add_lab_para;
        $requestUrl = $settingsData->mountinglistapifancy . $query_string;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $requestUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        $responce = curl_exec($curl);
        $results = json_decode($responce);
        if (curl_errno($curl)) {
            return $returnData = ['ringData' => [], 'total' => 0, 'message' => 'Gemfind: An error has occurred.'];
        }
        if (isset($results->message)) {
            return $returnData = ['ringData' => [], 'total' => 0, 'message' => 'Gemfind: An error has occurred.'];
        }
        curl_close($curl);
        if ($results->settingId != "" && $results->settingId > 0) {
            $ringData = (array) $results;
            $returnData = ['ringData' => $ringData];
        } else {
            $returnData = ['ringData' => []];
        }
        return $returnData;
    }

    public function getStoreSmtp($shopDomain)
    {
        $getSmtpData = DB::table('smtp_config')->where(['shop_name' => $shopDomain])->first();
        return $getSmtpData;
    }

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

    public function dropHintApi(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'name'                  => 'required',
            'email'                 => 'required',
            'hint_Recipient_name'   => 'required',
            'hint_Recipient_email'  => 'required',
            'reason_of_gift'        => 'required',
            'hint_message'          => 'required',
            'deadline'              => 'required',

        ]);
        if ($validatedData->fails()) {
            $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
            $validation_error['status']  = 'fail';
            $validation_error['data']    = [];
            return response()->json($validation_error);
        }

        $hint_post_data = $request->all();
        $hintData = DB::table('ringbuilder_config')->select('*')->where(['shop' => $request->shopurl])->get()->first();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'secret' => $hintData->google_secret_key,
            'response' => $request->input('recaptchaToken'),
        ]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $output = curl_exec($ch);
        $response = json_decode(($output));
        curl_close($ch);

        if ($response->success == 'true' || $hintData->google_secret_key == null) {

            $storeAdminEmail = $hintData->admin_email_address;
            $shopurl = "https://" . $hint_post_data['shopurl'];
            $store_logo = $hintData->shop_logo;
            $ringData =  $this->getRingById($hint_post_data['settingid'], $hint_post_data['shopurl'], $hint_post_data['islabsettings']);


            $shopData = $this->getShopJsonData($hint_post_data['shopurl']);

            $getCustomerData = DB::table('customer')
                ->where('shop', $hint_post_data['shopurl'])
                ->orderBy('id', 'DESC')
                ->first();

            $retaileremail = $storeAdminEmail ? $storeAdminEmail : "";
            $retailername = ($ringData['ringData']['vendorName'] ? $ringData['ringData']['vendorName'] : $hintData['shop']);

            //MAIL TO USER
            $data = [
                'shopurl' => $shopurl,
                'retailername' => $retailername,
                'retailerphone' => $ringData['ringData']['vendorPhone'],
                'name' => $hint_post_data['name'],
                'email' => $hint_post_data['email'],
                'hint_Recipient_name' => $hint_post_data['hint_Recipient_name'],
                'hint_Recipient_email' => $hint_post_data['hint_Recipient_email'],
                'reason_of_gift' => $hint_post_data['reason_of_gift'],
                'hint_message' => $hint_post_data['hint_message'],
                'deadline' => $hint_post_data['deadline'],
                'ring_url' => $hint_post_data['ring_url'],
                'shop_logo' => $store_logo,
                'shop_logo_alt' => $hintData->shop,
                'retailerEmail' => $retaileremail,
            ];

            //Sender Email
            $user['to'] = $request->email;
            $user['from'] = $storeAdminEmail ? $storeAdminEmail : $getCustomerData->email;
            $user['store'] = $shopData->original['name'];

            Mail::send('ringDropHintSender', $data, function ($messages) use ($user) {
                $messages->to($user['to']);
                $messages->from($user['from'], $user['store']);
                $messages->subject('Someone Wants To Drop You A Hint');
                $messages->replyTo($user['from'], $user['store']);
            });

            //Retailer Email
            $user['to'] = $data['retailerEmail'];
            Mail::send('ringDropHintRetailer', $data, function ($messages) use ($user) {
                $messages->to($user['to']);
                $messages->from($user['from'], $user['store']);
                $messages->subject('Someone Wants To Drop You A Hint');
                $messages->replyTo($user['from'], $user['store']);
            });

            // Receiver Email
            $user['to'] = $request->hint_Recipient_email;
            Mail::send('ringDropHintReceiver', $data, function ($messages) use ($user) {
                $messages->to($user['to']);
                $messages->from($user['from'], $user['store']);
                $messages->subject('Someone Wants To Drop You A Hint');
                $messages->replyTo($user['from'], $user['store']);
            });
            return response()->json(['success' => true, 'message' => 'Thanks for your submission.']);
        } else {
            // reCAPTCHA verification failed
            return response()->json(['success' => false, 'message' => 'reCAPTCHA verification failed.']);
        }
        // return response()->json(['message' => 'Email send successfully', 'status' => 'success']);
    }

    public function reqInfoApi(Request $request)
    {
        // header('Access-Control-Allow-Origin: *');
        //  echo '<pre>';print_r($request->all());exit;
        $req_post_data = $request->all();

        $validatedData = Validator::make($request->all(), [
            'name'                  => 'required',
            'email'                 => 'required',
            'phone_no'              => 'required',
            'message'               => 'required',
            'contact_preference'    => 'required',
        ]);
        if ($validatedData->fails()) {
            $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
            $validation_error['status']  = 'fail';
            $validation_error['data']    = [];
            return response()->json($validation_error);
        }

        $currency = $req_post_data['currency'];
        $reqData = DB::table('ringbuilder_config')->select('*')->where(['shop' => $request->shopurl])->get()->first();

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

            $store_logo = $reqData->shop_logo;
            $storeAdminEmail = $reqData->admin_email_address;
            $shopurl = "https://" . $req_post_data['shopurl'];
            $shop_data = User::where('name', $request->shopurl)->firstOrFail();
            if (isset($req_post_data['variantId'])  && isset($req_post_data['productType']) && $req_post_data['productType'] == 'RingBuilderAdvance') {
                $settingId = $req_post_data['variantId'];
                $url = 'https://' . $request->shopurl . '/admin/api/2020-07/graphql.json';
                $qry = '{
                        productVariants(first: 1, query: "' . $settingId . '") {
                            edges {
                                node {
                                    price
                                    title
                                }
                            }
                        }
                    }';
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $qry);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt(
                    $ch,
                    CURLOPT_HTTPHEADER,
                    array(
                        'Content-Type: application/graphql',
                        'X-Shopify-Access-Token:' . $shop_data->password
                    )
                );
                $server_output = curl_exec($ch);
                $variantData = json_decode($server_output, true);
                $price = $variantData['data']['productVariants']['edges'][0]['node']['price'];
                $max_carat = $req_post_data['max_carat'];
                $min_carat = $req_post_data['min_carat'];
                $metalType = explode('/', $variantData['data']['productVariants']['edges'][0]['node']['title'])[0];
                $ringData = $this->getRingById($settingId, $req_post_data['shopurl'], $req_post_data['islabsettings']);
                // echo '<pre>';print_r($metalType);exit;
            } else {
                $settingId = $req_post_data['settingid'];
                $ringData = $this->getRingById($settingId, $req_post_data['shopurl'], $req_post_data['islabsettings']);
                if ($ringData['ringData']['showPrice'] == true) {
                    $price  = $ringData['ringData']['cost'] ? number_format($ringData['ringData']['cost']) : '';
                } else {
                    $price = 'Call For Price';
                }
                $max_carat = $ringData['ringData']['centerStoneMinCarat'] ? $ringData['ringData']['centerStoneMinCarat'] : '';
                $min_carat = $ringData['ringData']['centerStoneMaxCarat'] ? $ringData['ringData']['centerStoneMaxCarat'] : '';
                $metalType = $ringData['ringData']['metalType'] ? $ringData['ringData']['metalType'] : '';
            }
            $vendorEmail = ($storeAdminEmail ? $storeAdminEmail : $ringData['ringData']['vendorEmail']);
            $vendorName = ($ringData['ringData']['vendorName'] ? $ringData['ringData']['vendorName'] : $frndData['shop']);


            $shopData = $this->getShopJsonData($req_post_data['shopurl']);

            $getCustomerData = DB::table('customer')
                ->where('shop', $req_post_data['shopurl'])
                ->orderBy('id', 'DESC')
                ->first();

            $retaileremail = $storeAdminEmail ? $storeAdminEmail : "";

            //MAIL TO USER
            $data = [
                'name' => $req_post_data['name'],
                'email' => $req_post_data['email'],
                'phone_no' => $req_post_data['phone_no'],
                'req_message' => $req_post_data['message'],
                'contact_preference' => $req_post_data['contact_preference'],
                'ring_url' => $req_post_data['ring_url'],
                // 'price' => $ringData['ringData']['cost'] ? $ringData['ringData']['currencySymbol'] . ' ' . number_format($ringData['ringData']['cost']) : '',
                'price' => $currency . $price,
                'setting_id' => $ringData['ringData']['settingId'] ? $ringData['ringData']['settingId'] : '',
                'stylenumber' => $ringData['ringData']['styleNumber'] ? $ringData['ringData']['styleNumber'] : '',
                'metaltype' => $metalType,
                'centerStoneMinCarat' => $min_carat,
                'centerStoneMaxCarat' => $max_carat,
                'retailerName' => $ringData['ringData']['retailerInfo']->retailerName ? $ringData['ringData']['retailerInfo']->retailerName : '',
                'retailerID' => $ringData['ringData']['retailerInfo']->retailerID ? $ringData['ringData']['retailerInfo']->retailerID : '',
                'retailerEmail' => $retaileremail,
                'retailerContactNo' => $ringData['ringData']['retailerInfo']->retailerContactNo ? $ringData['ringData']['retailerInfo']->retailerContactNo : '',
                'retailerFax' => $ringData['ringData']['retailerInfo']->retailerFax ? $ringData['ringData']['retailerInfo']->retailerFax : '',
                'retailerAddress' => $ringData['ringData']['retailerInfo']->retailerAddress ? $ringData['ringData']['retailerInfo']->retailerAddress : '',
                'vendorName' => $vendorName,
                'vendorEmail' => $retaileremail,
                'vendorPhone' => $ringData['ringData']['vendorPhone'],
                'shop_logo' => $store_logo,
                'shop_logo_alt' => $reqData->shop,
                'shopurl' => $shopurl,
            ];

            //NEED TO GET DATA FROM DATABASE HERE
            //     $store_detail = $this->getStoreSmtp($req_post_data['shopurl']);
            //     if($store_detail){
            //         $config = array(
            //             'protocol' =>  'smtp',
            //             'smtp_host' => $store_detail->smtphost,
            //             'smtp_port' => $store_detail->smtpport,
            //             'smtp_user' => $store_detail->smtpusername,
            //             'smtp_pass' => $store_detail->smtppassword,
            //             'smtp_crypto' => $store_detail->protocol == "none" ? "tls" : $store_detail->protocol,
            //             'mailtype' => 'html',
            //             'smtp_timeout' => '4',
            //             'charset' => 'utf-8',
            //             'wordwrap' => TRUE,
            //             'newline' => "\r\n"
            //         );
            //    }
            //END FOR DATABASE QUERY



            //Sender Email
            $user['to'] = $req_post_data['email'];
            $user['from'] = $storeAdminEmail ? $storeAdminEmail : $getCustomerData->email;
            $user['store'] = $shopData->original['name'];

            Mail::send('ringReqInfoSender', $data, function ($messages) use ($user) {
                $messages->to($user['to']);
                $messages->from($user['from'], $user['store']);
                $messages->replyTo($user['from'], $user['store']);
                $messages->subject('Request For More Info');
            });

            //Retailer Email
            $user['to'] = $vendorEmail;
            Mail::send('ringReqInfoRetailer', $data, function ($messages) use ($user) {
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
        // return response()->json(['message' => 'Email send successfully', 'status' => 'success']);
    }

    public function emailFriendApi(Request $request)
    {
        // header('Access-Control-Allow-Origin: *');
        $validatedData = Validator::make($request->all(), [
            'name'       => 'required',
            'email'        => 'required',
            'frnd_name'    => 'required',
            'frnd_email'   => 'required',
            'frnd_message' => 'required',
        ]);
        if ($validatedData->fails()) {
            $validation_error['message'] = implode('|| ', $validatedData->messages()->all());
            $validation_error['status']  = 'fail';
            $validation_error['data']    = [];
            return response()->json($validation_error);
        }

        $email_friend_post_data = $request->all();
        $currency = $email_friend_post_data['currency'];
        $frndData = DB::table('ringbuilder_config')->select('*')->where(['shop' => $request->shopurl])->get()->first();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'secret' => $frndData->google_secret_key,
            'response' => $request->input('recaptchaToken'),
        ]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $output = curl_exec($ch);
        $response = json_decode(($output));
        curl_close($ch);

        if ($response->success == 'true' || $frndData->google_secret_key == null) {

            $storeAdminEmail = $frndData->admin_email_address;
            $store_logo = $frndData->shop_logo;
            $shopurl = "https://" . $email_friend_post_data['shopurl'];
            $shop_data = User::where('name', $request->shopurl)->firstOrFail();
            if (isset($email_friend_post_data['variantId'])  && isset($email_friend_post_data['productType']) && $email_friend_post_data['productType'] == 'RingBuilderAdvance') {
                $settingId = $email_friend_post_data['variantId'];
                $url = 'https://' . $request->shopurl . '/admin/api/2020-07/graphql.json';
                $qry = '{
                        productVariants(first: 1, query: "' . $settingId . '") {
                            edges {
                                node {
                                    price
                                    title
                                }
                            }
                        }
                    }';
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $qry);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt(
                    $ch,
                    CURLOPT_HTTPHEADER,
                    array(
                        'Content-Type: application/graphql',
                        'X-Shopify-Access-Token:' . $shop_data->password
                    )
                );
                $server_output = curl_exec($ch);
                $variantData = json_decode($server_output, true);
                $price = $variantData['data']['productVariants']['edges'][0]['node']['price'];
                $max_carat = $email_friend_post_data['max_carat'];
                $min_carat = $email_friend_post_data['min_carat'];
                $metalType = explode('/', $variantData['data']['productVariants']['edges'][0]['node']['title'])[0];
                $ringData = $this->getRingById($settingId, $email_friend_post_data['shopurl'], $email_friend_post_data['islabsettings']);
                // echo '<pre>';print_r($price);exit;
            } else {
                $settingId = $email_friend_post_data['settingid'];
                $ringData = $this->getRingById($settingId, $email_friend_post_data['shopurl'], $email_friend_post_data['islabsettings']);
                if ($ringData['ringData']['showPrice'] == true) {
                    $price  = $ringData['ringData']['cost'] ? number_format($ringData['ringData']['cost']) : '';
                } else {
                    $price = 'Call For Price';
                }
                $max_carat = $ringData['ringData']['centerStoneMinCarat'] ? $ringData['ringData']['centerStoneMinCarat'] : '';
                $min_carat = $ringData['ringData']['centerStoneMaxCarat'] ? $ringData['ringData']['centerStoneMaxCarat'] : '';
                $metalType = $ringData['ringData']['metalType'] ? $ringData['ringData']['metalType'] : '';
            }

            $shopData = $this->getShopJsonData($email_friend_post_data['shopurl']);

            $getCustomerData = DB::table('customer')
                ->where('shop', $email_friend_post_data['shopurl'])
                ->orderBy('id', 'DESC')
                ->first();


            $vendorEmail = $storeAdminEmail ? $storeAdminEmail : $getCustomerData->email;
            $vendorName = ($ringData['ringData']['vendorName'] ? $ringData['ringData']['vendorName'] : $frndData['shop']);



            //MAIL TO USER
            $data = [
                'name' => $email_friend_post_data['name'],
                'email' => $email_friend_post_data['email'],
                'frnd_name' => $email_friend_post_data['frnd_name'],
                'frnd_email' => $email_friend_post_data['frnd_email'],
                'frnd_message' => $email_friend_post_data['frnd_message'],
                'ring_url' => $email_friend_post_data['ring_url'] ? $email_friend_post_data['ring_url'] : '',
                'setting_id' => $ringData['ringData']['settingId'] ? $ringData['ringData']['settingId'] : '',
                'stylenumber' => $ringData['ringData']['styleNumber'] ? $ringData['ringData']['styleNumber'] : '',
                'metaltype' => $metalType,
                'centerStoneMinCarat' => $min_carat,
                'centerStoneMaxCarat' => $max_carat,
                // 'price' => $ringData['ringData']['cost'] ? $ringData['ringData']['currencySymbol'] . ' ' . number_format($ringData['ringData']['cost']) : '',
                'price' => $currency . $price,
                'retailerName' => $ringData['ringData']['retailerInfo']->retailerName ? $ringData['ringData']['retailerInfo']->retailerName : '',
                'retailerID' => $ringData['ringData']['retailerInfo']->retailerID ? $ringData['ringData']['retailerInfo']->retailerID : '',
                'retailerEmail' => $storeAdminEmail ? $storeAdminEmail : "",
                'retailerContactNo' => $ringData['ringData']['retailerInfo']->retailerContactNo ? $ringData['ringData']['retailerInfo']->retailerContactNo : '',
                'retailerFax' => $ringData['ringData']['retailerInfo']->retailerFax ? $ringData['ringData']['retailerInfo']->retailerFax : '',
                'retailerAddress' => $ringData['ringData']['retailerInfo']->retailerAddress ? $ringData['ringData']['retailerInfo']->retailerAddress : '',
                'vendorName' => $vendorName,
                'vendorEmail' => $vendorEmail,
                'vendorPhone' => $ringData['ringData']['vendorPhone'],
                'shop_logo' => $store_logo,
                'shop_logo_alt' => $frndData->shop,
                'shopurl' => $shopurl,
            ];

            //Sender Email
            $user['to'] = $email_friend_post_data['email'];
            $user['from'] = $storeAdminEmail ? $storeAdminEmail : $getCustomerData->email;
            $user['store'] = $shopData->original['name'];

            Mail::send('ringEmailFriendSender', $data, function ($messages) use ($user) {
                $messages->to($user['to']);
                $messages->from($user['from'], $user['store']);
                $messages->replyTo($user['from'], $user['store']);
                $messages->subject('A Friend Wants To Share With You');
            });

            //Retailer Email
            $user['to'] = $vendorEmail;
            Mail::send('ringEmailFriendRetailer', $data, function ($messages) use ($user) {
                $messages->to($user['to']);
                $messages->from($user['from'], $user['store']);
                $messages->replyTo($user['from'], $user['store']);
                $messages->subject('A Friend Wants To Share With You');
            });

            // Receiver email
            $user['to'] = $email_friend_post_data['frnd_email'];
            Mail::send('ringEmailFriendReceiver', $data, function ($messages) use ($user) {
                $messages->to($user['to']);
                $messages->from($user['from'], $user['store']);
                $messages->replyTo($user['from'], $user['store']);
                $messages->subject('A Friend Wants To Share With You');
            });


            return response()->json(['success' => true, 'message' => 'Thanks for your submission.']);
        } else {
            // reCAPTCHA verification failed
            return response()->json(['success' => false, 'message' => 'reCAPTCHA verification failed.']);
        }
        // return response()->json(['message' => 'Email send successfully', 'status' => 'success']);
    }

    public function scheViewApi(Request $request)
    {
        // echo '<pre>';print_r($request->all());exit;
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
        $schldData = DB::table('ringbuilder_config')->select('*')->where(['shop' => $request->shopurl])->get()->first();

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

            $storeAdminEmail = $schldData->admin_email_address;
            $store_logo = $schldData->shop_logo;
            $shopurl = "https://" . $sch_view_post_data['shopurl'];
            $shop_data = User::where('name', $request->shopurl)->firstOrFail();
            if (isset($sch_view_post_data['variantId'])  && isset($sch_view_post_data['productType']) && $sch_view_post_data['productType'] == 'RingBuilderAdvance') {
                $settingId = $sch_view_post_data['variantId'];
                $url = 'https://' . $request->shopurl . '/admin/api/2020-07/graphql.json';
                $qry = '{
                        productVariants(first: 1, query: "' . $settingId . '") {
                            edges {
                                node {
                                    price
                                    title
                                }
                            }
                        }
                    }';
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $qry);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt(
                    $ch,
                    CURLOPT_HTTPHEADER,
                    array(
                        'Content-Type: application/graphql',
                        'X-Shopify-Access-Token:' . $shop_data->password
                    )
                );
                $server_output = curl_exec($ch);
                $variantData = json_decode($server_output, true);
                $price = $variantData['data']['productVariants']['edges'][0]['node']['price'];
                $max_carat = $sch_view_post_data['max_carat'];
                $min_carat = $sch_view_post_data['min_carat'];
                $metalType = explode('/', $variantData['data']['productVariants']['edges'][0]['node']['title'])[0];
                $ringData = $this->getRingById($settingId, $sch_view_post_data['shopurl'], $sch_view_post_data['islabsettings']);
                // echo '<pre>';print_r($price);exit;
            } else {
                $settingId = $sch_view_post_data['settingid'];
                $ringData = $this->getRingById($settingId, $sch_view_post_data['shopurl'], $sch_view_post_data['islabsettings']);
                if ($ringData['ringData']['showPrice'] == true) {
                    $price  = $ringData['ringData']['cost'] ? number_format($ringData['ringData']['cost']) : '';
                } else {
                    $price = 'Call For Price';
                }
                $max_carat = $ringData['ringData']['centerStoneMinCarat'] ? $ringData['ringData']['centerStoneMinCarat'] : '';
                $min_carat = $ringData['ringData']['centerStoneMaxCarat'] ? $ringData['ringData']['centerStoneMaxCarat'] : '';
                $metalType = $ringData['ringData']['metalType'] ? $ringData['ringData']['metalType'] : '';
            }
            $vendorEmail = $storeAdminEmail ? $storeAdminEmail : $getCustomerData->email;
            $vendorName = ($ringData['ringData']['vendorName'] ? $ringData['ringData']['vendorName'] : $schldData['shop']);

            $shopData = $this->getShopJsonData($sch_view_post_data['shopurl']);

            $getCustomerData = DB::table('customer')
                ->where('shop', $sch_view_post_data['shopurl'])
                ->orderBy('id', 'DESC')
                ->first();

            //MAIL TO USER
            $data = [
                'name' => $sch_view_post_data['name'],
                'email' => $sch_view_post_data['email'],
                'phone_no' => $sch_view_post_data['phone_no'],
                'schl_message' => $sch_view_post_data['schl_message'],
                'location' => $sch_view_post_data['location'],
                'availability_date' => $sch_view_post_data['availability_date'],
                'appnt_time' => $sch_view_post_data['appnt_time'],
                'ring_url' => $sch_view_post_data['ring_url'] ? $sch_view_post_data['ring_url'] : '',
                'setting_id' => $ringData['ringData']['settingId'] ? $ringData['ringData']['settingId'] : '',
                'stylenumber' => $ringData['ringData']['styleNumber'] ? $ringData['ringData']['styleNumber'] : '',
                'metaltype' => $metalType,
                'centerStoneMinCarat' => $min_carat,
                'centerStoneMaxCarat' => $max_carat,
                // 'price' => $ringData['ringData']['cost'] ? $ringData['ringData']['currencySymbol'] . ' ' . number_format($ringData['ringData']['cost']) : '',
                'price' => $currency . $price,
                'retailerName' => $ringData['ringData']['retailerInfo']->retailerName ? $ringData['ringData']['retailerInfo']->retailerName : '',
                'retailerID' => $ringData['ringData']['retailerInfo']->retailerID ? $ringData['ringData']['retailerInfo']->retailerID : '',
                'retailerEmail' => $storeAdminEmail ? $storeAdminEmail : "",
                'retailerContactNo' => $ringData['ringData']['retailerInfo']->retailerContactNo ? $ringData['ringData']['retailerInfo']->retailerContactNo : '',
                'retailerFax' => $ringData['ringData']['retailerInfo']->retailerFax ? $ringData['ringData']['retailerInfo']->retailerFax : '',
                'retailerAddress' => $ringData['ringData']['retailerInfo']->retailerAddress ? $ringData['ringData']['retailerInfo']->retailerAddress : '',
                'vendorName' => $vendorName,
                'vendorEmail' => $vendorEmail,
                'vendorPhone' => $ringData['ringData']['vendorPhone'],
                'shop_logo' => $store_logo,
                'shop_logo_alt' => $schldData->shop,
                'shopurl' => $shopurl,
            ];



            $user['from'] = $storeAdminEmail ? $storeAdminEmail : $getCustomerData->email;
            $user['store'] = $shopData->original['name'];


            //Sender Email
            $user['to'] = $sch_view_post_data['email'];
            Mail::send('ringScheViewSender', $data, function ($messages) use ($user) {
                $messages->to($user['to']);
                $messages->subject('Request To Schedule A Viewing');
                $messages->from($user['from'], $user['store']);
                $messages->replyTo($user['from'], $user['store']);
            });

            // Retailer Email
            $user['to'] = $vendorEmail;
            Mail::send('ringScheViewRetailer', $data, function ($messages) use ($user) {
                $messages->to($user['to']);
                $messages->subject('Request To Schedule A Viewing');
                $messages->from($user['from'], $user['store']);
                $messages->replyTo($user['from'], $user['store']);
            });

            return response()->json(['success' => true, 'message' => 'Thanks for your submission.']);
        } else {
            // reCAPTCHA verification failed
            return response()->json(['success' => false, 'message' => 'reCAPTCHA verification failed.']);
        }

        // return response()->json(['message' => 'Email send successfully', 'status' => 'success']);
    }
}
