<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\User;
use App\Jobs\ImportProductsJob;
use File;

class ImportController extends Controller
{

    function importApi(Request $request)
    {
        $requestData = $request->all();
        // echo '<pre>';print_r($requestData);exit;
        foreach ($requestData as $value) {
            if ($value['value'] == '') {
                return response()->json(['message' => 'Please select any options for your Ring Builder Settings', 'status' => 'fail']);
            }
            if ($value['value'] == 1) {
                $saveImportData =  DB::table('ringbuilder_config')->where(['shop' => $value['shopDomain']])->update(['type_1' => "1"]);
                return response()->json(['message' => 'Settings saved as Api successfully', 'status' => 'success', 'data' => "1"]);
            } elseif ($value['value'] == 2) {
                self::insertCollection($requestData);
                // echo '<pre>';print_r($finalCollectionData);exit;
                // if($value['enableImport'] == 'csv'){
                //     $saveImportData =  DB::table('ringbuilder_config')->where(['shop'=> $value['shopDomain']])->update(['type_1'=>"2"]);
                //     return response()->json(['message'=>'Settings saved as CSV Import successfully','status'=>'success','data'=>"2"]);
                // }else{
                $saveImportData =  DB::table('ringbuilder_config')->where(['shop' => $value['shopDomain']])->update(['type_1' => "2"]);
                return response()->json(['message' => 'Settings saved as CSV Import successfully', 'status' => 'success', 'data' => "2"]);
                // }
            }
        }
    }

    function insertCollection($requestData)
    {
        $path = public_path();
        $shop = User::where('name', $requestData['data']['shopDomain'])->firstOrFail();
        file_put_contents($path . '/shopImport.txt', $shop);
        $shop_arr = json_decode($shop, true);
        $theme = $shop->api()->rest('GET', '/admin/themes.json')['body'];
        $theme_data = json_encode($theme);
        $theme_array = json_decode($theme_data, true);

        // echo '<pre>';print_r($theme_array);exit;
        foreach ($theme_array['themes'] as $key => $value) {
            if ($value['role'] == 'main') {
                $current_themeId = $value['id'];
            }
        }
        sleep(5);

        // READ CONTENT OF THEME LIQUID
        $configure = $shop->api()->rest('GET', '/admin/themes/' . $current_themeId . '/assets.json', [
            'asset' => [
                'key'      => 'layout/theme.liquid',
                'theme_id' => $current_themeId
            ]
        ]);
        $configureData = json_encode($configure);
        $configureArray = json_decode($configureData, true);
        $themeContent = $configureArray['body']['asset']['value'];

        // INCLUDE CSS & JS IN THEME LIQUID
        $head = "{% if template.name == 'product' %}{% if product.type == 'RingBuilderAdvance'%}<style>.product-form__quantity {display: none;}.product-form__buttons{display: none;}.product-form__cart-submit {display: none;}.shopify-payment-button {display: none;}</style>{% endif %}{% endif %}</head>";
        $newContent = str_replace("</head>", $head, $themeContent);

        // MODIFY THEME LIQUID FILE
        $shop->api()->rest('PUT', '/admin/themes/' . $current_themeId . '/assets.json', [
            'asset' => [
                'key'   => 'layout/theme.liquid',
                'value' => $newContent
            ]
        ]);
        // // CREATE MAIN PRODUCT RINGBUILDER TEMPLATE
        // $mainProductTemplate = public_path() . '/ringbuilder/main-product-ringbuilder.liquid';
        // $shop->api()->rest('PUT', '/admin/themes/' . $current_themeId . '/assets.json', [
        //     'asset' => [
        //         'key'   => 'sections/main-product-ringbuilder.liquid',
        //         'value' => file_get_contents($mainProductTemplate)
        //     ]
        // ]);

        // // CREATE MAIN PRODUCT RINGBUILDER TEMPLATE
        // $add_diamondTemplate = public_path() . '/ringbuilder/add_diamond.liquid';
        // $shop->api()->rest('PUT', '/admin/themes/' . $current_themeId . '/assets.json', [
        //     'asset' => [
        //         'key'   => 'sections/add_diamond.liquid',
        //         'value' => file_get_contents($add_diamondTemplate)
        //     ]
        // ]);

        // // CREATE HEADER TEMPLATE
        // $headerTemplate = public_path() . '/ringbuilder/ringbuilder_header.liquid';
        // $shop->api()->rest('PUT', '/admin/themes/' . $current_themeId . '/assets.json', [
        //     'asset' => [
        //         'key'   => 'sections/ringbuilder_header.liquid',
        //         'value' => file_get_contents($headerTemplate)
        //     ]
        // ]);
        // // CREATE COLLECTION TEMPLATE
        // $collectionTemplate = public_path() . '/ringbuilder/collection_ringbuilder_settings.json';
        // $shop->api()->rest('PUT', '/admin/themes/' . $current_themeId . '/assets.json', [
        //     'asset' => [
        //         'key'   => 'templates/collection.ringbuilder_settings.json',
        //         'value' => file_get_contents($collectionTemplate)
        //     ]
        // ]);

        // // CREATE PRODUCT TEMPLATE
        // $productTemplate = public_path() . '/ringbuilder/product_ringbuilder_settings.json';
        // $shop->api()->rest('PUT', '/admin/themes/' . $current_themeId . '/assets.json', [
        //     'asset' => [
        //         'key'   => 'templates/product.ringbuilder_settings.json',
        //         'value' => file_get_contents($productTemplate)
        //     ]
        // ]);

        // // CREATE JS TEMPLATE
        // $jsTemplate = public_path() . '/ringbuilder/ringbuilder_settings.js';
        // $shop->api()->rest('PUT', '/admin/themes/' . $current_themeId . '/assets.json', [
        //     'asset' => [
        //         'key'   => 'assets/ringbuilder_settings.js',
        //         'value' => file_get_contents($jsTemplate)
        //     ]
        // ]);

        // // CREATE CSS TEMPLATE
        // $cssTemplate = public_path() . '/ringbuilder/ringbuilder_settings.css';
        // $shop->api()->rest('PUT', '/admin/themes/' . $current_themeId . '/assets.json', [
        //     'asset' => [
        //         'key'   => 'assets/ringbuilder_settings.css',
        //         'value' => file_get_contents($cssTemplate)
        //     ]
        // ]);

        // // CREATE DROP HINT SNIPPET
        // $cssTemplate = public_path() . '/ringbuilder/drop_hint.liquid';
        // $shop->api()->rest('PUT', '/admin/themes/' . $current_themeId . '/assets.json', [
        //     'asset' => [
        //         'key'   => 'snippets/drop_hint.liquid',
        //         'value' => file_get_contents($cssTemplate)
        //     ]
        // ]);

        // // CREATE REQUEST INFO SNIPPET
        // $cssTemplate = public_path() . '/ringbuilder/email_friend.liquid';
        // $shop->api()->rest('PUT', '/admin/themes/' . $current_themeId . '/assets.json', [
        //     'asset' => [
        //         'key'   => 'snippets/email_friend.liquid',
        //         'value' => file_get_contents($cssTemplate)
        //     ]
        // ]);

        // // CREATE EMAIL FRIEND SNIPPET
        // $cssTemplate = public_path() . '/ringbuilder/request_info.liquid';
        // $shop->api()->rest('PUT', '/admin/themes/' . $current_themeId . '/assets.json', [
        //     'asset' => [
        //         'key'   => 'snippets/request_info.liquid',
        //         'value' => file_get_contents($cssTemplate)
        //     ]
        // ]);

        // // CREATE SCHEDULEVIEW SNIPPET
        // $cssTemplate = public_path() . '/ringbuilder/schedule_view.liquid';
        // $shop->api()->rest('PUT', '/admin/themes/' . $current_themeId . '/assets.json', [
        //     'asset' => [
        //         'key'   => 'snippets/schedule_view.liquid',
        //         'value' => file_get_contents($cssTemplate)
        //     ]
        // ]);

        // // CREATE RINGBUIDER SUCCESS FILE
        // $successTemplate = public_path() . '/ringbuilder/ringbuilder_success_modal.liquid';
        // $shop->api()->rest('PUT', '/admin/themes/' . $current_themeId . '/assets.json', [
        //     'asset' => [
        //         'key'   => 'snippets/ringbuilder_success_modal.liquid',
        //         'value' => file_get_contents($successTemplate)
        //     ]
        // ]);

        //CHECKING COLLECTION IF EXISTS BY HANDLE
        $url = 'https://' . $requestData['data']['shopDomain'] . '/admin/api/2020-07/graphql.json';
        $qry = '{
                    collectionByHandle(handle: "ringbuilder-settings") {
                        id
                        handle
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
                'X-Shopify-Access-Token:' . $shop_arr['password']
            )
        );
        $server_output = curl_exec($ch);
        $collectionByHandle = json_decode($server_output, true);
        // echo '<pre>';print_r($collectionByHandle);exit;
        $final_handle = $collectionByHandle['data']['collectionByHandle'];
        if (empty($final_handle)) {
            $collections_array = array(
                "smart_collection" => array(
                    "title"             => "Ringbuilder Settings",
                    "template_suffix"   => "ringbuilder_settings",
                    "rules"             => [
                        [
                            "column"    => "type",
                            "relation"  => "equals",
                            "condition" => "RingBuilderAdvance",
                        ]
                    ]
                )
            );
            $create_collections = $shop->api()->rest('POST', '/admin/smart_collections.json', $collections_array);
            $collections_data = json_encode($create_collections);
            $finalCollectionData = json_decode($collections_data);
        }
    }

    function getImportData($shopDomain)
    {
        $importData = DB::table('ringbuilder_config')->where(['shop' => $shopDomain])->value('type_1');
        if (!empty($importData)) {
            return $importData;
        } else {
            return 0;
        }
    }

    function getCsvFile()
    {
        return response()->streamDownload(function () {
            echo file_get_contents('https://rb-advance-dev.partners.gemfind.com/product_template.csv');
        }, 'nice-name.jpg');
    }

    function findImages($arr)
    {
        $extraimage = array();
        $i = 0;
        foreach ($arr as $key => $value) {
            if (preg_match('/^Extra_image_/', $key)) {
                if (!empty($value)) {
                    $extraimage[] = $value;
                }
                $i++;
            }
        }
        return $extraimage;
    }

    function createMetaFields($shop, $name, $password)
    {
        //CREATE METAFIELDS WITH DEFINITIONS
        $urlMeta = 'https://' . $shop . '/admin/api/2022-04/graphql.json';
        // echo '<pre>';print_r($shop);
        // echo '<pre>';print_r($name);
        $qryMeta = '{
            "query": "mutation CreateMetafieldDefinition($definition: MetafieldDefinitionInput!) { metafieldDefinitionCreate(definition: $definition) { createdDefinition { id name } userErrors { field message code } } }",
             "variables": {
                "definition": {
                  "name": "' . ucfirst($name) . '",
                  "namespace": "Product",
                  "key": "' . $name . '",
                  "description": "A list of ingredients used to make the product.",
                  "value_type": "single_line_text_field",
                  "ownerType": "PRODUCT"
                }
              }
            }';
        $ch = curl_init($urlMeta);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $qryMeta);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'X-Shopify-Access-Token:' . $password
            )
        );
        $server_output = curl_exec($ch);
        return $server_output;
    }

    function findMetaFields($arr)
    {
        $metafields = array();
        $i = 0;
        foreach ($arr as $key => $value) {
            if (preg_match('/^metafields_/', $key)) {
                if (!empty($value)) {
                    $metafields[$i]['key'] = $key;
                    $metafields[$i]['value'] = $value;
                }
                $i++;
            }
        }
        return $metafields;
    }

    function productImages($filedata, $product_id, $shop)
    {
        $findimage = $this->findImages($filedata);
        // echo '<pre>';print_r($findimage);
        $MainImage = array($filedata['MainImage']);
        if (!empty($MainImage) && !empty($findimage)) {
            $attachment = array_merge($MainImage, $findimage);
        } elseif (!empty($MainImage) && empty($findimage)) {
            $attachment = $MainImage;
        } elseif (empty($MainImage) && !empty($findimage)) {
            $attachment = $findimage;
        }
        $base64Image = array();
        foreach ($attachment as $key => $image) {
            $image_array = array(
                "image" => array(
                    "attachment" => base64_encode(file_get_contents($image))
                ),
            );
            $create_product_image = $shop->api()->rest('POST', '/admin/products/' . $product_id . '/images.json', $image_array);
        }
    }

    function csvImportApi(Request $request)
    {
        $requestData = $request->all();
        $shop = $requestData['data']['shopDomain'];
        dispatch(new \App\Jobs\ImportProductsJob($requestData, $shop))->delay(now()->addSecond());
        $msg = array();
        $msg['message'] = 'Products will be imported in background...';
        $msg['status']  = 'success';
        $msg['data'] = [];
        return response()->json($msg);
    }

    function dispatchCsvImportApi($requestData)
    {
        $filemainarray = $requestData['data']['file'];
        $count = 0;
        // echo '<pre>';print_r($requestData);exit;
        foreach ($filemainarray as $filedata) {
            //THIS IS FOR CHECKING SIMPLE PRODUCT WITHOUT PARENT AND CHILD SKU
            if (isset($filedata['Product_Type']) && $filedata['Product_Type'] == 'simple') {
                //THIS IS TO CREATE SIMPLE PRODUCT WITHOUT VARIATION
                if ($filedata['Parent_Sku'] == '') {
                    //GET ALL THE METAFIELDS OF THE PROJECT
                    $metafields_data = $this->findMetaFields($filedata);
                    $product_metafield = array();
                    $k = 0;
                    foreach ($metafields_data as $key => $value) {
                        $metakey = explode('metafields_', $value['key']);
                        $product_metafield[$k]['namespace'] = "Product";
                        $product_metafield[$k]['key'] = $metakey[1];
                        $product_metafield[$k]['value'] = $value['value'];
                        $product_metafield[$k]['type'] = "single_line_text_field";
                        $product_metafield[$k]['name'] = "product";
                        $k++;
                    }
                    $createDynamicAttribute = array();
                    $selectedAttributes = explode(',', $filedata['Configurable_Attributes']);
                    //    print_r($selectedAttributes);
                    // exit;
                    if (count($selectedAttributes) > 3) {
                        return response()->json(['message' => 'You can not add more than 3 Configurable Attributes.', 'status' => 'fail', 'data' => []]);
                    }
                    $i = 0;
                    foreach ($selectedAttributes as $key => $value) {
                        $createDynamicAttribute[$i]['name'] = $value;
                        $i++;
                    }
                    $variantsData = array();
                    if (isset($selectedAttributes) && !empty($selectedAttributes[0])) {
                        $option1 = $filedata[$selectedAttributes[0]];
                    } else {
                        $option1 = '';
                    }
                    if (isset($selectedAttributes[1]) && !empty($selectedAttributes[1])) {
                        $option2 = $filedata[$selectedAttributes[1]];
                    } else {
                        $option2 = '';
                    }
                    if (isset($selectedAttributes[2]) && !empty($selectedAttributes[2])) {
                        $option3 = $filedata[$selectedAttributes[2]];
                    } else {
                        $option3 = '';
                    }

                    $variantsData = [
                        [
                            'sku'       => $filedata["Sku"],
                            'price'     => $filedata["Price"],
                            'compare_at_price' => $filedata["Compare_Price"] ? $filedata["Compare_Price"] : "",
                            'option1'   => $option1,
                            'option2'   => $option2,
                            'option3'   => $option3,
                        ]
                    ];
                    //CONVERT TAGS TO ARRAY
                    $converted_tags = explode(',', $filedata['Tags']);
                    $products_array = array(
                        "product" => array(
                            "title"             => $filedata['Name'],
                            "body_html"         => $filedata['Description'],
                            "template_suffix"   => "ringbuilder_settings",
                            "vendor"            => $filedata['Vendor'],
                            "product_type"      => $filedata['Type'],
                            "published"         => $filedata['Status'],
                            "tags"              => $converted_tags,
                            "metafields"        => $product_metafield,
                            "options"           => $createDynamicAttribute,
                            "variants"          => $variantsData,
                        )
                    );

                    // echo '<pre>';print_r($products_array);exit;
                    $shop = User::where('name', $requestData['data']['shopDomain'])->firstOrFail();
                    $create_product = $shop->api()->rest('POST', '/admin/products.json', $products_array);
                    $product_data = json_encode($create_product);
                    $finalProductData = json_decode($product_data);
                    $product_id = $finalProductData->body->product->id;
                    if ($product_id) {
                        $pathLog = public_path() . '/' . $requestData['data']['shopDomain'];
                        $string = $product_id . '|' . $filedata['Name'] . '|' . date('Y-m-d h:i:s') . '|||';
                        file_put_contents($pathLog . '/product_import_success.txt', $string, FILE_APPEND);
                    } else {
                        $pathLog = public_path() . '/' . $requestData['data']['shopDomain'];
                        $string_1 = $filedata['Name'] . '|' . date('Y-m-d h:i:s') . '|' . $product_data . '|||';
                        file_put_contents($pathLog . '/product_import_fail.txt', $string_1, FILE_APPEND);
                    }
                    //CREATE IMAGE
                    $uploadProductImage = $this->productImages($filedata, $product_id, $shop);
                    //END IMPORT IMAGE
                    //CREATE VARIANTS
                    //CREATE METAFIELDS FOR PRODUCT
                    // foreach ($product_metafield as $key => $value) {
                    //     $data = array('metafield'=>$value);
                    //     $createmeta_fields = $shop->api()->rest('POST','/admin/products/'.$product_id.'/metafields.json',$data);
                    // }
                    $count++;
                }
            } elseif (isset($filedata['Product_Type']) && $filedata['Product_Type'] == 'configurable') {

                if ($filedata['Parent_Sku'] == '' && $filedata['Child_Sku'] != '') {
                    $metafields_data = $this->findMetaFields($filedata);
                    $product_metafield = array();
                    $k = 0;
                    foreach ($metafields_data as $key => $value) {
                        $metakey = explode('metafields_', $value['key']);
                        $product_metafield[$k]['namespace'] = "Product";
                        $product_metafield[$k]['key'] = $metakey[1];
                        $product_metafield[$k]['value'] = $value['value'];
                        $product_metafield[$k]['type'] = "single_line_text_field";
                        $product_metafield[$k]['name'] = "product";
                        $k++;
                    }

                    //CONVERT TAGS TO ARRAY
                    $converted_tags = explode(',', $filedata['Tags']);
                    $createDynamicAttribute = array();
                    //THIS IS FOR SELECTED ATTRIBUTES CREATE IN SHOPIFY
                    $selectedAttributes = explode(',', $filedata['Configurable_Attributes']);
                    if (count($selectedAttributes) > 3) {
                        return response()->json(['message' => 'You can not add more than 3 Configurable Attributes.', 'status' => 'fail', 'data' => []]);
                    }
                    $i = 0;
                    foreach ($selectedAttributes as $key => $value) {
                        $createDynamicAttribute[$i]['name'] = $value;
                        $i++;
                    }
                    //CREATE VARIATION DATE
                    $selectedChildSku = explode(',', $filedata['Child_Sku']);
                    $variantsData = array();
                    $j = 0;
                    foreach ($selectedChildSku as $key => $value) {
                        $arraykey = array_search($value, array_column($filemainarray, 'Sku'));
                        $variantsData[$j]['sku'] = $filemainarray[$arraykey]["Sku"];
                        $variantsData[$j]['price'] = $filemainarray[$arraykey]["Price"];
                        $variantsData[$j]['compare_at_price'] = $filemainarray[$arraykey]["Compare_Price"] ? $filemainarray[$arraykey]["Compare_Price"] : "";

                        if (isset($selectedAttributes) && !empty($selectedAttributes[0])) {
                            $variantsData[$j]['option1'] = $filemainarray[$arraykey][$selectedAttributes[0]];
                        }
                        if (isset($selectedAttributes[1]) && !empty($selectedAttributes[1])) {
                            $variantsData[$j]['option2'] = $filemainarray[$arraykey][$selectedAttributes[1]];
                        }
                        if (isset($selectedAttributes[2]) && !empty($selectedAttributes[2])) {
                            $variantsData[$j]['option3'] = $filemainarray[$arraykey][$selectedAttributes[2]];
                        }
                        $j++;
                    }
                    $products_array = array(
                        "product" => array(
                            "title"        => $filedata['Name'],
                            "template_suffix"   => "ringbuilder_settings",
                            "body_html"    => $filedata['Description'],
                            "vendor"       => $filedata['Vendor'],
                            "metafields"        => $product_metafield,
                            "product_type" => $filedata['Type'],
                            "published"    => $filedata['Status'],
                            "tags"    => $converted_tags,
                            "options" => $createDynamicAttribute,
                            "variants" => $variantsData,
                        )
                    );
                    // echo '<pre>';
                    // print_r($products_array);
                    // exit;
                    $shop = User::where('name', $requestData['data']['shopDomain'])->firstOrFail();
                    $create_product = $shop->api()->rest('POST', '/admin/products.json', $products_array);
                    $product_data = json_encode($create_product);
                    $finalProductData = json_decode($product_data);
                    // echo '<pre>';
                    // print_r($finalProductData);
                    $product_id = $finalProductData->body->product->id;
                    if ($product_id) {
                        $pathLog = public_path() . '/' . $requestData['data']['shopDomain'];
                        $string = $product_id . '|' . $filedata['Name'] . '|||';
                        file_put_contents($pathLog . '/product_import_success.txt', $string, FILE_APPEND);
                    } else {
                        $pathLog = public_path() . '/' . $requestData['data']['shopDomain'];
                        $string_1 = $filedata['Name'] . '|||';
                        file_put_contents($pathLog . '/product_import_fail.txt', $string_1, FILE_APPEND);
                    }
                    //CREATE IMAGE
                    $uploadProductImage = $this->productImages($filedata, $product_id, $shop);
                    //END IMPORT IMAGE
                    //CREATE METAFIELDS FOR PRODUCT
                    // foreach ($product_metafield as $key => $value) {
                    //     $data = array('metafield'=>$value);
                    //     $createmeta_fields = $shop->api()->rest('POST','/admin/products/'.$product_id.'/metafields.json',$data);
                    // }
                    //END PRODUCT VARIATION
                    //CREATE METAFIELDS FOR VARIATION
                    foreach ($finalProductData->body->product->variants as $key => $value) {
                        $arraykey = array_search($value->sku, array_column($filemainarray, 'Sku'));
                        $metafieldsdata = $this->findMetaFields($filemainarray[$arraykey]);
                        $product_metafield = array();
                        $k = 0;
                        foreach ($metafieldsdata as $key => $valuemeta) {
                            $metakey = explode('metafields_', $valuemeta['key']);
                            $product_metafield[$k]['namespace'] = "Product";
                            $product_metafield[$k]['key'] = $metakey[1];
                            $product_metafield[$k]['value'] = $valuemeta['value'];
                            $product_metafield[$k]['type'] = "single_line_text_field";
                            $product_metafield[$k]['name'] = "product";
                            $k++;
                        }

                        //VARIENT API CALL
                        foreach ($product_metafield as $key => $valueproduct) {
                            $data = array('metafield' => $valueproduct);
                            $createmeta_fields = $shop->api()->rest('POST', '/admin/products/' . $product_id . '/variants/' . $value->id . '/metafields.json', $data);
                            // file_put_contents('MissingProducttest.txt', '=='.json_encode($createmeta_fields));
                        }
                    }
                    //END PRODUCT VARIATION
                    $count++;
                }
            }
        }
    }

    function jsonImportApi(Request $request)
    {
        $requestData = $request->all();
        $count = 0;
        $shop = User::where('name', $requestData['data']['shopDomain'])->firstOrFail();
        $filemainarray = json_decode($requestData['data']['file'], TRUE);
        $path = public_path() . '/' . $requestData['data']['shopDomain'];
        File::makeDirectory($path, $mode = 0777, true, true);
        $checkMetafields = $this->findMetaFields($filemainarray[0]);

        foreach ($checkMetafields as $key => $value) {
            $metakey = explode('metafields_', $value['key']);
            $create = self::createMetaFields($shop->name, $metakey[1], $shop->password);
        }

        sleep(5);
        foreach ($filemainarray as $filedata) {
            //THIS IS FOR CHECKING SIMPLE PRODUCT WITHOUT PARENT AND CHILD SKU
            if (isset($filedata['Product_Type']) && $filedata['Product_Type'] == 'simple') {
                //THIS IS TO CREATE SIMPLE PRODUCT WITHOUT VARIATION
                if ($filedata['Parent_Sku'] == '' && $filedata['Child_Sku'] == '') {
                    //GET ALL THE METAFIELDS OF THE PROJECT
                    $metafields_data = $this->findMetaFields($filedata);
                    $product_metafield = array();
                    $k = 0;
                    foreach ($metafields_data as $key => $value) {
                        $metakey = explode('metafields_', $value['key']);
                        $product_metafield[$k]['namespace'] = "Product";
                        $product_metafield[$k]['key'] = $metakey[1];
                        $product_metafield[$k]['value'] = $value['value'];
                        $product_metafield[$k]['type'] = "single_line_text_field";
                        $product_metafield[$k]['name'] = "product";
                        $k++;
                    }
                    $createDynamicAttribute = array();
                    $selectedAttributes = explode(',', $filedata['Configurable_Attributes']);
                    if (count($selectedAttributes) > 3) {
                        return response()->json(['message' => 'You can not add more than 3 Configurable Attributes.', 'status' => 'fail', 'data' => []]);
                    }

                    $i = 0;
                    foreach ($selectedAttributes as $key => $value) {
                        $createDynamicAttribute[$i]['name'] = $value;
                        $i++;
                    }
                    $variantsData = array();
                    if (isset($selectedAttributes) && !empty($selectedAttributes[0])) {
                        $option1 = $filedata[$selectedAttributes[0]];
                    } else {
                        $option1 = '';
                    }
                    if (isset($selectedAttributes[1]) && !empty($selectedAttributes[1])) {
                        $option2 = $filedata[$selectedAttributes[1]];
                    } else {
                        $option2 = '';
                    }
                    if (isset($selectedAttributes[2]) && !empty($selectedAttributes[2])) {
                        $option3 = $filedata[$selectedAttributes[2]];
                    } else {
                        $option3 = '';
                    }
                    $variantsData = [
                        [
                            'sku'       => $filedata["Sku"],
                            'price'     => $filedata["Price"],
                            'compare_at_price' => $filedata["Compare_Price"] ? $filedata["Compare_Price"] : "",
                            'option1'   => $option1,
                            'option2'   => $option2,
                            'option3'   => $option3,
                        ]
                    ];
                    //CONVERT TAGS TO ARRAY
                    $converted_tags = explode(',', $filedata['Tags']);
                    $products_array = array(
                        "product" => array(
                            "title"             => $filedata['Name'],
                            "template_suffix"   => "ringbuilder_settings",
                            "body_html"         => $filedata['Description'],
                            "vendor"            => $filedata['Vendor'],
                            "metafields"        => $product_metafield,
                            "product_type"      => $filedata['Type'],
                            "published"         => $filedata['Status'],
                            "tags"              => $converted_tags,
                            "options"           => $createDynamicAttribute,
                            "variants"          => $variantsData,
                        )
                    );

                    $shop = User::where('name', $requestData['data']['shopDomain'])->firstOrFail();
                    $create_product = $shop->api()->rest('POST', '/admin/products.json', $products_array);
                    $product_data = json_encode($create_product);
                    $finalProductData = json_decode($product_data);
                    $product_id = $finalProductData->body->product->id;
                    //CREATE IMAGE
                    $uploadProductImage = $this->productImages($filedata, $product_id, $shop);
                    //END IMPORT IMAGE
                    //CREATE METAFIELDS FOR PRODUCT
                    // foreach ($product_metafield as $key => $value) {
                    //     $data = array('metafield'=>$value);
                    //     $createmeta_fields = $shop->api()->rest('POST','/admin/products/'.$product_id.'/metafields.json',$data);
                    // }
                    $count++;
                }
            } elseif (isset($filedata['Product_Type']) && $filedata['Product_Type'] == 'configurable') {

                if ($filedata['Parent_Sku'] == '' && $filedata['Child_Sku'] != '') {
                    $metafields_data = $this->findMetaFields($filedata);
                    $product_metafield = array();
                    $k = 0;
                    foreach ($metafields_data as $key => $value) {
                        $metakey = explode('metafields_', $value['key']);
                        $product_metafield[$k]['namespace'] = "Product";
                        $product_metafield[$k]['key'] = $metakey[1];
                        $product_metafield[$k]['value'] = $value['value'];
                        $product_metafield[$k]['type'] = "single_line_text_field";
                        $product_metafield[$k]['name'] = "product";
                        $k++;
                    }

                    //CONVERT TAGS TO ARRAY
                    $converted_tags = explode(',', $filedata['Tags']);
                    $createDynamicAttribute = array();
                    //THIS IS FOR SELECTED ATTRIBUTES CREATE IN SHOPIFY
                    $selectedAttributes = explode(',', $filedata['Configurable_Attributes']);
                    if (count($selectedAttributes) > 3) {
                        return response()->json(['message' => 'You can not add more than 3 Configurable Attributes.', 'status' => 'fail', 'data' => []]);
                    }
                    $i = 0;
                    foreach ($selectedAttributes as $key => $value) {
                        $createDynamicAttribute[$i]['name'] = $value;
                        $i++;
                    }
                    //CREATE VARIATION DATE
                    $selectedChildSku = explode(',', $filedata['Child_Sku']);
                    $variantsData = array();
                    $j = 0;
                    foreach ($selectedChildSku as $key => $value) {
                        $arraykey = array_search($value, array_column($filemainarray, 'Sku'));
                        $variantsData[$j]['sku'] = $filemainarray[$arraykey]["Sku"];
                        $variantsData[$j]['price'] = $filemainarray[$arraykey]["Price"];
                        $variantsData[$j]['price'] = $filemainarray[$arraykey]["Price"];
                        $variantsData[$j]['compare_at_price'] = $filemainarray[$arraykey]["Compare_Price"] ? $filemainarray[$arraykey]["Compare_Price"] : "";
                        if (isset($selectedAttributes) && !empty($selectedAttributes[0])) {
                            $variantsData[$j]['option1'] = $filemainarray[$arraykey][$selectedAttributes[0]];
                        }
                        if (isset($selectedAttributes[1]) && !empty($selectedAttributes[1])) {
                            $variantsData[$j]['option2'] = $filemainarray[$arraykey][$selectedAttributes[1]];
                        }
                        if (isset($selectedAttributes[2]) && !empty($selectedAttributes[2])) {
                            $variantsData[$j]['option3'] = $filemainarray[$arraykey][$selectedAttributes[2]];
                        }
                        $j++;
                    }
                    $products_array = array(
                        "product" => array(
                            "title"        => $filedata['Name'],
                            "body_html"    => $filedata['Description'],
                            "template_suffix"   => "ringbuilder_settings",
                            "vendor"       => $filedata['Vendor'],
                            "product_type" => $filedata['Type'],
                            "metafields"        => $product_metafield,
                            "published"    => $filedata['Status'],
                            "tags"    => $converted_tags,
                            "options" => $createDynamicAttribute,
                            "variants" => $variantsData,
                        )
                    );
                    $shop = User::where('name', $requestData['data']['shopDomain'])->firstOrFail();
                    $create_product = $shop->api()->rest('POST', '/admin/products.json', $products_array);
                    $product_data = json_encode($create_product);
                    $finalProductData = json_decode($product_data);
                    $product_id = $finalProductData->body->product->id;
                    //CREATE IMAGE
                    $uploadProductImage = $this->productImages($filedata, $product_id, $shop);
                    //END IMPORT IMAGE
                    //CREATE METAFIELDS FOR PRODUCT
                    // foreach ($product_metafield as $key => $value) {
                    //     $data = array('metafield'=>$value);
                    //     $createmeta_fields = $shop->api()->rest('POST','/admin/products/'.$product_id.'/metafields.json',$data);
                    // }
                    //END PRODUCT VARIATION
                    //CREATE METAFIELDS FOR VARIATION
                    foreach ($finalProductData->body->product->variants as $key => $value) {
                        $arraykey = array_search($value->sku, array_column($filemainarray, 'Sku'));
                        $metafieldsdata = $this->findMetaFields($filemainarray[$arraykey]);
                        $product_metafield = array();
                        $k = 0;
                        foreach ($metafieldsdata as $key => $valuemeta) {
                            $metakey = explode('metafields_', $valuemeta['key']);
                            $product_metafield[$k]['namespace'] = "Product";
                            $product_metafield[$k]['key'] = $metakey[1];
                            $product_metafield[$k]['value'] = $valuemeta['value'];
                            $product_metafield[$k]['type'] = "single_line_text_field";
                            $product_metafield[$k]['name'] = "product";
                            $k++;
                        }

                        //VARIENT API CALL
                        foreach ($product_metafield as $key => $valueproduct) {
                            $data = array('metafield' => $valueproduct);
                            $createmeta_fields = $shop->api()->rest('POST', '/admin/products/' . $product_id . '/variants/' . $value->id . '/metafields.json', $data);
                            // file_put_contents('MissingProducttest.txt', '=='.json_encode($createmeta_fields));
                        }
                    }
                    //END PRODUCT VARIATION
                    $count++;
                }
            }
        }
        $msg = array();
        $msg['message'] = $count . ' Products Imported Sucessfully.';
        $msg['status']  = 'success';
        $msg['data'] = [];
        return response()->json($msg);
    }

    public static function getSuccessFileContent($shopDomain)
    {
        $successFile = public_path() . '/' . $shopDomain . '/' . 'product_import_success.txt';
        if (file_exists($successFile)) {
            $getSuccessContent = file_get_contents($successFile);
            $explodeSuccessContent = explode('|||', $getSuccessContent);
            //SUCCESS ARRAY
            foreach ($explodeSuccessContent as $success) {
                if (!empty($success)) {
                    $explodeSuccess = explode('|', $success);
                    $final_success_array[] = [
                        'product_id'    => $explodeSuccess[0],
                        'product_name'  => $explodeSuccess[1],
                        'time'          => $explodeSuccess[2],
                    ];
                }
            }
        } else {
            $explodeSuccessContent = [];
            $final_success_array = [];
        }
        $failedFile = public_path() . '/' . $shopDomain . '/' . 'product_import_fail.txt';
        // echo '<pre>';print_r($failedFile);exit;
        if (file_exists($failedFile)) {
            $getFailedContent = file_get_contents($failedFile);
            $explodeFailedContent = explode('|||', $getFailedContent);
            //FAILED ARRAY
            foreach ($explodeFailedContent as $fail) {
                if (!empty($fail)) {
                    $explodeFail = explode('|', $fail);
                    foreach ($explodeFail as $fSuccess) {
                        $final_fail_array[] = [
                            'product_id'    => $explodeFail[0],
                            'product_name'  => $explodeFail[1],
                            'time'          => $explodeFail[2],
                        ];
                    }
                }
            }
        } else {
            $explodeFailedContent = [];
            $final_fail_array = [];
        }
        // echo '<pre>';print_r($explodeSuccessContent);exit;


        return response()->json([
            'message' => 'Settings saved as JSON Import successfully',
            'status' => 'success',
            'data' => [
                'success_count' => count($final_success_array),
                'success_array' => $final_success_array,
                'fail_count' => count($final_fail_array),
                'fail_array' => $final_fail_array
            ]
        ]);
        // exit;
    }

    public static function clearAllLogs($shopDomain)
    {
        $successFile = public_path() . '/' . $shopDomain . '/' . 'product_import_success.txt';
        $failedFile = public_path() . '/' . $shopDomain . '/' . 'product_import_fail.txt';
        if (file_exists($successFile)) {
            unlink($successFile);
        }
        if (file_exists($failedFile)) {
            unlink($failedFile);
        }
        return response()->json(['message' => 'Log files deleted successfully', 'status' => 'success', 'data' => []]);
    }

    public static function getProduct($shopDomain)
    {
        $api_key = env('VITE_SHOPIFY_API_KEY');
        $hostname = $shopDomain;
        $apppassword = "";
        // $shop_data = User::where('name', $shopDomain)->firstOrFail();
        // $images = $shop_data->api()->rest('POST','/admin/products/7384018092186/images.json');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        $url = 'https://' . $api_key . ':' . $apppassword . '@' . $hostname . '/admin/products/7384018092186/images.json';
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        $server_output = curl_exec($ch);
        $images = json_decode($server_output, true);
        echo '<pre>';
        print_r($images);
        exit;
    }
}
