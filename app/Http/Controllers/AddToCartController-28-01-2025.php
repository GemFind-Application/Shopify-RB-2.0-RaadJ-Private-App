<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use DB;
use Log;
use PDF;

class AddToCartController extends Controller
{
    // public function addToCart(Request $request)
    // {
    //     header('Access-Control-Allow-Origin: *');
    //     // echo '<pre>';print_r($request->all());exit;

    //     $shop_data = User::where('name', $request->shop_domain)->firstOrFail();
    //     $shop_base_url = "https://" . $request->shop_domain;
    //     $diamond_product_id = "";
    //     $setting_product_id = "";
    //     if ($request->diamond_id) {
    //         try {
    //             $diamondData = $this->getDiamondById($request->dealer_id, $request->diamond_id, $request->is_lab);
    //             // print_r($diamondData['diamondData']['fltPrice']);
    //             //    exit;
    //             $url = 'https://' . $request->shop_domain . '/admin/api/2020-07/graphql.json';
    //             $qry = '{
    //                         productVariants(first: 250, query: "' . $request->diamond_id . '") {
    //                             edges {
    //                             cursor
    //                             node {
    //                                 product{
    //                                     id
    //                                 }
    //                                 inventoryItem {
    //                                     id
    //                                 }
    //                                 id
    //                                 sku
    //                                 }
    //                             }
    //                         }
    //                     }';
    //             $ch = curl_init($url);
    //             curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    //             curl_setopt($ch, CURLOPT_POSTFIELDS, $qry);
    //             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //             curl_setopt(
    //                 $ch,
    //                 CURLOPT_HTTPHEADER,
    //                 array(
    //                     'Content-Type: application/graphql',
    //                     'X-Shopify-Access-Token:' . $shop_data->password
    //                 )
    //             );
    //             $server_output = curl_exec($ch);
    //             $sku = json_decode($server_output, true);
    //             if ($sku['data']['productVariants']['edges']) {
    //                 $in_shopify = "1";
    //             } else {
    //                 $in_shopify = "0";
    //             }
    //             if ($in_shopify == "1") {
    //                 $finalSku = $sku['data']['productVariants']['edges'][0]['node'];
    //                 $variantGid = explode('/', $finalSku['id']);
    //                 $variantId = $variantGid[4];
    //                 $productGid = explode('/', $finalSku['product']['id']);
    //                 $productId = $productGid[4];
    //                 $InventoryGid = explode('/', $finalSku['inventoryItem']['id']);
    //                 $InventoryId = $InventoryGid[4];
    //                 $products_array = array(
    //                     "product" => array(
    //                         "id"                => $productId,
    //                         "variants"          => array(array("id" => $variantId, "price" => number_format($diamondData['diamondData']['fltPrice']))),
    //                     )
    //                 );
    //                 $update_product = $shop_data->api()->rest('PUT', '/admin/products/' . $productId . '.json', $products_array);
    //                 $product_data = json_encode($update_product);
    //                 $finalProductData = json_decode($product_data);
    //                 $diamond_product_id = $finalProductData->body->product->variants[0]->id;
    //                 // $locations =  $shop_data->api()->rest('GET', '/admin/locations.json')['body']['container']['locations'];
    //                 // $location_id = $locations[0]['id'];
    //                 // $inventory_array = array(array("location_id"=> $location_id, "inventory_item_id"=> $InventoryId, "available" => 10));
    //                 // $updateInventory = $shop_data->api()->rest('POST','/admin/inventory_levels/set.json',$inventory_array);
    //                 // Log::info($updateInventory);
    //                 // echo '<pre>';print_r($diamond_product_id);exit;
    //             } else {
    //                 $products_array = array(
    //                     "product" => array(
    //                         "title"             => $diamondData['diamondData']['mainHeader'],
    //                         "body_html"         => $diamondData['diamondData']['subHeader'],
    //                         "vendor"            => "GemFind",
    //                         "product_type"      => "GemFindDiamond",
    //                         "published_scope"   => "web",
    //                         "tags"              => "SEARCHANISE_IGNORE,GemfindDiamond",
    //                         "variants"          => array(array("sku" => $request->diamond_id, "price" => number_format($diamondData['diamondData']['fltPrice']))),
    //                         "metafields"        => array(array("namespace" => "seo", "key" => "hidden", "value" => 1, "type" => "integer")),
    //                         "sales_channels"    => ["online"] // Adding sales_channels here
    //                     )
    //                 );


    //                 $create_product = $shop_data->api()->rest('POST', '/admin/products.json', $products_array);
    //                 $product_data = json_encode($create_product);
    //                 $finalProductData = json_decode($product_data);
    //                 $product_id = $finalProductData->body->product->id;
    //                 $diamond_product_id = $finalProductData->body->product->variants[0]->id;
    //                 // $image_array = array("image" => array("attachment" => base64_encode(file_get_contents($diamondData['diamondData']['image2']))));
    //                 // $create_product_image = $shop_data->api()->rest('POST', '/admin/products/' . $product_id . '/images.json', $image_array);


    //                 $image_url = $diamondData['diamondData']['image2'] ? $diamondData['diamondData']['image2'] : '';

    //                 $ch = curl_init($image_url);
    //                 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //                 curl_setopt($ch, CURLOPT_USERAGENT, 'Your User-Agent Here');

    //                 $imageData = curl_exec($ch);

    //                 $image_array = array("image" => array("attachment" => base64_encode($imageData)));

    //                 $create_product_image = $shop_data->api()->rest('POST', '/admin/products/' . $product_id . '/images.json', $image_array);
    //             }
    //         } catch (Exception $e) {
    //             redirect($this->agent->referrer() . '/error');
    //         }
    //     }
    //     if ($request->setting_id) {
    //         try {
    //             $settingDataRIng = $this->getRingById($request->dealer_id, $request->setting_id);
    //             // echo '<pre>';print_r($settingDataRIng['settingData']);exit;
    //             $urlRing = 'https://' . $request->shop_domain . '/admin/api/2020-07/graphql.json';
    //             $qryRing = '{
    //                         productVariants(first: 250, query: "' . $request->setting_id . '") {
    //                             edges {
    //                             cursor
    //                             node {
    //                                 price
    //                                 product{
    //                                     id
    //                                 }
    //                                 inventoryItem {
    //                                     id
    //                                 }
    //                                 id
    //                                 sku
    //                                 }
    //                             }
    //                         }
    //                     }';
    //             $ch = curl_init($urlRing);
    //             curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    //             curl_setopt($ch, CURLOPT_POSTFIELDS, $qryRing);
    //             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //             curl_setopt(
    //                 $ch,
    //                 CURLOPT_HTTPHEADER,
    //                 array(
    //                     'Content-Type: application/graphql',
    //                     'X-Shopify-Access-Token:' . $shop_data->password
    //                 )
    //             );
    //             $server_outputRing = curl_exec($ch);
    //             $skuRIng = json_decode($server_outputRing, true);
    //             // echo '<pre>';print_r($skuRIng);exit;
    //             if (!empty(($skuRIng['data']['productVariants']['edges']))) {
    //                 $in_ring_shopify = "1";
    //             } else {
    //                 $in_ring_shopify = "0";
    //             }
    //             if ($in_ring_shopify == "1") {
    //                 if ($request->sidestonequalityvalue) {
    //                     $roption_name = $request->ringsizesettingonly . " / " . $request->metaltype . " / " . $request->sidestonequalityvalue . " / " . $request->centerstonesizevalue;
    //                 } else {
    //                     $roption_name = $request->ringsizesettingonly . " / " . $request->metaltype . " / " . $request->centerstonesizevalue;
    //                 }
    //                 $finalSkuRing = $skuRIng['data']['productVariants']['edges'][0]['node'];
    //                 // echo '<pre>';print_r($diamond_product_id);exit;
    //                 $variantGidRIng = explode('/', $finalSkuRing['id']);
    //                 $variantIdRIng = $variantGidRIng[4];
    //                 $productGidRIng = explode('/', $finalSkuRing['product']['id']);
    //                 $productIdRIng = $productGidRIng[4];
    //                 // $InventoryGidRIng = explode('/',$finalSkuRing['inventoryItem']['id']);
    //                 // $InventoryIdRIng = $InventoryGid[4];
    //                 $price = $skuRIng['data']['productVariants']['edges'][0]['node']['price'];
    //                 // echo '<pre>';print_r($price);exit;
    //                 $products_array_ring = array(
    //                     "product" => array(
    //                         "id"                => $productIdRIng,
    //                         // "variants"          => array(array("id" => $variantIdRIng, "price" => number_format($price), "option1" => $roption_name)),
    //                     )
    //                 );
    //                 $update_productRIng = $shop_data->api()->rest('PUT', '/admin/products/' . $productIdRIng . '.json', $products_array_ring);
    //                 $product_dataRIng = json_encode($update_productRIng);
    //                 $finalProductDataRIng = json_decode($product_dataRIng);
    //                 $setting_product_id = $finalProductDataRIng->body->product->variants[0]->id;
    //                 // echo '<pre>';print_r($setting_product_id);exit;
    //             } else {
    //                 if ($request->sidestonequalityvalue) {
    //                     $roption_name = $request->ringsizesettingonly . " / " . $request->metaltype . " / " . $request->sidestonequalityvalue . " / " . $request->centerstonesizevalue;
    //                 } else {
    //                     $roption_name = $request->ringsizesettingonly . " / " . $request->metaltype . " / " . $request->centerstonesizevalue;
    //                 }
    //                 $products_array_ring = array(
    //                     "product" => array(
    //                         "title"             => $settingDataRIng['settingData']['settingName'],
    //                         "body_html"         => $settingDataRIng['settingData']['description'],
    //                         "vendor"            => "GemFindRB",
    //                         "product_type"      => "GemFindRing",
    //                         "published_scope"   => "web",
    //                         "tags"              => "SEARCHANISE_IGNORE,GemfindRing",
    //                         "variants"          => array(array("sku" => $request->setting_id, "price" => number_format($settingDataRIng['settingData']['cost']), "option1" => $roption_name)),
    //                         "metafields"        => array(array("namespace" => "seo", "key" => "hidden", "value" => 1, "type" => "integer")),
    //                         "sales_channels"    => ["online"] // Adding sales_channels here
    //                     )
    //                 );
    //                 $create_product_ring = $shop_data->api()->rest('POST', '/admin/products.json', $products_array_ring);
    //                 $product_data_ring = json_encode($create_product_ring);
    //                 $finalProductDataRIng = json_decode($product_data_ring);
    //                 $setting_product_id = $finalProductDataRIng->body->product->variants[0]->id;
    //                 $product_id_ring = $finalProductDataRIng->body->product->id;
    //                 $image_array_ring = array("image" => array("attachment" => base64_encode(file_get_contents($settingDataRIng['settingData']['imageUrl']))));
    //                 $create_product_image_ring = $shop_data->api()->rest('POST', '/admin/products/' . $product_id_ring . '/images.json', $image_array_ring);
    //             }
    //         } catch (Exception $e) {
    //             redirect($this->agent->referrer() . '/error');
    //         }
    //     }

    //     //REDIRECTING URLS
    //     if ($diamond_product_id && $setting_product_id) {
    //         $checkout_url = $shop_base_url . "/cart/add?id[]=" . $diamond_product_id . "&id[]=" . $setting_product_id;
    //         $response = [
    //             'status' => true,
    //             'message' => "diamond & ring",
    //             'data'    => $checkout_url,
    //         ];

    //         // return response()->header('Access-Control-Allow-Origin', '*')->json($response, 200);
    //         echo json_encode($checkout_url);
    //         exit;
    //         // redirect($checkout_url);
    //         // exit;
    //     }
    //     if ($diamond_product_id) {
    //         $checkout_url = $shop_base_url . "/cart/add?id[]=" . $diamond_product_id;
    //         $response = [
    //             'status' => true,
    //             'message' => "diamond",
    //             'data'    => $checkout_url,
    //         ];

    //         // return response()->header('Access-Control-Allow-Origin', '*')->json($response, 200);
    //         echo json_encode($checkout_url);
    //         exit;
    //         // redirect($checkout_url);
    //         // exit;
    //     }
    //     if ($setting_product_id) {
    //         $checkout_url = $shop_base_url . "/cart/add?id[]=&id[]=" . $setting_product_id;
    //         $response = [
    //             'status' => true,
    //             'message' => "diamond",
    //             'data'    => $checkout_url,
    //         ];

    //         // return response()->header('Access-Control-Allow-Origin', '*')->json($response, 200);
    //         echo json_encode($checkout_url);
    //         exit;
    //         // redirect($checkout_url);
    //         // exit;
    //     }
    // }

    public function addToCart(Request $request)
    {
        header('Access-Control-Allow-Origin: *');

        $shop_data = User::where('name', $request->shop_domain)->firstOrFail();

        // Get the HTTP referer
        $referer = $_SERVER['HTTP_REFERER'] ?? '';

        // Parse the URL to extract only the scheme and host
        $parsedUrl = parse_url($referer);
        $shop_base_url = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];

        $diamond_product_id = "";
        $setting_product_id = "";

        if ($request->diamond_id) {
            try {
                $diamondData = $this->getDiamondById($request->dealer_id, $request->diamond_id, $request->is_lab);
                $option_name = "Title: " . $diamondData['diamondData']['mainHeader'] . " | Shape: " . $diamondData['diamondData']['shape'] . " | CaratWeight: " . $diamondData['diamondData']['caratWeight'] . " | Cut: " . $diamondData['diamondData']['cut'] . " | Color: " . $diamondData['diamondData']['color'] . " | Clarity: " . $diamondData['diamondData']['clarity'];

                // Query Shopify for existing product variant
                $graphqlQuery = <<<QUERY
                query GetProductVariants(\$query: String!) {
                    productVariants(first: 250, query: \$query) {
                        edges {
                            node {
                                product {
                                    id
                                }
                                inventoryItem {
                                    id
                                }
                                id
                                sku
                            }
                        }
                    }
                }
                QUERY;

                $variables = [
                    'query' => $request->diamond_id,
                ];

                $response = $this->executeGraphQL($request->shop_domain, $shop_data->password, $graphqlQuery, $variables);

                $sku = $response;
                $in_shopify = !empty($sku['data']['productVariants']['edges']) ? "1" : "0";

                if ($in_shopify == "1") {
                    $finalSku = $sku['data']['productVariants']['edges'][0]['node'];
                    $variantId = $finalSku['id'];
                    $productId = $finalSku['product']['id'];

                    $mutation = <<<QUERY
                        mutation UpdateProductVariantWithOptions(\$productId: ID!, \$variants: [ProductVariantsBulkInput!]!) {
                            productVariantsBulkUpdate(productId: \$productId, variants: \$variants) {
                                productVariants {
                                    id
                                    price
                                }
                                userErrors {
                                    field
                                    message
                                }
                            }
                        }
                        QUERY;

                    $variables = [
                        'productId' => $productId,
                        'variants' => [
                            [
                                'id' => $variantId,
                                'price' => (float)$diamondData['diamondData']['fltPrice'],
                            ]
                        ]
                    ];

                    $updateResponse = $this->executeGraphQL($request->shop_domain, $shop_data->password, $mutation, $variables);

                    if (isset($updateResponse['data']['productVariantsBulkUpdate']['productVariants'][0]['id'])) {
                        $diamond_product_id = preg_replace('/gid:\/\/shopify\/ProductVariant\//', '', $updateResponse['data']['productVariantsBulkUpdate']['productVariants'][0]['id']);
                    }
                } else {

                    $image_url = $diamondData['diamondData']['image2'] ?? '';

                    $mutation = <<<QUERY
                        mutation CreateProductWithVariants(\$input: ProductSetInput!) {
                            productSet(synchronous: true, input: \$input) {
                                product {
                                    id
                                    title
                                    media(first: 5) {
                                        nodes {
                                            id
                                            alt
                                            mediaContentType
                                            status
                                        }
                                    }
                                    variants(first: 10) {
                                        edges {
                                            node {
                                                id
                                                sku
                                                price
                                                media(first: 5) {
                                                    nodes {
                                                        id                                                       
                                                        alt
                                                        mediaContentType
                                                        status
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                userErrors {
                                    field
                                    message
                                }
                            }
                        }
                        QUERY;

                    $variables = [
                        'input' => [
                            'title' => $diamondData['diamondData']['mainHeader'],
                            'descriptionHtml' => $diamondData['diamondData']['subHeader'],
                            'vendor' => 'GemFind',
                            'productType' => 'GemFindDiamond',
                            'tags' => ['SEARCHANISE_IGNORE', 'GemfindDiamond'],
                            'productOptions' => [
                                [
                                    'name' => 'Properties',
                                    'position' => 1,
                                    'values' => [
                                        ['name' => $option_name],
                                    ],
                                ],
                            ],
                            'files' => [
                                [
                                    'originalSource' => $image_url, // URL to the product image
                                    'alt' => 'Product Image', // Optional alt text
                                    'contentType' => 'IMAGE', // Ensure this matches the file type
                                ],
                            ],
                            'variants' => [
                                [
                                    'optionValues' => [
                                        ['optionName' => 'Properties', 'name' => $option_name],
                                    ],
                                    'sku' => $request->diamond_id,
                                    'price' => (float)$diamondData['diamondData']['fltPrice'],
                                    'file' => [
                                        'originalSource' => $image_url, // URL to the variant image
                                        'alt' => 'Variant Image',
                                        'contentType' => 'IMAGE', // Match the file type
                                    ],
                                ],
                            ],
                        ],
                    ];

                    $createResponse = $this->executeGraphQL($request->shop_domain, $shop_data->password, $mutation, $variables);

                    if (!empty($createResponse['data']['productSet']['userErrors'])) {
                        foreach ($createResponse['data']['productSet']['userErrors'] as $error) {
                            echo "Error: " . $error['message'] . " (Field: " . implode(', ', $error['field']) . ")";
                        }
                        exit;
                    }

                    if (isset($createResponse['data']['productSet']['product']['id'])) {
                        $productId = $createResponse['data']['productSet']['product']['id'];

                        $diamond_product_id = preg_replace('/gid:\/\/shopify\/ProductVariant\//', '', $createResponse['data']['productSet']['product']['variants']['edges'][0]['node']['id']);

                        $fetchPublicationsQuery = <<<QUERY
                        query GetPublications {
                            publications(first: 10) {
                              nodes {
                                id
                                name
                              }
                            }
                        }
                        QUERY;

                        // Execute the query to fetch publications
                        $publicationsResponse = $this->executeGraphQL($request->shop_domain, $shop_data->password, $fetchPublicationsQuery);

                        if (isset($publicationsResponse['data']['publications']['nodes'])) {
                            $publications = $publicationsResponse['data']['publications']['nodes'];

                            // Find the publication ID for the "online_store" channel
                            $publicationId = null;
                            foreach ($publications as $publication) {

                                if ($publication['name'] == 'Online Store') {
                                    $publicationId = $publication['id'];
                                    break;
                                }
                            }

                            if ($publicationId) {

                                $publishMutation = <<<QUERY
                                    mutation CreateProductWithOnlineStore {
                                        publishablePublish(
                                            input: {publicationId: "$publicationId"}
                                            id: "$productId"
                                        ) {
                                            userErrors {
                                                message
                                                field
                                            }
                                        }
                                    }
                                    QUERY;

                                $publishResponse = $this->executeGraphQL($request->shop_domain, $shop_data->password, $publishMutation);
                            } else {
                                return response()->json(['status' => false, 'message' => 'Online Store publication not found.']);
                            }
                        } else {
                            return response()->json(['status' => false, 'message' => 'Error fetching publications.', 'errors' => $publicationsResponse]);
                        }
                    } else {
                        $userErrors = $createResponse['data']['productCreate']['userErrors'] ?? [];
                        return response()->json(['status' => false, 'message' => 'Error creating product', 'errors' => $userErrors]);
                    }
                }
            } catch (Exception $e) {
                return response()->json(['status' => false, 'message' => $e->getMessage()]);
            }
        }


        if ($request->setting_id) {
            try {
                $settingData = $this->getRingById($request->dealer_id, $request->setting_id);

                if ($request->sidestonequalityvalue) {
                    $roption_name = "Ring Size: " . $request->ringsizesettingonly . " | Metal Type: " . $request->metaltype . " | Side Stone: " . $request->sidestonequalityvalue . " | Center Stone: " . $request->centerstonesizevalue;
                } else {
                    $roption_name = "Ring Size: " . $request->ringsizesettingonly . " | Metal Type: " . $request->metaltype . " | Center Stone: " . $request->centerstonesizevalue;
                }

                // Query Shopify for existing product variant
                $graphqlQuery = <<<QUERY
                query GetProductVariants(\$query: String!) {
                    productVariants(first: 250, query: \$query) {
                        edges {
                            node {
                                product {
                                    id
                                }
                                inventoryItem {
                                    id
                                }
                                id
                                sku
                            }
                        }
                    }
                }
                QUERY;

                $variables = [
                    'query' => $request->setting_id,
                ];

                $response = $this->executeGraphQL($request->shop_domain, $shop_data->password, $graphqlQuery, $variables);

                $sku = $response;
                $in_shopify = !empty($sku['data']['productVariants']['edges']) ? "1" : "0";

                if ($in_shopify == "1") {
                    $finalSku = $sku['data']['productVariants']['edges'][0]['node'];
                    $variantId = $finalSku['id'];
                    $productId = $finalSku['product']['id'];


                    $mutation = <<<QUERY
                        mutation UpdateProductVariantWithOptions(\$productId: ID!, \$variants: [ProductVariantsBulkInput!]!) {
                            productVariantsBulkUpdate(productId: \$productId, variants: \$variants) {
                                productVariants {
                                    id
                                    price
                                }
                                userErrors {
                                    field
                                    message
                                }
                            }
                        }
                        QUERY;



                    $variables = [
                        'productId' => $productId,
                        'variants' => [
                            [
                                'id' => $variantId,
                                'price' => (float)$settingData['settingData']['cost'],
                            ]
                        ]
                    ];

                    $updateResponse = $this->executeGraphQL($request->shop_domain, $shop_data->password, $mutation, $variables);

                    if (isset($updateResponse['data']['productVariantsBulkUpdate']['productVariants'][0]['id'])) {
                        $setting_product_id = preg_replace('/gid:\/\/shopify\/ProductVariant\//', '', $updateResponse['data']['productVariantsBulkUpdate']['productVariants'][0]['id']);
                    }
                } else {

                    $image_url = $settingData['settingData']['imageUrl'] ?? '';

                    $mutation = <<<QUERY
                        mutation CreateProductWithVariants(\$input: ProductSetInput!) {
                            productSet(synchronous: true, input: \$input) {
                                product {
                                    id
                                    title
                                    media(first: 5) {
                                        nodes {
                                            id
                                            alt
                                            mediaContentType
                                            status
                                        }
                                    }
                                    variants(first: 10) {
                                        edges {
                                            node {
                                                id
                                                sku
                                                price
                                                media(first: 5) {
                                                    nodes {
                                                        id                                                       
                                                        alt
                                                        mediaContentType
                                                        status
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                userErrors {
                                    field
                                    message
                                }
                            }
                        }
                        QUERY;

                    $variables = [
                        'input' => [
                            'title' => $settingData['settingData']['settingName'],
                            'descriptionHtml' => $settingData['settingData']['description'],
                            'vendor' => 'GemFind',
                            'productType' => 'GemFindDiamond',
                            'tags' => ['SEARCHANISE_IGNORE', 'GemfindDiamond'],
                            'productOptions' => [
                                [
                                    'name' => 'Properties',
                                    'position' => 1,
                                    'values' => [
                                        ['name' => $roption_name],
                                    ],
                                ],
                            ],
                            'files' => [
                                [
                                    'originalSource' => $image_url, // URL to the product image
                                    'alt' => 'Product Image', // Optional alt text
                                    'contentType' => 'IMAGE', // Ensure this matches the file type
                                ],
                            ],
                            'variants' => [
                                [
                                    'optionValues' => [
                                        ['optionName' => 'Properties', 'name' => $roption_name],
                                    ],
                                    'sku' => $request->setting_id,
                                    'price' => (float)$settingData['settingData']['cost'],
                                    'file' => [
                                        'originalSource' => $image_url, // URL to the variant image
                                        'alt' => 'Variant Image',
                                        'contentType' => 'IMAGE', // Match the file type
                                    ],
                                ],
                            ],
                        ],
                    ];

                    $createResponse = $this->executeGraphQL($request->shop_domain, $shop_data->password, $mutation, $variables);

                    if (!empty($createResponse['data']['productSet']['userErrors'])) {
                        foreach ($createResponse['data']['productSet']['userErrors'] as $error) {
                            echo "Error: " . $error['message'] . " (Field: " . implode(', ', $error['field']) . ")";
                        }
                        exit;
                    }

                    if (isset($createResponse['data']['productSet']['product']['id'])) {
                        $productId = $createResponse['data']['productSet']['product']['id'];

                        $setting_product_id = preg_replace('/gid:\/\/shopify\/ProductVariant\//', '', $createResponse['data']['productSet']['product']['variants']['edges'][0]['node']['id']);

                        $fetchPublicationsQuery = <<<QUERY
                        query GetPublications {
                            publications(first: 10) {
                              nodes {
                                id
                                name
                              }
                            }
                        }
                        QUERY;

                        // Execute the query to fetch publications
                        $publicationsResponse = $this->executeGraphQL($request->shop_domain, $shop_data->password, $fetchPublicationsQuery);

                        if (isset($publicationsResponse['data']['publications']['nodes'])) {
                            $publications = $publicationsResponse['data']['publications']['nodes'];

                            // Find the publication ID for the "online_store" channel
                            $publicationId = null;
                            foreach ($publications as $publication) {

                                if ($publication['name'] == 'Online Store') {
                                    $publicationId = $publication['id'];
                                    break;
                                }
                            }

                            if ($publicationId) {

                                $publishMutation = <<<QUERY
                                    mutation CreateProductWithOnlineStore {
                                        publishablePublish(
                                            input: {publicationId: "$publicationId"}
                                            id: "$productId"
                                        ) {
                                            userErrors {
                                                message
                                                field
                                            }
                                        }
                                    }
                                    QUERY;

                                $publishResponse = $this->executeGraphQL($request->shop_domain, $shop_data->password, $publishMutation);
                            } else {
                                return response()->json(['status' => false, 'message' => 'Online Store publication not found.']);
                            }
                        } else {
                            return response()->json(['status' => false, 'message' => 'Error fetching publications.', 'errors' => $publicationsResponse]);
                        }
                    } else {
                        $userErrors = $createResponse['data']['productCreate']['userErrors'] ?? [];
                        return response()->json(['status' => false, 'message' => 'Error creating product', 'errors' => $userErrors]);
                    }
                }
            } catch (Exception $e) {
                return response()->json(['status' => false, 'message' => $e->getMessage()]);
            }
        }

        // Redirect or return the checkout URL
        // if ($diamond_product_id) {
        //     // $checkout_url = $shop_base_url . "/cart/add?id[]=" . $diamond_product_id;

        //     $checkout_url = $shop_base_url
        //         . "/cart/add?id[]="
        //         . $diamond_product_id
        //         . (isset($diamondData['diamondData']['stockNumber']) ? "&properties[_stokeNumber]=" . $diamondData['diamondData']['stockNumber'] : "")
        //         . (isset($diamondData['diamondData']['measurement']) ? "&properties[_measurement]=" . $diamondData['diamondData']['measurement'] : "")
        //         . (isset($diamondData['diamondData']['vendorStockNo']) ? "&properties[_vendorStockNo]=" . $diamondData['diamondData']['vendorStockNo'] : "");


        //     $response = [
        //         'status' => true,
        //         'message' => "diamond",
        //         'data'    => $checkout_url,
        //     ];
        //     echo json_encode($checkout_url);
        //     exit;
        // }


        if ($diamond_product_id && $setting_product_id) {
            $checkout_url = $shop_base_url . "/cart/add?id[]=" . $diamond_product_id . "&id[]=" . $setting_product_id;
            $response = [
                'status' => true,
                'message' => "diamond & ring",
                'data'    => $checkout_url,
            ];

            echo json_encode($checkout_url);
            exit;
        }

        if ($diamond_product_id) {
            $checkout_url = $shop_base_url . "/cart/add?id[]=" . $diamond_product_id;
            $response = [
                'status' => true,
                'message' => "diamond",
                'data'    => $checkout_url,
            ];
            echo json_encode($checkout_url);
            exit;
        }

        if ($setting_product_id) {
            $checkout_url = $shop_base_url . "/cart/add?id[]=&id[]=" . $setting_product_id;
            $response = [
                'status' => true,
                'message' => "diamond",
                'data'    => $checkout_url,
            ];
            echo json_encode($checkout_url);
            exit;
        }

        return response()->json(['status' => false, 'message' => "Diamond not available."]);
    }

    private function executeGraphQL($shop_domain, $access_token, $query, $variables = [])
    {
        $url = "https://{$shop_domain}/admin/api/2025-01/graphql.json";

        $headers = [
            "Content-Type: application/json",
            "X-Shopify-Access-Token: {$access_token}",
        ];

        // Prepare payload
        $payload = json_encode([
            'query' => $query,
            'variables' => (object)$variables,
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new Exception('cURL error: ' . curl_error($ch));
        }

        curl_close($ch);

        return json_decode($response, true);
    }

    public static function getDiamondById($dealerId, $diamondId, $isalab)
    {
        if ($isalab == "true") {
            $requestUrl = "http://api.jewelcloud.com/api/RingBuilder/GetDiamondDetail?DealerID=" . $dealerId . "&DID=" . $diamondId . '&IsLabGrown=true';
        } else {
            $requestUrl = "http://api.jewelcloud.com/api/RingBuilder/GetDiamondDetail?DealerID=" . $dealerId . "&DID=" . $diamondId;
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $requestUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        $response = curl_exec($curl);
        $results = json_decode($response);
        if (curl_errno($curl)) {
            return $returnData = ['diamondData' => [], 'total' => 0, 'message' => 'Gemfind: An error has occurred.'];
        }
        if (isset($results->message)) {
            return $returnData = ['diamondData' => [], 'total' => 0, 'message' => 'Gemfind: An error has occurred.'];
        }
        curl_close($curl);
        if ($results->diamondId != "" && $results->diamondId > 0) {
            $diamondData = (array) $results;
            $returnData = ['diamondData' => $diamondData];
        } else {
            $returnData = ['diamondData' => []];
        }
        return $returnData;
    }

    public static function getRingById($dealerId, $settingId)
    {
        $requestUrl = "http://api.jewelcloud.com/api/RingBuilder/GetMountingDetail?DealerID=" . $dealerId . "&SID=" . $settingId;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $requestUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        $response = curl_exec($curl);
        $results = json_decode($response);
        if (curl_errno($curl)) {
            return $returnData = ['settingData' => [], 'total' => 0, 'message' => 'Gemfind: An error has occurred.'];
        }
        if (isset($results->message)) {
            return $returnData = ['settingData' => [], 'total' => 0, 'message' => 'Gemfind: An error has occurred.'];
        }
        curl_close($curl);
        if ($results->settingId != "") {
            $settingData = (array) $results;
            $returnData = ['settingData' => $settingData];
        } else {
            $returnData = ['settingData' => []];
        }
        return $returnData;
    }

    function printDiamond($shop_domain, $diamond_id, $type)
    {
        // header('Access-Control-Allow-Origin: *');
        // header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        $getDiamondData = self::getDiamondByIdForPdf($shop_domain, $diamond_id, $type);
        view()->share('diamond', $getDiamondData);
        $pdf = PDF::loadView('printDiamond', $getDiamondData);
        $headers = array(
            'Content-Type: application/pdf',
        );
        return $pdf->download('Diamond-' . $diamond_id . '.pdf', $headers);
    }


    public static function getDiamondByIdForPdf($shop, $diamondId, $type)
    {
        $IslabGrown = '';
        if ($type && $type == 'labcreated') {
            $diamond_type = '&IslabGrown=true';
        } elseif ($type == 'fancydiamonds') {
            $diamond_type = '&IsFancy=true';
        } else {
            $diamond_type = '';
        }
        $shop_data = DB::table('ringbuilder_config')->where('shop', $shop)->first();
        $requestUrl = "http://api.jewelcloud.com/api/RingBuilder/GetDiamondDetail?DealerID=" . $shop_data->dealerid . "&DID=" . $diamondId;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $requestUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        $response = curl_exec($curl);
        $results = json_decode($response);
        if (curl_errno($curl)) {
            return $returnData = ['diamondData' => [], 'total' => 0, 'message' => 'Gemfind: An error has occurred.'];
        }
        if (isset($results->message)) {
            return $returnData = ['diamondData' => [], 'total' => 0, 'message' => 'Gemfind: An error has occurred.'];
        }
        curl_close($curl);
        if ($results->diamondId != "" && $results->diamondId > 0) {
            $diamondData = (array) $results;
            $returnData = ['diamondData' => $diamondData];
        } else {
            $returnData = ['diamondData' => []];
        }
        return $returnData;
    }

    function getProductDetails($shop_domain, $productId, $variantId)
    {
        $settingData = DB::table('ringbuilder_config')->where('shop', $shop_domain)->first();
        // echo "<pre>";
        //     print_r($settingData);
        //     exit;
        $shop = User::where('name', $shop_domain)->firstOrFail();
        $product = $shop->api()->rest('GET', '/admin/products/' . $productId . '.json');
        $variant = $shop->api()->rest('GET', '/admin/variants/' . $variantId . '.json');
        //echo "<pre>";print_r($variant);exit;
        $meta_fields = $shop->api()->rest('GET', '/admin/products/' . $productId . '/metafields.json');
        if ($product) {
            $getProduct = $product['body']['container']['product'];
            // echo "<pre>";
            // print_r($variant['body']['container']['variant']['price']);
            // exit;
            $getVariant = $variant['body']['container']['variant'];
            $getMetaFields = $meta_fields['body']['container']['metafields'];
            // echo "<pre>";print_r($getMetaFields);
            // exit;

            $groupMetaFields = array();
            foreach ($getMetaFields as $element) {
                $groupMetaFields[$element['key']] = $element;
            }
            if ($groupMetaFields) {
                foreach ($groupMetaFields as $meta) {
                    if (array_key_exists("ringSize", $groupMetaFields)) {
                        $ringSize = $groupMetaFields['ringSize']['value'];
                    } else {
                        $ringSize = "";
                    }
                    if (array_key_exists("shape", $groupMetaFields)) {
                        $shape = $groupMetaFields['shape']['value'];
                    } else {
                        $shape = "";
                    }
                    if (array_key_exists("MinimumCarat", $groupMetaFields)) {
                        $centerStoneMinCarat = $groupMetaFields['MinimumCarat']['value'];
                    } else {
                        $centerStoneMinCarat = "";
                    }
                    if (array_key_exists("MaximumCarat", $groupMetaFields)) {
                        $centerStoneMaxCarat = $groupMetaFields['MaximumCarat']['value'];
                    } else {
                        $centerStoneMaxCarat = "";
                    }
                    if (array_key_exists("islabsettings", $groupMetaFields)) {
                        $islabsettings = $groupMetaFields['islabsettings']['value'];
                    } else {
                        $islabsettings = "";
                    }
                }
            } else {
                $ringSize = "NA";
                $shape = "NA";
                $centerStoneMinCarat = "NA";
                $centerStoneMaxCarat = "NA";
                $islabsettings = "NA";
            }
            //echo '<pre>';print_r($centerStoneMinCarat);exit;
            $imageFinal = [];
            foreach ($getProduct['images'] as $image) {
                $imageFinal[] = $image['src'];
            }

            foreach ($getProduct as $prod) {

                $finalProduct = [
                    '$id'                   => $getProduct['id'] ? $getProduct['id'] : 'NA',
                    'styleNumber'           => $getProduct['variants'][0]['sku'] ? $getProduct['variants'][0]['sku'] : 'NA',
                    "settingName"           => $getProduct['title'] ? $getProduct['title'] : 'NA',
                    "description"           => $getProduct['body_html'] ? $getProduct['body_html'] : 'NA',
                    "metalType"             => $variant['body']['container']['variant']['option1'] ? $variant['body']['container']['variant']['option1'] : 'NA',
                    "centerStoneFit"        => $shape,
                    "centerStoneMinCarat"   => $centerStoneMinCarat,
                    "centerStoneMaxCarat"   => $centerStoneMaxCarat,
                    "category"              => 'NA',
                    "settingId"             => $getVariant['sku'] ? $getVariant['sku'] : 'NA',
                    "vendorId"              => $settingData->dealerid,
                    "vendorCompany"         => "Retailer Demo",
                    "vendorName"            => $getProduct['vendor'] ? $getProduct['vendor'] : 'NA',
                    "vendorEmail"           => "gflink@gemfind.net,michael.mastros@gemfind.com",
                    "vendorPhone"           => "888-999-7755",
                    "imageUrl"              => $getProduct['image']['src'] ? $getProduct['image']['src'] : 'NA',
                    "cost"                  => $variant['body']['container']['variant']['price'] ? $variant['body']['container']['variant']['price'] : 'NA',
                    "originalCost"          => $getProduct['variants'][0]['price'] ? $getProduct['variants'][0]['price'] : 'NA',
                    "mainImageURL"          => $getProduct['image']['src'] ? $getProduct['image']['src'] : 'NA',
                    "roundImageURL"         => "",
                    "asscherImageURL"       => "",
                    "emeraldImageURL"       => "",
                    "radiantImageURL"       => "",
                    "cushionImageURL"       => "",
                    "marquiseImageURL"      => "",
                    "ovalImageURL"          => "",
                    "heartImageURL"         => "",
                    "pearImageURL"          => "",
                    "princessImageURL"      => "",
                    "dealerId"              => null,
                    "thumbNailImage"        => null,
                    "extraImage"            => $imageFinal,
                    "relatedProductImage"   => "",
                    "configurableProduct"   => "",
                    "prongMetal"            => "",
                    "settingType"           => $getProduct['product_type'] ? $getProduct['product_type'] : 'NA',
                    "width"                 => "",
                    "videoURL"              => "",
                    "designerLogo"          => "",
                    "designerName"          => "Overnight Mountings Ring Builder",
                    "isFavorite"            => false,
                    "ringSize"              => $ringSize,
                    "sideStoneQuality"      => [],
                    "currencyFrom"          => "USD",
                    "currencySymbol"        => "US$",
                    "sideDiamondDetail1"    => [],
                    "sideDiamondDetail"     => "",
                    "retailerInfo"          => "",
                    "addressList"           => [],
                    "timingList"            => [],
                    "metalID"               => $getVariant['option1'] ? $getVariant['option1'] : 'NA',
                    "colorID"               => $getVariant['option2'] ? $getVariant['option2'] : 'NA',
                    "internalUselink"       => "No",
                    "ringSizeType"          => "",
                    "showPrice"             => true,
                    "rbEcommerce"           => false,
                    "showBuySettingOnly"    => false,
                    "tryon"                 => true,
                    "isLabSetting"          => $islabsettings
                ];
            }
        }
        // echo "<pre>";
        // print_r($finalProduct);
        // exit;
        return $finalProduct;
    }
}
