<?php
/**
 * Plugin Name: Woocommerce SKU generation
 * Description: Käsitööturg auto SKU
 * Version: 1.0
 */

class lbSku{

	function __construct(){

		add_action( 'save_post', [$this, 'update'] );

	}

	// TODO: properly test it afted Donkan plugin update (SKU bug)
	// TODO: SKU format... 10 - digit code?
	function update( $post_id ) {

		if ( get_post_type($post_id) == "product" ) {

			$generated_sku = get_post_meta( $post_id, '_generated_sku', true);

			if( empty($generated_sku) ){
				$generated_sku = 'A-'.$post_id . time();
        		update_post_meta( $post_id, '_generated_sku', $generated_sku );
			}

			$sku = get_post_meta( $post_id, '_sku', true);

			
			if( empty($sku) ){
			
        		update_post_meta( $post_id, '_sku', $generated_sku );
				
			}

		}

	}

}

new lbSku();
