<?php

namespace App\Jobs;

use Illuminate\Http\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use File;
use App\Models\User;
use Log;

class ImportProductsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $request;
    protected $shop;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($request,$shop)
    {
        $this->request = $request;
        $this->shop = $shop;
    }

    /**
     * Execute the job.
     *
     * @return void
     */

    protected function findImages($arr){
        $extraimage = array();
        $i=0;
        foreach($arr as $key => $value)
        {
           if(preg_match('/^Extra_image_/', $key))
           {
             if(!empty($value)){
                $extraimage[] = $value;
             }
             $i++;
           }
        }
        return $extraimage;
    }

    protected function createMetaFields($shop,$name,$password){
        //CREATE METAFIELDS WITH DEFINITIONS
        $urlMeta = 'https://'.$shop.'/admin/api/2022-04/graphql.json';
        // echo '<pre>';print_r($shop);
        // echo '<pre>';print_r($name);
        $qryMeta = '{
            "query": "mutation CreateMetafieldDefinition($definition: MetafieldDefinitionInput!) { metafieldDefinitionCreate(definition: $definition) { createdDefinition { id name } userErrors { field message code } } }",
             "variables": {
                "definition": {
                  "name": "'.ucfirst($name).'",
                  "namespace": "Product",
                  "key": "'.$name.'",
                  "description": "A list of ingredients used to make the product.",
                  "type": "single_line_text_field",
                  "ownerType": "PRODUCT"
                }
              }
            }';
        $ch = curl_init($urlMeta);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $qryMeta);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'X-Shopify-Access-Token:'.$password)
        );
        $server_output = curl_exec($ch);
        return $server_output;

    }

    protected function findMetaFields($arr){
        $metafields = array();
        $i=0;
        foreach($arr as $key => $value)
        {
           if(preg_match('/^metafields_/', $key))
           {
             if(!empty($value)){
                $metafields[$i]['key'] = $key;
                $metafields[$i]['value'] = $value;
             }
             $i++;
           }
        }
        return $metafields;
    }

    protected function curl($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        $return = curl_exec($ch);
        curl_close ($ch);
        return $return;
    }


    protected function productImages($filedata,$product_id,$shop){
        $findimage = $this->findImages($filedata);
        // echo '<pre>';print_r($findimage);
        $MainImage = array($filedata['MainImage']);
        if(!empty($MainImage) && !empty($findimage)){
            $attachment = array_merge($MainImage,$findimage);
        }elseif(!empty($MainImage) && empty($findimage)){
            $attachment = $MainImage;
        }elseif(empty($MainImage) && !empty($findimage)){
            $attachment = $findimage;
        }
        $base64Image = array();
        // ini_set("allow_url_fopen", 1);
        foreach ($attachment as $key => $image) {
            $image_array = array(
                "image" => array(
                "attachment" => base64_encode($this->curl($image))
                    ),
            );
            $path = public_path();
            $create_product_image = $shop->api()->rest('POST','/admin/products/'.$product_id.'/images.json',$image_array);
            // echo '<pre>';print_r($create_product_image);
            // file_put_contents($path.'/shopImportImage.txt',$create_product_image."-".$product_id,FILE_APPEND);
        }
    }


    protected function variantImages($image,$variant_id,$product_id,$shop){
        if(!empty($image)){
            $attachment = $image;
        }
        $base64Image = array();
        // foreach ($attachment as $key => $vImage) {
            $image_array = array(
                "image" => array(
                "variant_ids" => array($variant_id),
                "attachment" => base64_encode($this->curl($image))
                    ),
            );
        // }
        $create_variant_image = $shop->api()->rest('POST','/admin/products/'.$product_id.'/images.json',$image_array);
    }

    public function handle()
    {
        Log::info("import start");
        //QUEUE START
        $filemainarray = $this->request['data']['file'];
        $shop = User::where('name',$this->shop)->firstOrFail();
        // echo '<pre>';print_r($shop);exit;
        $count=0;
        //echo '<pre>';print_r($filemainarray);exit;
        $path = public_path().'/'.$this->shop;
        File::makeDirectory($path, $mode = 0777, true, true);
        $checkMetafields = $this->findMetaFields($filemainarray[0]);
        foreach ($checkMetafields as $key => $value) {
            $metakey = explode('metafields_', $value['key']);
            $create = $this->createMetaFields($shop->name,$metakey[1],$shop->password);
        }
        sleep(5);
        foreach ($filemainarray as $filedata) {
            //THIS IS FOR CHECKING SIMPLE PRODUCT WITHOUT PARENT AND CHILD SKU
            if(isset($filedata['Product_Type']) && $filedata['Product_Type'] == 'simple'){
               //THIS IS TO CREATE SIMPLE PRODUCT WITHOUT VARIATION
                if($filedata['Parent_Sku'] == ''){
                    //GET ALL THE METAFIELDS OF THE PROJECT
                    $metafields_data = $this->findMetaFields($filedata);
                    $product_metafield = array();
                    $k=0;
                    foreach ($metafields_data as $key => $value) {
                        $metakey = explode('metafields_', $value['key']);
                        $product_metafield[$k]['namespace']="Product";
                        $product_metafield[$k]['key']=$metakey[1];
                        $product_metafield[$k]['value']=$value['value'];
                        $product_metafield[$k]['type']="single_line_text_field";
                        $product_metafield[$k]['name']="product";
                        $k++;
                    }
                    $createDynamicAttribute = array();
                    $selectedAttributes = explode(',', $filedata['Configurable_Attributes']);
                    //    print_r($selectedAttributes);
                    // exit;
                    if(count($selectedAttributes) > 3){
                        return response()->json(['message'=>'You can not add more than 3 Configurable Attributes.','status'=>'fail','data'=>[]]);
                    }
                    $i=0;
                    foreach ($selectedAttributes as $key => $value) {
                        $createDynamicAttribute[$i]['name'] = $value;
                        $i++;
                    }
                    $variantsData = array();
                    if(isset($selectedAttributes) && !empty($selectedAttributes[0])){
                        $option1=$filedata[$selectedAttributes[0]];
                    }else{
                         $option1 = '';
                    }
                    if(isset($selectedAttributes[1]) && !empty($selectedAttributes[1])){
                        $option2=$filedata[$selectedAttributes[1]];
                    }else{
                        $option2 = '';
                    }
                    if(isset($selectedAttributes[2]) && !empty($selectedAttributes[2])){
                        $option3=$filedata[$selectedAttributes[2]];
                    }else{
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
                    $shop = User::where('name',$this->shop)->firstOrFail();
                    $create_product = $shop->api()->rest('POST','/admin/products.json',$products_array);
                    $product_data = json_encode($create_product);
                    $finalProductData = json_decode($product_data);
                    if(isset($finalProductData->body->product) && $finalProductData->body->product != ''){
                        $product_id = $finalProductData->body->product->id;
                        if($product_id){
                            $pathLog = public_path().'/'.$this->shop;
                            $string = $product_id.'|'.$filedata['Name'].'|'.date('Y-m-d h:i:s').'|||';
                            file_put_contents($pathLog.'/product_import_success.txt',$string,FILE_APPEND);
                            //CREATE IMAGE
                            $uploadProductImage = $this->productImages($filedata,$product_id,$shop);
                            //UPDATING VARIANT IMAGES START
                            if(isset($finalProductData->body->product->variants) && $finalProductData->body->product->variants != ''){
                                $variant_data = $finalProductData->body->product->variants;
                                $product_id = $finalProductData->body->product->id;
                                foreach ($variant_data as $variant) {
                                    $uploadProductImage = $this->variantImages($filedata['MainImage'],$variant->id,$product_id,$shop);
                                }
                            }
                        }
                    }else{
                        $pathLog = public_path().'/'.$this->shop;
                        $string_1 = $filedata['Name'].'|'.date('Y-m-d h:i:s').'|'.$product_data.'|||';
                        file_put_contents($pathLog.'/product_import_fail.txt',$string_1,FILE_APPEND);
                    }
                    //END IMPORT IMAGE
                    //CREATE VARIANTS
                    //CREATE METAFIELDS FOR PRODUCT
                    // foreach ($product_metafield as $key => $value) {
                    //     $data = array('metafield'=>$value);
                    //     $createmeta_fields = $shop->api()->rest('POST','/admin/products/'.$product_id.'/metafields.json',$data);
                    // }
                  $count++;
                }
            }elseif(isset($filedata['Product_Type']) && $filedata['Product_Type'] == 'configurable'){

            if($filedata['Parent_Sku'] == '' && $filedata['Child_Sku'] != ''){
                $metafields_data = $this->findMetaFields($filedata);
                $product_metafield = array();
                $k=0;
                foreach ($metafields_data as $key => $value) {
                    $metakey = explode('metafields_', $value['key']);
                    $product_metafield[$k]['namespace']="Product";
                    $product_metafield[$k]['key']=$metakey[1];
                    $product_metafield[$k]['value']=$value['value'];
                    $product_metafield[$k]['type']="single_line_text_field";
                    $product_metafield[$k]['name']="product";
                    $k++;
                }

                //CONVERT TAGS TO ARRAY
                $converted_tags = explode(',', $filedata['Tags']);
                $createDynamicAttribute = array();
                //THIS IS FOR SELECTED ATTRIBUTES CREATE IN SHOPIFY
                $selectedAttributes = explode(',', $filedata['Configurable_Attributes']);
                if(count($selectedAttributes) > 3){
                    return response()->json(['message'=>'You can not add more than 3 Configurable Attributes.','status'=>'fail','data'=>[]]);
                }
                $i=0;
                foreach ($selectedAttributes as $key => $value) {
                    $createDynamicAttribute[$i]['name'] = $value;
                    $i++;
                }
                //CREATE VARIATION DATE
                $selectedChildSku = explode(',', $filedata['Child_Sku']);
                $variantsData = array();
                $variant_image = [];
                $j=0;
                foreach ($selectedChildSku as $key => $value) {
                    $arraykey = array_search($value, array_column($filemainarray, 'Sku'));
                    $variantsData[$j]['sku']=$filemainarray[$arraykey]["Sku"];
                    $variantsData[$j]['price']=$filemainarray[$arraykey]["Price"];
                    $variantsData[$j]['compare_at_price'] = $filemainarray[$arraykey]["Compare_Price"] ? $filemainarray[$arraykey]["Compare_Price"] : "";

                    if(isset($selectedAttributes) && !empty($selectedAttributes[0])){
                        $variantsData[$j]['option1']=$filemainarray[$arraykey][$selectedAttributes[0]];
                    }
                    if(isset($selectedAttributes[1]) && !empty($selectedAttributes[1])){
                        $variantsData[$j]['option2']=$filemainarray[$arraykey][$selectedAttributes[1]];
                    }
                    if(isset($selectedAttributes[2]) && !empty($selectedAttributes[2])){
                        $variantsData[$j]['option3']=$filemainarray[$arraykey][$selectedAttributes[2]];
                    }
                    $variant_image[$filemainarray[$arraykey]["Sku"]] = $filemainarray[$arraykey]["MainImage"];
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
                        "variants"=>$variantsData,
                    )
                );
                // echo '<pre>';
                // print_r($products_array);
                // exit;
                $shop = User::where('name',$this->shop)->firstOrFail();
                $create_product = $shop->api()->rest('POST','/admin/products.json',$products_array);
                $product_data = json_encode($create_product);
                $finalProductData = json_decode($product_data);

                if(isset($finalProductData->body->product) && $finalProductData->body->product != ''){
                    $product_id = $finalProductData->body->product->id;
                    if($product_id){
                        $pathLog = public_path().'/'.$this->shop;
                        $string = $product_id.'|'.$filedata['Name'].'|'.date('Y-m-d h:i:s').'|||';
                        file_put_contents($pathLog.'/product_import_success.txt',$string,FILE_APPEND);

                        //CREATE IMAGE
                        $uploadProductImage = $this->productImages($filedata,$product_id,$shop);
                        //UPDATING VARIANT IMAGES START
                        if(isset($finalProductData->body->product->variants) && $finalProductData->body->product->variants != ''){
                            $variant_data = $finalProductData->body->product->variants;
                            $product_id = $finalProductData->body->product->id;
                            foreach ($variant_data as $variant) {
                                $final_image = $variant_image[$variant->sku];
                                // echo '<pre>';
                                // print_r($variant);
                                $uploadProductImage = $this->variantImages($final_image,$variant->id,$product_id,$shop);
                            }
                        }
                        //UPDATING VARIANT IMAGES END
                    }
                }else{
                    $pathLog = public_path().'/'.$this->shop;
                    $string_1 = $filedata['Name'].'|'.date('Y-m-d h:i:s').'|'.$product_data.'|||';
                    file_put_contents($pathLog.'/product_import_fail.txt',$string_1,FILE_APPEND);
                }
                //END IMPORT IMAGE
                //CREATE METAFIELDS FOR PRODUCT
                // foreach ($product_metafield as $key => $value) {
                //     $data = array('metafield'=>$value);
                //     $createmeta_fields = $shop->api()->rest('POST','/admin/products/'.$product_id.'/metafields.json',$data);
                // }
                //END PRODUCT VARIATION
                //CREATE METAFIELDS FOR VARIATION
                if(isset($finalProductData->body->product) && $finalProductData->body->product != ''){
                    foreach ($finalProductData->body->product->variants as $key => $value) {
                        $arraykey = array_search($value->sku, array_column($filemainarray,'Sku'));
                        $metafieldsdata = $this->findMetaFields($filemainarray[$arraykey]);
                        $product_metafield = array();
                        $k=0;
                        foreach ($metafieldsdata as $key => $valuemeta) {
                            $metakey = explode('metafields_', $valuemeta['key']);
                            $product_metafield[$k]['namespace']="Product";
                            $product_metafield[$k]['key']=$metakey[1];
                            $product_metafield[$k]['value']=$valuemeta['value'];
                            $product_metafield[$k]['type']="single_line_text_field";
                            $product_metafield[$k]['name']="product";
                            $k++;
                        }

                        //VARIENT API CALL
                        foreach ($product_metafield as $key => $valueproduct) {
                            $data = array('metafield'=>$valueproduct);
                            $createmeta_fields = $shop->api()->rest('POST','/admin/products/'.$product_id.'/variants/'.$value->id.'/metafields.json',$data);
                        // file_put_contents('MissingProducttest.txt', '=='.json_encode($createmeta_fields));
                        }
                    }
                }

                //END PRODUCT VARIATION
                $count++;
              }
            }
        }
        // $tafArr = app('App\Http\Controllers\ImportController')->dispatchCsvImportApi($filemainarray);
    }

}
