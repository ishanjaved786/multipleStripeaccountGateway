<?php

/**
  * 
  */
class issregister {

	function __construct()
	{

		add_action('admin_menu', array($this,'adminmenu'));
		add_action( 'admin_enqueue_scripts', array($this,'add_assets'));
		$this->ajax_hooks();

	}

	public function ajax_hooks(){

		add_action('wp_ajax_iss_data_update', array($this, 'iss_data_update'));
		add_action('wp_ajax_iss_data_delete', array($this, 'iss_data_delete'));
		add_action('wp_ajax_iss_shipping_update', array($this, 'iss_shipping_update'));
		add_action('wp_ajax_NewStripeAjax', array($this, 'newstripe'));
		add_action('woocommerce_scheduled_subscription_payment_istripe', array($this, 'scheduled_subscription_payment_istripe'), 10, 2 );
		//add_action('woocommerce_subscription_payment_complete', array($this, 'subscription_payment_complete_hook_callback'), 10, 2);

	}

	public function adminmenu(){

		add_menu_page( 'IswitchStripe', 'IswitchStripe', 'administrator', 'istripeswitch', array($this, 'home'), 'dashicons-media-spreadsheet', 10 );

	}

	public function add_assets(){

		if(@$_GET['page'] == 'istripeswitch'){

			wp_enqueue_style( 'istripeswitch', ISS_PLUGIN_URL . '/assets/css/iss-custom.css' );
			wp_enqueue_style( 'select2_css', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/css/select2.min.css' );
			wp_enqueue_script( 'select2_js', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/js/select2.min.js' );
			wp_enqueue_script( 'istripeswitch', ISS_PLUGIN_URL . '/assets/js/iss_custom.js' );
			wp_localize_script( 'istripeswitch', 'aj',
				array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		}

	}


	public function home(){

		$args = array(
			'taxonomy'   => "product_cat",
			'hide_empty' => false
		);

		$product_categories = get_terms($args);
		$shipping_check = get_option('iss_shipping');

		//Fetch Stripe Settings

		$args = array(
			'numberposts' => -1,
			'post_type'   => 'stripe_setting'
		  );
		   
		  $Ssettings = get_posts( $args );

		//	include_once ISS_PLUGIN_DIR.'/template/main.php';

			include_once ISS_PLUGIN_DIR.'/template/dynamic_main.php';

	}

	public function iss_data_update(){

		parse_str($_POST['data'], $temp);

			if(update_post_meta( $_POST['id'], 'options' , $temp)){
				echo 1;
			}else{
				echo 0;
			}

		die();

	}


	public function iss_data_delete(){

		$del = wp_delete_post( $_POST['id'], true );

		if($del != false || $del != null){
			echo 1;
		}else{
			echo 0;
		}

		die();

	}

	public function iss_shipping_update(){

		$ship = update_option('iss_shipping', $_POST['ship_id']); 

		if($ship){
			echo 1;
		}else{
			echo 0;
		}
		die();

	}


	public function newstripe(){

	   $name =	wp_strip_all_tags( $_POST['name'] );

	   $my_post = array(
		'post_title'    => $name,
		'post_content'  => '',
		'post_status'   => 'publish',
		'post_author'   => 1,
		'post_type'     => 'stripe_setting'
	  
	);
	   
	  // Insert the post into the database
		if(wp_insert_post( $my_post )){
			echo 1;
		}else{
			echo 'error';
		}
		die();
	}


	public function scheduled_subscription_payment_istripe($amount_to_charge, $renewal_order){

		$id = get_post_meta( $renewal_order->get_id(), '_subscription_renewal', true );

		$result = $this->process_subscription_renewal_istripe($amount_to_charge, $id, $renewal_order);

	}


	public function process_subscription_renewal_istripe($amount_to_charge, $order_id, $renewal_order){

		
		$order = wc_get_order( $order_id );

		require_once( ISS_PLUGIN_DIR.'/Iclass-payment-functions.php' );
		
		$op = get_option('woocommerce_istripe_settings');
		$testmode = 'yes' === $op['iss_testmode'];
		$private_key = $testmode ? $op['iss_test_private_key'] : $op['iss_private_key'];
		$publishable_key = $testmode ? $op['iss_test_publishable_key'] : $op['iss_publishable_key'];

		$py = new isspaymentfunctions;
		$p_user = new issfront(); // Product check Conditions class
		$options = $p_user->stripe_params_request(); // get products conditions for stripe data

				foreach ( $order->get_items() as $item_id => $item ) {
                        // Add order pay to available pay
					$product_id = $item->get_product_id();

				$key = $p_user->non_variation_code($product_id,$options);

				if(empty($key) || $key == false){ $key['p'] =  $publishable_key; $key['l'] =  $private_key; }
				$cid = get_post_meta( $order_id, 'cid', true ); 

				if(!empty($cid)){
					$res = $py->subscription_payment_renew($key,$amount_to_charge,$product_id,$cid);

					if($res['status'] == 'succeeded'){

						$renewal_order->payment_complete();

						WC_Subscriptions_Manager::process_subscription_payments_on_order( $renewal_order );

					}else{

						WC_Subscriptions_Manager::process_subscription_payment_failure_on_order( $renewal_order, $product_id );

					}
				}else{
						WC_Subscriptions_Manager::process_subscription_payment_failure_on_order( $renewal_order, $product_id );

				}
			}

	}



public function subscription_payment_complete_hook_callback( $subscription, $last_order ) {

        $last_order->payment_complete('completed');
}


} 


new issregister();

?>