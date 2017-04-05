<?php
/**
 * Plugin Name: Dokan extension
 * Description: Käsitööturg custom extension for Dokan plugin
 * Version: 1.0
 */


class lbDokan{

	function __construct(){

		add_action( 'dokan_store_profile_saved', [$this, 'save_shop'] );
		add_action( 'wp_enqueue_scripts', [$this, 'register_scripts'], 15 );
		add_action( 'woocommerce_save_account_details', [$this, 'save_user_details'] );
		
	}

	function register_scripts(){
		global $wp_scripts;

		if (!is_admin()) {
			wp_enqueue_style( 'lb-dokan', plugin_dir_url( __FILE__ ) . 'dokan.css', false,'1.0','all');
			wp_enqueue_script( 'lb-dokan', plugin_dir_url( __FILE__ ) . 'dokan.js', false,'1.0','all');
		}

	}

	function save_shop($store_id){

		$ext_settings = get_user_meta( $store_id, 'ktt_extended_settings', true );

    	$ext_settings['company_name'] = ! empty( $_POST['dokan_company_name'] ) ? wc_clean( $_POST['dokan_company_name'] ) : '';
    	$ext_settings['company_nr'] = ! empty( $_POST['dokan_company_nr'] ) ? wc_clean( $_POST['dokan_company_nr'] ) : '';
    	$ext_settings['company_type'] = ! empty( $_POST['dokan_company_type'] ) ? wc_clean( $_POST['dokan_company_type'] ) : '';
    	$ext_settings['description'] = ! empty( $_POST['dokan_description'] ) ? wc_clean( $_POST['dokan_description'] ) : '';

    	if( ! empty( $_POST['dokan_media'] ) ){

    		$media = $_POST['dokan_media'];

    		// Remove all empty strings first
    		$media = array_diff($media, array('http://', 'https://', ''));

	    	// Make sure all media liks have http:// or https:// in front of them
	    	$media = array_map(function($element) {
			        return (strpos($element, 'http://') !== 0 && strpos($element, 'https://') !== 0)? 'http://'.$element : $element;
			    },
			    $media
			);

			if(!count($media)){ $media = ['']; }
    		
    		$ext_settings['media'] = wc_clean( $media );
    	}

    	if( ! empty( $_POST['dokan_address'] ) ){

    		$addresses = [];

    		foreach ($_POST['dokan_address'] as $address) {
    			
    			$address_data_entered = array_diff($address, array('', ' '));
    			if( count($address_data_entered) ) { 
    				$addresses[] = $address; 
    			}

    		}

    		if(!count($addresses)){ 
    			$addresses = [['country' => false, 'state' => '', 'city' => '', 'address' => '', 'email' => '', 'phone' => '']]; 
    		}

    		$ext_settings['address'] = wc_clean( array_values($addresses) );

    	}


	    update_user_meta( $store_id, 'ktt_extended_settings', $ext_settings );

	}


	function save_user_details( $user_ID ){

		$user_avatar = get_user_meta( $user_ID, 'dokan_profile_settings', true );
	    $user_avatar['gravatar'] = $_POST['dokan_gravatar'];
	    update_user_meta( $user_ID, 'dokan_profile_settings', $user_avatar );

		$ext_profile = get_user_meta( $user_ID, 'ktt_extended_profile', true );

    	$ext_profile['mobile'] = ! empty( $_POST['account_mobile'] ) ? wc_clean( $_POST['account_mobile'] ) : '';
    	$ext_profile['skype'] = ! empty( $_POST['account_skype'] ) ? wc_clean( $_POST['account_skype'] ) : '';
    	$ext_profile['gender'] = ! empty( $_POST['account_gender'] ) ? wc_clean( $_POST['account_gender'] ) : '';
    	$ext_profile['dob'] = ! empty( $_POST['account_dob'] ) ? wc_clean( $_POST['account_dob'] ) : '';
    	$ext_profile['workyears'] = ! empty( $_POST['account_workyears'] ) ? wc_clean( $_POST['account_workyears'] ) : '';
    	$ext_profile['video'] = ! empty( $_POST['account_video'] ) ? wc_clean( $_POST['account_video'] ) : '';
    	$ext_profile['description'] = ! empty( $_POST['account_description'] ) ? wc_clean( $_POST['account_description'] ) : '';
    	$ext_profile['education'] = ! empty( $_POST['account_education'] ) ? wc_clean( $_POST['account_education'] ) : '';
    	$ext_profile['country'] = ! empty( $_POST['account_location_country'] ) ? wc_clean( $_POST['account_location_country'] ) : '';
    	$ext_profile['state'] = ! empty( $_POST['account_location_state'] ) ? wc_clean( $_POST['account_location_state'] ) : '';
    	$ext_profile['city'] = ! empty( $_POST['account_location_city'] ) ? wc_clean( $_POST['account_location_city'] ) : '';
    	$ext_profile['address'] = ! empty( $_POST['account_location_address'] ) ? wc_clean( $_POST['account_location_address'] ) : '';

	    update_user_meta( $user_ID, 'ktt_extended_profile', $ext_profile );

	}

	static function user_profile_completeness($user_id){

		$required_fields = ['mobile', 'skype', 'gender', 'dob', 'workyears', 'video', 'description', 'education', 'country', 'state', 'city', 'address'];

		$ext_profile = get_user_meta( $user_id, 'ktt_extended_profile', true );
		$completeness = 0;

		foreach($required_fields as $req_field){

			if( isset($ext_profile[$req_field]) && !empty($ext_profile[$req_field]) ){
				$completeness += 100/count($required_fields);
			}

		}

		return floor($completeness);

	}

	static function shop_profile_completeness($user_id){

		$required_fields = ['company_name', 'company_nr', 'company_type', 'description', 'media', 'address'];

		$ext_profile = get_user_meta( $user_id, 'ktt_extended_settings', true );
		$completeness = 0;

		foreach($required_fields as $req_field){

			if( isset($ext_profile[$req_field]) && !empty($ext_profile[$req_field]) ){

				if($req_field == 'media' || $req_field == 'address'){
    				$media = array_diff($ext_profile[$req_field], array('http://', 'https://', ''));

    				if( count($media) ){
    					$completeness += 100/count($required_fields);
    				}
				}else{
					$completeness += 100/count($required_fields);
				}
			}

		}

		return floor($completeness);

	}

}

new lbDokan();