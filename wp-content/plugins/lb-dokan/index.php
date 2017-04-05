<?php
/**
 * Plugin Name: Dokan extension
 * Description: Käsitööturg custom extension for Dokan plugin
 * Version: 1.0
 */


class lbDokan{

	function __construct(){

		add_action( 'dokan_store_profile_saved', [$this, 'save_shop'] );
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
    		
    		$ext_settings['media'] = wc_clean( $media );
    	}

    	if( ! empty( $_POST['dokan_address'] ) ){

    		$addresses = [];

    		foreach ($_POST['dokan_address'] as $address) {
    			
    			$address = array_diff($address, array('', ' '));
    			if( count($address) ) { 
    				$addresses[] = $address; 
    			}

    		}

    		$ext_settings['address'] = wc_clean( array_values($addresses) );

    	}


	    update_user_meta( $store_id, 'ktt_extended_settings', $ext_settings );

	}

}

new lbDokan();