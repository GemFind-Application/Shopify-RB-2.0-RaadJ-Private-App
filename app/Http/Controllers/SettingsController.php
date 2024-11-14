<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Mail;
use Validator;
use App\Models\User;
use App\Models\CssConfigure;
use Illuminate\Support\Str;

class SettingsController extends Controller
{
    public static function ifCustomerExists($shopDomain)
    {
        $customerExists = DB::table('customer')->where(['shop' => $shopDomain])->first();
        // echo $shopDomain;exit;
        if ($customerExists) {
            $customer = "1";
        } else {
            $customer = "0";
        }
        return $customer;
    }

    function saveCustomer(Request $request)
    {
        $requestData = $request->all();
        foreach ($requestData as $value) {
            $contains = Str::contains($value['email'], '@');
            if ($contains) {
                if ($value['business'] == '') {
                    return response()->json(['message' => 'Name of Business field is required', 'status' => 'fail']);
                }
                if ($value['fullname'] == '') {
                    return response()->json(['message' => 'First and Last name field is required', 'status' => 'fail']);
                }
                if ($value['address'] == '') {
                    return response()->json(['message' => 'Address field is required', 'status' => 'fail']);
                }
                if ($value['state'] == '') {
                    return response()->json(['message' => 'State field is required', 'status' => 'fail']);
                }
                if ($value['city'] == '') {
                    return response()->json(['message' => 'City field is required', 'status' => 'fail']);
                }
                if ($value['zipcode'] == '') {
                    return response()->json(['message' => 'Zipcode field is required', 'status' => 'fail']);
                }
                if ($value['telephone'] == '') {
                    return response()->json(['message' => 'Telephone field is required', 'status' => 'fail']);
                }
                if ($value['website'] == '') {
                    return response()->json(['message' => 'Website Url field is required', 'status' => 'fail']);
                }
                if ($value['email'] == '') {
                    return response()->json(['message' => 'Email field is required', 'status' => 'fail']);
                }
                if ($value['notes'] == '') {
                    return response()->json(['message' => 'Notes field is required', 'status' => 'fail']);
                }
                $domainExists = DB::table('customer')->where(['shop' => $value['shopDomain']])->first();
                if ($domainExists) {
                    $updateData = [
                        'business'      => $value['business'],
                        'name'          => $value['fullname'],
                        'address'       => $value['address'],
                        'state'         => $value['state'],
                        'city'          => $value['city'],
                        'zip_code'      => $value['zipcode'],
                        'telephone'     => $value['telephone'],
                        'website'       => $value['website'],
                        'email'         => $value['email'],
                        'notes'         => $value['notes'],
                        'shop'          => $value['shopDomain'],
                        'created_at'    => date('Y-m-d h:i:s'),
                        'updated_at'    => date('Y-m-d h:i:s'),
                    ];
                    $saveSmtpData =  DB::table('customer')->update($updateData);
                } else {
                    $insertData = [
                        'business'      => $value['business'],
                        'name'          => $value['fullname'],
                        'address'       => $value['address'],
                        'state'         => $value['state'],
                        'city'          => $value['city'],
                        'zip_code'      => $value['zipcode'],
                        'telephone'     => $value['telephone'],
                        'website'       => $value['website'],
                        'email'         => $value['email'],
                        'notes'         => $value['notes'],
                        'shop'          => $value['shopDomain'],
                        'created_at'    => date('Y-m-d h:i:s'),
                        'updated_at'    => date('Y-m-d h:i:s'),
                    ];
                    $inserSettingsData = [
                        'shop'                          => $value['shopDomain'],
                        'dealerid'                      => "1089",
                        'admin_email_address'           => "support@gemfind.com",
                        'dealerauthapi'                 => "http://api.jewelcloud.com/api/RingBuilder/AccountAuthentication",
                        'ringfiltersapi'                => "http://api.jewelcloud.com/api/RingBuilder/GetFilters?",
                        'mountinglistapi'               => "http://api.jewelcloud.com/api/RingBuilder/GetMountingList?",
                        'mountinglistapifancy'          => "http://api.jewelcloud.com/api/RingBuilder/GetMountingDetail?",
                        'ringstylesettingapi'           => "http://api.jewelcloud.com/api/RingBuilder/GetStyleSetting?",
                        'navigationapi'                 => "http://api.jewelcloud.com/api/RingBuilder/GetNavigation?",
                        'filterapi'                     => "http://api.jewelcloud.com/api/RingBuilder/GetDiamondFilter?",
                        'filterapifancy'                => "http://api.jewelcloud.com/api/RingBuilder/GetColorDiamondFilter?",
                        'diamondlistapi'                => "http://api.jewelcloud.com/api/RingBuilder/GetDiamond?",
                        'diamondlistapifancy'           => "http://api.jewelcloud.com/api/RingBuilder/GetColorDiamond?",
                        'diamondshapeapi'               => "http://api.jewelcloud.com/api/ringbuilder/GetShapeByColorFilter?",
                        'diamonddetailapi'              => "http://api.jewelcloud.com/api/RingBuilder/GetDiamondDetail?",
                        'stylesettingapi'               => "http://api.jewelcloud.com/api/RingBuilder/GetStyleSetting?",
                        'getvideoapi'                   => "https://api.jewelcloud.com/api/jewelry/GetVideoUrl?",
                        'enable_hint'                   => "1",
                        'enable_email_friend'           => "1",
                        'enable_schedule_viewing'       => "1",
                        'enable_more_info'              => "1",
                        'enable_print'                  => "1",
                        'enable_admin_notification'     => "1",
                        'show_filter_info'              => "1",
                        'default_viewmode'              => "1",
                        'show_powered_by'               => "1",
                        'enable_sticky_header'          => "1",
                        'price_row_format'              => "1",
                        'display_tryon'                 => "0",
                        'announcement_text'             => "",
                        'announcement_text_rbdetail'    => "",
                        'google_site_key'               => "",
                        'google_secret_key'             => "",
                        'type_1'                        => "0",
                        'settings_carat_ranges'         => '{"0.25":[0.2,0.3],"0.33":[0.31,0.4],"0.50":[0.41,0.65],"0.75":[0.66,0.85],"1.00":[0.86,1.14],"1.25":[1.15,1.40],"1.50":[1.41,1.65],"1.75":[1.66,1.85],"2.00":[1.86,2.15],"2.25":[2.16,2.45],"2.50":[2.46,2.65],"2.75":[2.66,2.85],"3.00":[2.85,3.25]}',
                        'created_at'                    => date('Y-m-d h:i:s'),
                        'updated_at'                    => date('Y-m-d h:i:s'),
                    ];
                    $saveSmtpData =  DB::table('ringbuilder_config')->insert($inserSettingsData);
                    $saveSmtpData =  DB::table('customer')->insert($insertData);
                }
            } else {
                return response()->json(['message' => 'Please enter a Valid Email', 'status' => 'fail']);
            }


            $userId = DB::table('users')->where(['name' => $requestData['data']['shopDomain']])->value('id');
            $price = DB::table('charges')->where(['user_id' => $userId])->orderBy('created_at', 'DESC')->value('price');
            $domain = $value['shopDomain'];
            // echo "<pre>";print_r($price);exit();
            //HUBSPOT API
            $arr = array(
                'filters' => array(
                    array(
                        'propertyName' => 'email',
                        'operator' => 'EQ',
                        'value' => $value['email'],
                    )
                )
            );
            $path = public_path();
            $post_json = json_encode($arr);
            // $file = "array_log.txt";
            // file_put_contents($file, $post_json);
            file_put_contents($path . '/array_log.txt', $post_json);
            $email_id = $value['email'];
            $endpoint = 'https://api.hubapi.com/contacts/v1/contact/email/' . $email_id . '/profile?hapikey=ee625d9a-7fde-44b5-b026-d5f771cfc343';
            $ch = curl_init();
            //curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_json);
            curl_setopt($ch, CURLOPT_URL, $endpoint);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_errors = curl_error($ch);
            curl_close($ch);

            // $file = "install_status_log.txt";
            //file_put_contents($path.'/install_status_log.txt',$status_code);
            file_put_contents($path . '/' . $domain . "-install_status_log" . time() . ".txt", $status_code);

            // file_put_contents($file, $status_code);

            // $file = "install_response_log.txt";
            // file_put_contents($path.'/install_response_log.txt',$response);
            file_put_contents($path . '/' . $domain . "-install_response_log" . time() . ".txt", $response);


            // file_put_contents($file, $response);

            $current_date = date('Y-m-d H:i:s');
            if ($status_code == 200) {
                $arr1 = array(
                    'properties' => array(
                        array(
                            'property' => 'email',
                            'value' => $value['email']
                        ),
                        array(
                            'property' => 'Install_Date',
                            'value' => $current_date
                        ),
                        array(
                            'property' => 'app_status',
                            'value' => 'REGISTER-RINGBUILDER-2.0'
                        )
                    )
                );
                $post_json1 = json_encode($arr1);
                // file_put_contents('update_data.log', $post_json1);
                file_put_contents($path . '/update_data.txt', $post_json1);

                $email_id1 = $value['email'];
                $endpoint1 = 'https://api.hubapi.com/contacts/v1/contact/email/' . $email_id1 . '/profile?hapikey=ee625d9a-7fde-44b5-b026-d5f771cfc343';

                $ch1 = curl_init();
                curl_setopt($ch1, CURLOPT_POST, true);
                curl_setopt($ch1, CURLOPT_POSTFIELDS, $post_json1);
                curl_setopt($ch1, CURLOPT_URL, $endpoint1);
                curl_setopt($ch1, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
                $response1 = curl_exec($ch1);
                $status_code1 = curl_getinfo($ch1, CURLINFO_HTTP_CODE);
                $curl_errors1 = curl_error($ch1);
                curl_close($ch1);

                // $file = "install_status_log2.txt";
                file_put_contents($path . '/' . $domain . "-customer_reinstall_status_log" . time() . ".txt", $status_code1);

                // file_put_contents($file, $status_code1);

                // $file = "install_response_log2.txt";
                //file_put_contents($path.'/customer_reinstall_response_log.txt',$response1);
                file_put_contents($path . '/' . $domain . "-customer_reinstall_response_log" . time() . ".txt", $response1);

                // file_put_contents($file, $response1);
            } else {
                $arr2 = array(
                    'properties' => array(
                        array(
                            'property' => 'email',
                            'value' => $value['email']
                        ),
                        array(
                            'property' => 'shop_name',
                            'value' => $value['shopDomain']
                        ),
                        array(
                            'property' => 'domain_name',
                            'value' => $value['shopDomain']
                        ),
                        array(
                            'property' => 'phone',
                            'value' => $value['telephone']
                        ),
                        array(
                            'property' => 'state',
                            'value' => $value['state']
                        ),
                        array(
                            'property' => 'address',
                            'value' => $value['address']
                        ),
                        array(
                            'property' => 'city',
                            'value' => $value['city']
                        ),
                        array(
                            'property' => 'ShopifyRB_Service_Fee',
                            'value' => $price
                        ),
                        array(
                            'property' => 'Install_Date',
                            'value' => $current_date
                        ),
                        array(
                            'property' => 'app_status',
                            'value' => 'REGISTER-RINGBUILDER-2.0'
                        )
                    )
                );
                $post_json2 = json_encode($arr2);
                // $file = "post_data_log3.txt";
                file_put_contents($path . '/post_data_log3.txt', $post_json2);
                // file_put_contents($file, $post_json2);
                $endpoint2 = 'https://api.hubapi.com/contacts/v1/contact?hapikey=ee625d9a-7fde-44b5-b026-d5f771cfc343';
                $ch2 = curl_init();
                curl_setopt($ch2, CURLOPT_POST, true);
                curl_setopt($ch2, CURLOPT_POSTFIELDS, $post_json2);
                curl_setopt($ch2, CURLOPT_URL, $endpoint2);
                curl_setopt($ch2, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
                $response2 = curl_exec($ch2);
                $status_code2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
                $curl_errors2 = curl_error($ch2);
                curl_close($ch2);
                // $file = "install_response_log3.txt";
                //file_put_contents($path.'/customer_install_response_log.txt',$response2);
                file_put_contents($path . '/' . $domain . "-customer_install_response_log" . time() . ".txt", $response2);

                // file_put_contents($file, $response2);
                // $file = "install_status_log3.txt";
                //file_put_contents($path.'/customer_install_status_log.txt',$status_code2);
                file_put_contents($path . '/' . $domain . "-customer_install_status_log" . time() . ".txt", $status_code2);


                // file_put_contents($file, $status_code2);
            }
        }

        //MAIL TO USER
        $data = [
            'business' => $value['business'],
            'fullname' => $value['fullname'],
            'address' => $value['address'],
            'state' => $value['state'],
            'city' => $value['city'],
            'zipcode' => $value['zipcode'],
            'telephone' => $value['telephone'],
            'website' => $value['website'],
            'email' => $value['email'],
            'notes' => $value['notes'],
        ];
        $user['to'] = $value['email'];
        Mail::send('customerDetails', $data, function ($messages) use ($user) {
            $messages->to($user['to']);
            $messages->subject('Customer Information Form');
        });
        return response()->json(['message' => 'Customer added successfully', 'status' => 'success']);
        // exit;
    }

    function saveSettings(Request $request)
    {
        $requestData = $request->all();
        // echo '<pre>';print_r($requestData);exit;
        foreach ($requestData as $value) {
            if ($value['dealerId'] == '') {
                return response()->json(['message' => 'Name of Business field is required', 'status' => 'fail']);
            }
            if ($value['adminEmail'] == '') {
                return response()->json(['message' => 'First and Last name field is required', 'status' => 'fail']);
            }
            // if($value['shopLogo'] == ''){
            //     return response()->json(['message'=>'Shop Logo Url field is required','status'=>'fail']);
            // }
            // if($value['announcementText'] == ''){
            //     return response()->json(['message'=>'Top TextArea field is required','status'=>'fail']);
            // }
            // if($value['announceRbDetail'] == ''){
            //     return response()->json(['message'=>'Ring Details TextArea field is required','status'=>'fail']);
            // }
            if ($value['valueCarat'] == '') {
                return response()->json(['message' => 'Settings Carat Ranges field is required', 'status' => 'fail']);
            }
            $updateData = [
                'dealerid'                      => $value['dealerId'],
                'admin_email_address'           => $value['adminEmail'],
                'enable_hint'                   => $value['enableHint'][0],
                'enable_email_friend'           => $value['enableEmail'][0],
                'enable_schedule_viewing'       => $value['enableSchedule'][0],
                'enable_more_info'              => $value['enableInfo'][0],
                'enable_print'                  => $value['enablePrint'][0],
                'enable_admin_notification'     => $value['enableNotification'][0],
                'show_filter_info'              => $value['showInfo'][0],
                'default_viewmode'              => $value['diamondListing'][0],
                'show_powered_by'               => $value['showPowered'][0],
                'enable_sticky_header'          => $value['enableSticky'][0],
                'price_row_format'              => $value['priceLocation'][0],
                // 'shop_logo'                     => $value['shopLogo'],
                'display_tryon'                 => $value['checked'],
                // 'settings_carat_ranges'         => $value['valueCarat'],
                'announcement_text'             => $value['announcementText'],
                'announcement_text_rbdetail'    => $value['announceRbDetail'],
                'google_site_key'               => $value['googleSiteKey'],
                'google_secret_key'             => $value['googleSecretKey'],
                'created_at'                    => date('Y-m-d h:i:s'),
                'updated_at'                    => date('Y-m-d h:i:s'),
            ];
            $saveSettingsData =  DB::table('ringbuilder_config')->where(['shop' => $value['shopDomain']])->update($updateData);
            $getSettingsData = DB::table('ringbuilder_config')->where(['shop' => $value['shopDomain']])->first();
            $type_1 = "";
            if (empty($getSettingsData->type_1)) {
                $type_1 = "0";
            }
        }
        return response()->json(['message' => 'Settings saved successfully', 'status' => 'success', 'data' => $type_1]);
    }

    public static function getSettingsData($shopDomain)
    {
        $getSettingsData = DB::table('ringbuilder_config')->where(['shop' => $shopDomain])->first();
        return $getSettingsData;
    }

    public function getMetafields($shopDomain, $productId)
    {

        //  header('Access-Control-Allow-Origin: *');
        $productGID = "gid://shopify/Product/" . $productId;
        $shop = User::where('name', $shopDomain)->firstOrFail();
        $url = 'https://' . $shopDomain . '/admin/api/2022-10/graphql.json';
        $qry = '{
                    product(id: "' . $productGID . '") {
                        metafields(first: 100) {
                                edges {
                                    node {
                                        id
                                        key
                                        namespace
                                        description
                                        value
                                    }
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
                'X-Shopify-Access-Token:' . $shop['password']
            )
        );
        $server_output = curl_exec($ch);
        $metafields = json_decode($server_output, true);
        //  echo '<pre>';print_r($metafields);exit;
        //  echo "somin";
        // exit;

        if ($metafields) {
            $metaArray = $metafields['data']['product']['metafields']['edges'];
            $fArray = [];
            foreach ($metaArray as $value) {
                $key = $value['node']['key'];
                $fArray[$key] = $value['node'];
            }
            // if (array_key_exists('ringSize', $fArray)) {
            //     $explode = explode(',',$fArray['ringSize']['value']);
            //     // echo '<pre>';print_r($explode);
            //     $meta_array =  $explode;
            // }
            // exit;
            return $fArray;
        }
        // echo '<pre>';print_r($ringSize);
    }

    public static function getCurrentVersion()
    {
        // $version = env('SHOPIFY_RINGBUILDER_VERSION','2.0');
        $version = "2.0";
        // echo '<pre>';print_r($version);exit;

        return response()->json(['message' => 'Settings saved successfully', 'status' => 'success', 'data' => $version]);
    }

    public static function saveCSSConfiguration(Request $request)
    {
        // echo '<pre>';print_r($request->all());exit;
        $requestData = $request->all();
        $shopDomain = $requestData['data']['shop_domain'];
        $cssExists = CssConfigure::where(['shop' => $shopDomain])->first();
        $data = [
            'shop'          => $requestData['data']['shop_domain'],
            'link'          => $requestData['data']['linkColour'],
            'header'        => $requestData['data']['headerColour'],
            'button'        => $requestData['data']['buttonColour'],
            'slider'        => $requestData['data']['sliderColour'],
            'hover'         => $requestData['data']['hoverColour'],
            'created_at'    => date('Y-m-d'),
        ];
        if ($cssExists) {
            $updateData = CssConfigure::where(['shop' => $shopDomain])->update($data);
        } else {
            $updateData = CssConfigure::where(['shop' => $shopDomain])->insert($data);
        }

        return response()->json(['message' => 'CSS Configuration saved successfully', 'status' => 'success', 'data' => []]);
    }

    public static function getCSSConfiguration($shopDomain)
    {
        $cssConfig = CssConfigure::where(['shop' => $shopDomain])->first();
        if ($cssConfig) {
            return response()->json(['message' => 'CSS Configuration saved successfully', 'status' => 'success', 'data' => $cssConfig]);
        } else {
            return response()->json(['message' => 'CSS Configuration saved successfully', 'status' => 'success', 'data' => []]);
        }
    }
}
