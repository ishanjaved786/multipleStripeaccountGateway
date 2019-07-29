<?php 

add_filter( 'woocommerce_payment_gateways', 'istripe_gc_init' );
function istripe_gc_init( $gateways ) {
	$gateways[] = 'Istripe_gc'; 
	return $gateways;
}

/*
 * The class itself, please note that it is inside plugins_loaded action hook
 */
add_action( 'plugins_loaded', 'Istripe_init_gateway_class' );
function Istripe_init_gateway_class() {

	class Istripe_gc extends WC_Payment_Gateway {

 		/**
 		 * Class constructor, more about it in Step 3
 		 */
 		public function __construct() {


			$this->id = 'istripe'; // payment gateway plugin ID
			$this->icon = ''; // URL of the icon that will be displayed on checkout page near your gateway name
			$this->has_fields = true; // in case you need a custom credit card form
			$this->method_title = 'Istripe Gateway';
			$this->method_description = 'Description of Istripe switch payment gateway developed by Ishan'; // will be displayed on the options page

			// gateways can support subscriptions, refunds, saved payment methods,
			// but in this tutorial we begin with simple payments
			$this->supports = array(
				'products',
				'subscriptions',
				'refunds',
				'subscription_cancellation', 
				'subscription_suspension', 
				'subscription_reactivation',
				'subscription_amount_changes',
				'subscription_date_changes',
				'subscription_payment_method_change',
				'subscription_payment_method_change_customer',
				'subscription_payment_method_change_admin'
			);

			// Method with all the options fields
			$this->init_form_fields();

			// Load the settings.
			$this->init_settings();
			$this->title = $this->get_option( 'iss_title' );
			$this->description = $this->get_option( 'iss_description' );
			$this->enabled = $this->get_option( 'enabled' );
			$this->testmode = 'yes' === $this->get_option( 'iss_testmode' );
			$this->private_key = $this->testmode ? $this->get_option( 'iss_test_private_key' ) : $this->get_option( 'iss_private_key' );
			$this->publishable_key = $this->testmode ? $this->get_option( 'iss_test_publishable_key' ) : $this->get_option( 'iss_publishable_key' );

			// This action hook saves the settings
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

			// We need custom JavaScript to obtain a token
			add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );

			// You can also register a webhook here
			// add_action( 'woocommerce_api_{webhook name}', array( $this, 'webhook' ) );


		}

		/**
 		 * Plugin options, we deal with it in Step 3 too
 		 */
		public function init_form_fields(){

			$this->form_fields = array(
				'enabled' => array(
					'title'       => 'Enable/Disable',
					'label'       => 'Enable Istripe Gateway',
					'type'        => 'checkbox',
					'description' => '',
					'default'     => 'no'
				),
				'iss_title' => array(
					'title'       => 'Title',
					'type'        => 'text',
					'description' => 'This controls the title which the user sees during checkout.',
					'default'     => 'Credit Card',
					'desc_tip'    => true,
				),
				'iss_description' => array(
					'title'       => 'Description',
					'type'        => 'textarea',
					'description' => 'This controls the description which the user sees during checkout.',
					'default'     => 'Pay with your credit card via our super-cool payment gateway.',
				),
				'iss_testmode' => array(
					'title'       => 'Test mode',
					'label'       => 'Enable Test Mode',
					'type'        => 'checkbox',
					'description' => 'Place the payment gateway in test mode using test API keys.',
					'default'     => 'yes',
					'desc_tip'    => true,
				),
				'iss_test_publishable_key' => array(
					'title'       => 'Test Publishable Key',
					'type'        => 'text'
				),
				'iss_test_private_key' => array(
					'title'       => 'Test Private Key',
					'type'        => 'password',
				),
				'iss_publishable_key' => array(
					'title'       => 'Live Publishable Key',
					'type'        => 'text'
				),
				'iss_private_key' => array(
					'title'       => 'Live Private Key',
					'type'        => 'password'
				)
			);

		}

		/**
		 * You will need it if you want your custom credit card form, Step 4 is about it
		 */
		public function payment_fields() {


						// ok, let's display some description before the payment form
			if ( $this->description ) {
					// you can instructions for test mode, I mean test card numbers etc.
				if ( $this->testmode ) {
					$this->description .= ' TEST MODE ENABLED. In test mode, you can use the card number 4242424242424242 and any 3 digits for CCV.';
					$this->description  = trim( $this->description );
				}
					// display the description with <p> tags etc.
				echo wpautop( wp_kses_post( $this->description ) );
			}

				// I will echo() the form, but you can close PHP tags and print it directly in HTML
			echo '<fieldset id="wc-' . esc_attr( $this->id ) . '-cc-form" class="wc-credit-card-form wc-payment-form card-js" style="background:transparent;">';

				// Add this action hook if you want your custom payment gateway to support it
			do_action( 'woocommerce_credit_card_form_start', $this->id );

				// I recommend to use inique IDs, because other gateways could already use #ccNo, #expdate, #cvc

			echo '<div class="form-row form-row-wide"><label>Card Number <span class="required">*</span></label>
			<input id="iss_ccNo" name="iss_ccNo" class="card-number" type="text" autocomplete="off" required="">
			</div>
			<div class="form-row form-row-first">
			<label>Expiry Date <span class="required">*</span></label>
			<input id="iss_expmon" name="iss_expmon"  class="expiry-month" type="text" autocomplete="off" placeholder="MM / YY" required="">
			<input id="iss_expyr" name="iss_expyr"  class="expiry-year" type="text" autocomplete="off" placeholder="MM / YY" required="">
			</div>
			<div class="form-row form-row-last">
			<label>Card Code (CVC) <span class="required">*</span></label>
			<input id="iss_cvv" name="iss_cvv" class="cvc" type="password" autocomplete="off" placeholder="CVC" required="">
			</div>
			<div class="clear"></div>';

			do_action( 'woocommerce_credit_card_form_end', $this->id );

			echo '<div class="clear"></div></fieldset>';


		}

		/*
		 * Custom CSS and JS, in most cases required only when you decided to go with a custom credit card form
		 */
		public function payment_scripts() {


			wp_enqueue_script( 'iss_cardjs', ISS_PLUGIN_URL . '/assets/js/iss_card.js' );
			wp_enqueue_script( 'stripe_js', 'https://js.stripe.com/v3/' );
			wp_enqueue_style('iss_cardcss', ISS_PLUGIN_URL . '/assets/css/card.css');


		}

		/*
 		 * Fields validation, more in Step 5
		 */
		public function validate_fields() {


			if(empty($_POST['iss_ccNo']) && strlen(trim($_POST['iss_ccNo'])) < 14 ){

				wc_add_notice(  'Card number is not correct!', 'error' );
				return false;

			}else if(empty($_POST['iss_cvv']) || strlen($_POST['iss_cvv']) > 4  || strlen($_POST['iss_cvv']) < 3){

				wc_add_notice(  'CCV is not correct!'. $_POST['iss_cvv'], 'error' );
				return false;

			}else if(empty($_POST['iss_expmon'])){

				wc_add_notice(  'Expiry date is not correct!', 'error' );
				return false;

			}else if(empty($_POST['iss_expyr'])){

				wc_add_notice(  'Expiry date is not correct!', 'error' );
				return false;
			}

			return true;

		}

		/*
		 * We're processing the payments here, everything about it is in Step 5
		 */
		public function process_payment( $order_id ) {

			require_once( ISS_PLUGIN_DIR.'/Iclass-payment-functions.php' );

			global $woocommerce;

	// we need it to get any order detailes
			$order = wc_get_order( $order_id );
	/*
 	 * Array with parameters for API interaction
	 */
	$args = array();

	$args['iss_No'] = $_POST['iss_ccNo']; 
	$args['iss_month'] = $_POST['iss_expmon']; 
	$args['iss_year'] = $_POST['iss_expyr']; 
	$args['iss_cvv'] = $_POST['iss_cvv'];


	$py = new isspaymentfunctions;
		$p_user = new issfront(); // Product check Conditions class
		$options = $p_user->stripe_params_request(); // get products conditions for stripe data
		$successCount = 0;
		$itemcount = count($order->get_items());

		$shipping = $order->get_shipping_total();

		foreach ( $order->get_items() as $item_id => $item ) {
                        // Add order pay to available pay
			$product_id = $item->get_product_id();
			$variation_id = $item->get_variation_id();
			
			if(!empty($variation_id) || $variation_id > 0){ $product_id = $variation_id; }

			$_product = wc_get_product( $product_id );

			if( $_product->is_type( 'subscription' ) || $_product->is_type('subscription_variation') ) {

				$sub_id = wcs_get_subscriptions_for_order( $order_id);

				reset($sub_id);
				$sub_id = key($sub_id);
				$price = $item->get_total();

				if(!empty($variation_id) || $variation_id > 0){

					$key = $p_user->variation_code($product_id,$options);

				}else{

					$key = $p_user->non_variation_code($product_id,$options);
					var_dump($key);
				}

				if(empty($key) || $key == false){ $key['p'] =  $this->publishable_key; $key['l'] =  $this->private_key; }
					//$res = $py->create_token($args,$key,$price,$product_id);
				$res = $py->subscription_payment($args,$key,$price,$product_id,$order_id,$sub_id);

				if($res['status'] == 'succeeded'){

					add_post_meta($order_id, '_tid_'.$item_id, $res['id']);
					$successCount++;
							
				}else{
					break;
				}

			} else {

				//Non Subscription product

				if($itemcount > 1){				
					$price = $item->get_total();
				}else{
					$price = $order->get_total();
				}

				$parent_id  = wp_get_post_parent_id( $product_id );

				if($parent_id > 0){

					$key = $p_user->variation_code($product_id,$options);

					if(empty($key) || $key == false){ $key['p'] =  $this->publishable_key; $key['l'] =  $this->private_key; }
					$res = $py->create_token($args,$key,$price,$product_id);

				}else{

					$key = $p_user->non_variation_code($product_id,$options);
					if(empty($key) || $key == false){ $key['p'] =  $this->publishable_key; $key['l'] =  $this->private_key; }
					$res = $py->create_token($args,$key,$price,$product_id);
					
				}

				if($res['status'] == 'succeeded'){

					add_post_meta($order_id, '_tid_'.$item_id, $res['id']);
					$successCount++;
				}else{
					break;
				}

			}

		} //Foreach finish

		if($successCount ==  $itemcount){

			/*if Shipping charge then pay here*/

			if(!empty($shipping) && $shipping > 0 && $itemcount > 1){

				$key = $p_user->category_check_for_shipping();
				if(empty($key) || $key == false){ $key['p'] =  $this->publishable_key; $key['l'] =  $this->private_key; }
				$charge = $py->shipping_charge($args,$key,$shipping);
				if(!empty($charge)){
					add_post_meta($order_id, '_shipping', $charge['id']);
				}
			}
			
			/*Shipping end*/

			$order->payment_complete();

			$order->reduce_order_stock();

			$order->add_order_note( 'Hey, your order is paid! Thank you!', true );

			$woocommerce->cart->empty_cart();

			return array(
				'result' => 'success',
				'redirect' => $this->get_return_url( $order )
			);

		}else{

			wc_add_notice(  $res, 'error' );
			return;

		}



	/*
	 * Your API interaction could be built with wp_remote_post()
 	 */
	//$response = wp_remote_post( '{payment processor endpoint}', $args );


/*	if( !is_wp_error( $response ) ) {

		$body = json_decode( $response['body'], true );

		 // it could be different depending on your payment processor
		if ( $body['response']['responseCode'] == 'APPROVED' ) {

			// we received the payment
			$order->payment_complete();
			$order->reduce_order_stock();

			// some notes to customer (replace true with false to make it private)
			$order->add_order_note( 'Hey, your order is paid! Thank you!', true );

			// Empty cart
			$woocommerce->cart->empty_cart();

			// Redirect to the thank you page
			return array(
				'result' => 'success',
				'redirect' => $this->get_return_url( $order )
			);

		} else {
			wc_add_notice(  'Please try again.', 'error' );
			return;
		}

	} else {
		wc_add_notice(  'Connection error.', 'error' );
		return;
	}*/


}


/*Process refunds*/

public function process_refund($order_id, $amount = null, $reason = ''){
	

	$line_item_amounts = $_POST['line_item_totals'];
	$line_item_amounts  = json_decode(stripslashes($line_item_amounts),true);


	$order  = wc_get_order( $order_id );

				  // If it's something else such as a WC_Order_Refund, we don't want that.
	if( ! is_a( $order, 'WC_Order') ) {
		return new WP_Error( 'wc-order', 'Provided ID is not a WC Order' );
	}

	if( 'refunded' == $order->get_status() ) {
		return new WP_Error( 'wc-order', 'Order has been already refunded' );
	}

	require_once( ISS_PLUGIN_DIR.'/Iclass-payment-functions.php' );

	$py = new isspaymentfunctions;
	$single = false;
		$p_user = new issfront(); // Product check Conditions class
		$options = $p_user->stripe_params_request(); // get products conditions for stripe data
		$successCount = 0;
		$itemcount = count($order->get_items());
		$shipping = $order->get_shipping_total();

		if( $itemcount == 1){
			$single = true;
		}

		// Get Items
		$order_items   = $order->get_items();

		if ( $order_items ) {
			foreach( $order_items as $item_id => $item ) {

				$product_id = $item->get_product_id();
				$variation_id = $item->get_variation_id();

				if(!empty($variation_id) || $variation_id > 0){ $product_id = $variation_id; }

				$item_meta 	= $order->get_item_meta( $item_id );
				$transaction_id = get_post_meta($order_id, '_tid_'.$item_id, true); //Stripe transaction id of item

				if(!empty($transaction_id)){

					if($itemcount > 1){				
						//$price = $item->get_total();
						if(!empty($line_item_amounts[$item_id])){
						    $price = $line_item_amounts[$item_id];
						}else{ continue; }

					}else{
						if($amount == null){
							$price = $order->get_total();
						}else{
							$price = $amount;
						}
					}

					$parent_id  = wp_get_post_parent_id( $product_id );

					if($parent_id > 0){

						$key = $p_user->variation_code($product_id,$options);
						if(empty($key) || $key == false){ $key['p'] =  $this->publishable_key; $key['l'] =  $this->private_key; }
						$res = $py->return_amount($key,$transaction_id,$order_id,$amount,$single,$price);

					}else{

						$key = $p_user->non_variation_code($product_id,$options);
						if(empty($key) || $key == false){ $key['p'] =  $this->publishable_key; $key['l'] =  $this->private_key; }
						$res = $py->return_amount($key,$transaction_id,$order_id,$amount,$single,$price);

					}					

					// Error handling
					if(strpos($res, 'already been refunded') === false){
						if( $res['status'] != 'succeeded' ){
							return new WP_Error( 'wc-order', $res );
						}
					} 

				}else{

					return new WP_Error( 'wc-order', 'transaction_id not found!' );
				}
			}

			if(!empty($shipping) && $shipping > 0 && $itemcount > 1){

				$ship_id = get_post_meta($order_id, '_shipping', true); //Stripe transaction id of item

				if(!empty($ship_id)){

					foreach( $order->get_items( 'shipping' ) as $item_id => $shipping_item_obj ){
						// Get the data in an unprotected array
						
						if(!empty($line_item_amounts[$item_id])){
						    $price = $line_item_amounts[$item_id];
						
						$shipping_available_cat = 'supplements';
						$key = $p_user->category_check_for_shipping($shipping_available_cat,$options);
						if(empty($key) || $key == false){ $key['p'] =  $this->publishable_key; $key['l'] =  $this->private_key; }
						$res = $py->return_amount($key,$ship_id,$order_id,$amount,$single,$price);
						
							if(strpos($res, 'already been refunded') === false){
								if( $res['status'] != 'succeeded' ){
									return new WP_Error( 'wc-order', $res );
								}
							}
						}else{ continue; }
					}

				}else{
					return new WP_Error( 'wc-order', 'Shipping charge transaction_id not found!' );
				}
			}

			return true;

		}
	}

		/*
		 * In case you need a webhook, like PayPal IPN etc
		 */
		public function webhook() {



		}


	}
}