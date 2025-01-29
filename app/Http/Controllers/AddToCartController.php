<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use DB;
use Log;
use PDF;

class AddToCartController extends Controller
{

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



                    $variantNumericId = $this->extractShopifyId($variantId);
                    $productNumericId = $this->extractShopifyId($productId);

                    $getproductCost = $this->getProductDetails($request->shop_domain, $productNumericId, $variantNumericId);

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
                                'price' => (float)$getproductCost['cost'],
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

    function extractShopifyId($gid)
    {
        return preg_replace('/\D/', '', $gid);
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
        // header('Access-Control-Allow-Origin: *');

        $settingData = DB::table('ringbuilder_config')->where('shop', $shop_domain)->first();
        $shop = User::where('name', $shop_domain)->firstOrFail();

        $GetProductsById = <<<QUERY
            query GetProduct(\$id: ID!) {
                product(id: \$id) {
                    id
                    title
                    description
                    productType
                    vendor               
                    featuredMedia {
                        id
                        preview {
                            image {
                                url
                                id
                            }
                        }
                    }
                    media(first: 100) {
                        nodes {
                            id
                            preview {
                                image {
                                    url
                                    id
                                }
                            } 
                        }               
                    }
                    variants(first: 100) {
                        nodes {
                            id
                            title
                            sku
                            price
                            product {
                                id
                                options {
                                    name
                                    values
                                }
                            }
                        }
                    }  
                    metafields(first: 100) {
                        edges {
                            node {
                                namespace
                                key
                                value
                            }
                        }
                    }  
                }  
            }  
            QUERY;


        $variables = [
            'id' => "gid://shopify/Product/{$productId}",
            'namespace' => 'Product',
            'key' => 'shape',
        ];

        $product = $this->executeGraphQL($shop_domain, $shop->password, $GetProductsById, $variables);

        $graphqlQuery = <<<QUERY
        query GetProductVariants(\$query: String!) {
            productVariants(first: 250, query: \$query) {
                edges {
                    node {
                        product {
                            id
                            options {
                                id
                                name
                                values
                            }                           
                        }
                        inventoryItem {
                            id
                        }
                        id
                        sku
                        price
                        compareAtPrice
                        image {
                            url
                        }
                    }
                }
            }
        }
        QUERY;

        $variables = [
            'query' => $variantId,
        ];

        $variant = $this->executeGraphQL($shop_domain, $shop->password, $graphqlQuery, $variables);



        if ($product) {
            $getProduct = $product['data']['product'];
            $getVariant = $variant['data']['productVariants']['edges'][0]['node'];


            $getMetaFields = $getProduct['metafields']['edges'];

            $groupMetaFields = array();
            foreach ($getMetaFields as $element) {
                $groupMetaFields[$element['node']['key']] = $element['node'];
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

            $imageFinal = [];
            foreach ($getProduct['media']['nodes'] as $image) {
                $imageFinal[] = $image['preview']['image']['url'];
            }
        }

        $finalProduct = [
            '$id'                   => $productId ? $productId : 'NA',
            'styleNumber'           => $getProduct['variants']['nodes'][0]['sku'] ? $getProduct['variants']['nodes'][0]['sku'] : 'NA',
            "settingName"           => $getProduct['title'] ? $getProduct['title'] : 'NA',
            "description"           => $getProduct['description'] ? $getProduct['description'] : 'NA',
            "metalType"             => $getProduct['variants']['nodes'][0]['product']['options'][0]['values'][0] ? $getProduct['variants']['nodes'][0]['product']['options'][0]['values'][0] : 'NA',
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
            "imageUrl"              => $getVariant['image']['url'] ? $getVariant['image']['url'] : $getProduct['featuredMedia']['preview']['image']['url'],
            "cost"                  => $getVariant['price'] ?  $getVariant['price'] : 'NA',
            "originalCost"          => $getVariant['compareAtPrice'] ? $getVariant['compareAtPrice'] : 'NA',
            "mainImageURL"          => $getVariant['image']['url'] ? $getVariant['image']['url'] : $getProduct['featuredMedia']['preview']['image']['url'],
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
            "settingType"           => $getProduct['productType'] ? $getProduct['productType'] : 'NA',
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
            "metalID"               => $getVariant['product']['options'][0]['values'][0]  ? $getVariant['product']['options'][0]['values'][0] : 'NA',
            "colorID"               => $getVariant['product']['options'][1]['values'][0] ? $getVariant['product']['options'][1]['values'][0] : 'NA',
            "internalUselink"       => "No",
            "ringSizeType"          => "",
            "showPrice"             => true,
            "rbEcommerce"           => true,
            "showBuySettingOnly"    => false,
            "tryon"                 => true,
            "isLabSetting"          => $islabsettings
        ];

        // echo '<pre>';
        // print_r($finalProduct);
        // exit;


        return $finalProduct;
    }
}
