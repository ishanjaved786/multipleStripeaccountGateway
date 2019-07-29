<?php 


/**
 * 
 */
class isspaymentfunctions
{
	
	public $currency;

	function __construct()
	{
		// Woocommerce store currency
		$this->currency = get_woocommerce_currency();

	}

	public function create_token($args,$key,$amount,$product_id){

		$amount = $amount * 100;

		$token = \Stripe\Token::create([
			'card' => [
				'number' => $args['iss_No'],
				'exp_month' => $args['iss_month'],
				'exp_year' => $args['iss_year'],
				'cvc' => $args['iss_cvv']
			]
		],$key['p']);

		if(!empty($token['id'])){

			$charge = \Stripe\Charge::create([
				"amount" => $amount,
				"currency" => $this->currency,
	  		"source" => $token, // obtained with Stripe.js
	  		"description" => "Charge for ".$_POST['billing_email']." product_id ".$product_id
	  	],$key['l']);

			return $charge;

		}else{
			echo $token;
		}

		//echo $token['id'];
		//return $charge;

	}


	public function shipping_charge($args,$key,$amount){

		$amount = $amount * 100;

		$token = \Stripe\Token::create([
			'card' => [
				'number' => $args['iss_No'],
				'exp_month' => $args['iss_month'],
				'exp_year' => $args['iss_year'],
				'cvc' => $args['iss_cvv']
			]
		],$key['p']);

		if(!empty($token['id'])){

			$charge = \Stripe\Charge::create([
				"amount" => $amount,
				"currency" => $this->currency,
	  		"source" => $token, // obtained with Stripe.js
	  		"description" => "Charge for ".$_POST['billing_email']."  Shipping Charge"
	  	],$key['l']);

			return $charge;

		}else{
			echo $token;
		}

		//echo $token['id'];
		//return $charge;

	}


	public function subscription_payment($args,$key,$amount,$product_id,$order_id,$sub_id){

		$amount = $amount * 100;

		$token = \Stripe\Token::create([
			'card' => [
				'number' => $args['iss_No'],
				'exp_month' => $args['iss_month'],
				'exp_year' => $args['iss_year'],
				'cvc' => $args['iss_cvv']
			]
		],$key['p']);


		if(!empty($token['id'])){

			$customer = \Stripe\Customer::create([
				'source' => $token,
				'email' => $_POST['billing_email'],
			],$key['l']);

			if ($customer->id || !empty($customer->id)) {
				

				add_post_meta($sub_id, 'cid', $customer->id);

/*				$data_store = WC_Data_Store::load( 'order-item' );
				$meta_id    = $data_store->add_metadata( $item_id, 'cid', $customer->id, true );

				if ( $meta_id ) {
					
					WC_Cache_Helper::incr_cache_prefix( 'object_' . $item_id ); // Invalidate cache.
					//return $meta_id;
				}*/
				if(round($amount) == 0){
					$charge = array('status' => 'succeeded');
					return $charge;
				}else{

					$charge = \Stripe\Charge::create([
						"amount" => $amount,
						"currency" => $this->currency,
	  				"customer" => $customer->id, // obtained with Stripe.js
	  				"description" => "Charge for ".$product_id." from ".$_POST['billing_email']."  Total Subscription Amount Order Id ".$order_id
	  			],$key['l']);

	  			//add_post_meta($order_id, '_shipping', $charge['id']);

					return $charge;
				}

			}else{
				echo $customer;
			}

		}else{
			echo $token;
		}


	}

	public function subscription_payment_renew($key,$amount,$product_id,$cid){

		$amount = $amount * 100;

		$charge = \Stripe\Charge::create([
			"amount" => $amount,
			"currency" => $this->currency,
	  				"customer" => $cid, // obtained with Stripe.js
	  				"description" => "Subscription renewal Charge for ".$product_id
	  			],$key['l']);

		return $charge;
	}

	public function return_amount($key, $transaction_id, $order_id, $amount, $single, $price){

		$error = null;

		try {	
			if($single && $amount != null){
				
				$amount = $amount * 100;

				$refund = \Stripe\Refund::create([
					'charge' => $transaction_id,
					'amount' => $amount,
				],$key['l']);
				
			}else if(!$single && $amount != null){
				
				$price = $price * 100;

				$refund = \Stripe\Refund::create([
					'charge' => $transaction_id,
					'amount' => $price,
				],$key['l']);

			}else if($single && $amount == null){
				
				$refund = \Stripe\Refund::create([
					'charge' => $transaction_id,
				],$key['l']);

			}

			return $refund;

		} catch (Exception $e) {
			  // Something else happened, completely unrelated to Stripe
			$error = $e->getMessage();
			return $error;

		}

	}


}