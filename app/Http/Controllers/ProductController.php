<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use DB;

class ProductController extends Controller
{
    public function getProducts(Request $request)
    {
		$shop = $request->shop_domain;
        $shop_data = DB::table('users')->where(['name'=>$shop])->first();
	    $url = 'https://'.$shop.'/api/2021-10/graphql.json';
	    // echo '<pre>';print_r($shop_data);exit;
		$qry = '{
            products(first: 10) {
              edges {
                node {
                  handle
                  id
                  productType
                  tags
                  title
                  images(first: 1) {
                    edges {
                      node {
                        id
                        originalSrc
                      }
                    }
                    pageInfo {
                      hasNextPage
                      hasPreviousPage
                    }
                  }
                  priceRange {
                    maxVariantPrice {
                      amount
                      currencyCode
                    }
                    minVariantPrice {
                      currencyCode
                      amount
                    }
                  }
                  collections(first: 10) {
                    edges {
                      node {
                        id
                        title
                        image {
                          originalSrc
                          id
                        }
                      }
                    }
                  }
                }
              }
              pageInfo {
                hasNextPage
                hasPreviousPage
              }
            }
          }';
        $products = $this->curlFunctionGraphQLPOST($url,$qry);
	    echo '<pre>';print_r($products);exit;
	    $product_id = "5375242600602";
	    foreach ($products as $value) {
	    	$subtitleArray = $value[0]['node']['collections']['edges'];
	    	if(!empty($subtitleArray)){
	    		$subtitle = $subtitleArray[0]['node']['title'];
	    	}else {
	    		$subtitle = [];
	    	}
	    	$finalProducts[] = [
	    		'product_id'	=> $value[0]['node']['id'],
	    		'title'			=> $value[0]['node']['title'],
	    		'sub_title'		=> $subtitle,
	    		'price'			=> $value[0]['node']['priceRange']['maxVariantPrice']['amount']." ".$value
	    							[0]['node']['priceRange']['maxVariantPrice']['currencyCode'],
	    		'image'			=> $value[0]['node']['images']['edges'][0]['node']['originalSrc'],
	    		// 'imageNext'		=> $value['edges']['node']['images']['pageInfo']['hasNextPage'],
	    		// 'imagePrevious'	=> $value['edges']['node']['images']['pageInfo']['hasPreviousPage'],
	    		'is_like'		=> '',
	    		'nextPage'		=> $value[0]['pageInfo']['hasNextPage'],
	    		'previousPage'	=> $value[0]['pageInfo']['hasNextPage'],
	    	];
	    }
	    return response()->json([
            'status'  => 'true',
            'message' => 'success',
            'data'    => array(
                'products' => $finalProducts
            )
		]);
    }



    public function curlFunctionGraphQLPOST($url,$data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          'Content-Type: application/graphql',
          'X-Shopify-Storefront-Access-Token:fb4726b5f961e203702e9f66f63af590')
        );
        $server_output = curl_exec($ch);
        $finalData = json_decode($server_output,true);
        return $finalData;
    }

    public function curlFunctionGraphQLGET($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          'Content-Type: application/graphql',
          'X-Shopify-Storefront-Access-Token:fb4726b5f961e203702e9f66f63af590')
        );
        $server_output = curl_exec($ch);
        $finalData = json_decode($server_output,true);
        return $finalData;
    }
}
