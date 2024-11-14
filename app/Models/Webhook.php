<?php



namespace App\Models;



use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;



class Webhook extends Model

{

	public function setShopifyUninstall()

    {

        $topic_header = $_SERVER['HTTP_X_SHOPIFY_TOPIC'];

        $shop = $_SERVER['HTTP_X_SHOPIFY_SHOP_DOMAIN'];

        $file = "shop_log.txt";

        file_put_contents($file, $topic_header .' '.$shop);



        //if( $topic_header == 'app/uninstalled' ) {

        $result = $this->general_model->getAppChargesData($shop);

        if ($result) {

            $data=$result->cid;

            $this->general_model->modifyAppStatus($shop,$data);

        }

                

                



        //$update_status = $this->general_model->modifyAppStatus($shop);

        $file = "update_log2.txt";

        file_put_contents($file, $result);

        //}

			

		

		$jsonobj = file_get_contents("php://input");

		$file = "jsonloog.txt";

        file_put_contents($file, $jsonobj);



		$data=json_decode($jsonobj, true);

		$webhook_post_data_ring = array(

			'{{shopid_rb}}' => $data['id'], 

			'{{shopname_rb}}' => $data['name'],

			'{{shopdomain_rb}}' => $data['domain'],

			'{{updated_at_rb}}' => $data['updated_at'],

			'{{customer_email_rb}}' => $data['customer_email'],

			'{{plan_name_rb}}' => $data['plan_name']



		);

		$admin_template = $this->load->view('emails/admin_mail_uninstall_templete_ring.html','',true);

		$admin_email_body = str_replace(array_keys($webhook_post_data_ring), array_values($webhook_post_data_ring), $admin_template);	

		$admin_subject = ": RingBuilder Dev App Uninstall";

		$admin_toEmail = ['dev@gemfind.com', 'support@gemfind.com', 'billing@gemfind.com'];

		$this->email->from('shopifyapps@shopifyappserver.gemfind.com', 'GemFind RingBuilder');

		$this->email->to($admin_toEmail);

		$this->email->subject($admin_subject);

		$this->email->message($admin_email_body);

		$this->email->set_mailtype('html');

		$this->email->send();



		$arr = array(

		    'filters' => array(

		     array(

		        'propertyName' => 'email',

		        'operator' => 'EQ',

		        'value' => $data['email']

		      )

		    )

		);

		

		$post_json = json_encode($arr);



		$file = "arr_log2.txt";

        file_put_contents($file, $post_json);



        $email_id=$data['email'];

        $endpoint ='https://api.hubapi.com/contacts/v1/contact/email/'.$email_id.'/profile?hapikey=ee625d9a-7fde-44b5-b026-d5f771cfc343';

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



        $file = "uninstall_status_log.txt";

        file_put_contents($file, $status_code);



        $file = "uninstall_response_log.txt";

        file_put_contents($file, $response);



        if ($status_code == 200) {

			$arr1 = array(

	            'properties' => array(

	                array(

	                    'property' => 'email',

	                    'value' => $data['email']

	                ),

	               array(

	                    'property' => 'app_status',

	                    'value' => 'UNINSTALL-RINGBUILDER-DEV'

	                )

	            )

	        );

			$post_json1 = json_encode($arr1);

	        $email_id1=$data['email'];

	        $endpoint1 ='https://api.hubapi.com/contacts/v1/contact/email/'.$email_id1.'/profile?hapikey=ee625d9a-7fde-44b5-b026-d5f771cfc343';

	        

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



	        $file = "uninstall_status_log2.txt";

	        file_put_contents($file, $status_code1);



	        $file = "uninstall_response_log2.txt";

	        file_put_contents($file, $response1);

        }

        else{

        	$arr2 = array(

            'properties' => array(

                array(

                    'property' => 'email',

                    'value' => $data['email']

                ),

                array(

                    'property' => 'shop_name',

                    'value' => $data['name']

                ),

                array(

                    'property' => 'domain_name',

                    'value' => $data['domain']

                ),

                array(

                    'property' => 'phone',

                    'value' => $data['phone']

                ),

                array(

                    'property' => 'state',

                    'value' => $data['province']

                ),

                array(

                    'property' => 'country',

                    'value' => $data['country']

                ),

                array(

                    'property' => 'address',

                    'value' => $data['address1']

                ),

                array(

                    'property' => 'city',

                    'value' => $data['city']

                ),

                array(

                    'property' => 'app_status',

                    'value' => 'INSTALL-RINGBUILDER-DEV'

                )

            )

        );

        $post_json2 = json_encode($arr2);

        $file = "post_data_log3.txt";

        file_put_contents($file, $post_json2);

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

        $file = "uninstall_response_log3.txt";

        file_put_contents($file, $response2);

        $file = "uninstall_status_log3.txt";

        file_put_contents($file, $status_code2);

        }



    }

}

