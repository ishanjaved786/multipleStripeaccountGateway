<?php 

/**
 * 
 */
class issfront
{
	
	function __construct()
	{

		//$this->Init();

	}


	public function Init(){

		//add_filter('wc_stripe_params ', array($this,'stripe_params'));
		add_action('wp', array($this, 'checkout_code'));
	}
 

	public function checkout_code(){
 
		
		if(is_checkout()){

			add_filter('wc_stripe_payment_request_params', array($this,'stripe_params_request'));

		}

	}


	public function stripe_params_request(){


		$args = array(
			'numberposts' => -1,
			'post_type'   => 'stripe_setting'
		  );
		   
		  $Ssettings = get_posts( $args );
		  $options = array();

		  if(count($Ssettings) > 0 ){
			foreach ($Ssettings as $item) {
				$id = $item->ID; 
				$option = get_post_meta($id, 'options', true);
				if(!empty($option)){
					array_push($options, $option);
				}
			}

		  }else{

			$options = 'error';

		  }

		return $options;

	}



	public function variation_code($product_id,$options){

		$key = false;

		if($options != 'error' && is_array($options)){

			foreach ($options as $key => $value) {
					
			$variation_id =	explode(',',  $value['tvi']);
				if(!empty($variation_id)){
					if(in_array($product_id, $variation_id)){
							$key = $this->switch_keys($value);
							break;
					}
				}
			}

		}else{

			return false;

		}

		return $key;
 
	}


	public function non_variation_code($product_id,$options){

		$keys = false;

		if($options != 'error' && is_array($options)){

			// Product id check for setting ids
			foreach ($options as $key => $value) {
					
			$variation_id =	explode(',',  $value['tpi']);
				if(!empty($variation_id)){
					if(in_array($product_id, $variation_id)){
						echo 1;	
						$keys = $this->switch_keys($value);
							break;
					}
				}
			}

			// if not found in setting ids then category check
			if($keys == false){
			 
			$cat = false; 
			 $terms = get_the_terms( $product_id, 'product_cat' );
			  if($terms != false){
				foreach ($options as $key => $value) {	//Stripe setting array
					foreach ($terms as $term) { // Terms array
						if(!empty($value['tselect']) && in_array($term->slug, $value['tselect'])){
							echo 2;
							$cat = true;
							$keys = $this->switch_keys($value);
							break;
						}
					}
				}
			  }else{
					return false;
			  }

			}
		}else{
			return false;
		}



		return $keys;

	}


	public function switch_keys($options){


		$st = get_option('woocommerce_stripe_settings');
		$test = false;
		
		if($st['testmode'] == 'yes'){ $test = true; }

			if($test){

				$key['p'] = $options['tpublic'];
				$key['l'] = $options['tprivate'];

				return $key;

			}else{

				$key['p'] = $options['lpublic'];	
				$key['l'] = $options['lprivate'];	

				return $key;
			}

	}


	public function category_check_for_shipping(){

		$key = false;

		$shipping_check = get_option('iss_shipping');
		if(!empty($shipping_check)){

			$setting_meta = get_post_meta($shipping_check, 'options', true);
			if(!empty($setting_meta)){

				$key = $this->switch_keys($setting_meta);
			
			}
		}
		return $key;
	}


}

?>