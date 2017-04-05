<?php
/**
 * Plugin Name: Woocommerce SKU generation
 * Description: Käsitööturg auto SKU
 * Version: 1.0
 */

class lbSku{

	function __construct(){

		add_action( 'save_post_product', [$this, 'update'], 12, 2 );

	}

	// TODO: properly test it afted Donkan plugin update (SKU bug)
	// TODO: SKU format... 10 - digit code?
	function update( $product_id, $post ) {

		 //If is doing auto-save: exit function
        if( defined('DOING_AUTOSAVE') AND DOING_AUTOSAVE ) return;

        //If is doing auto-save via AJAX: exit function
        if( defined( 'DOING_AJAX' ) && DOING_AJAX ) return;

        if (isset($post->post_status) && 'auto-draft' == $post->post_status) {
            return;
        }

        if( ! ( wp_is_post_revision( $product_id) || wp_is_post_autosave( $product_id ) ) ) {

        	// error_log( 'check / generate new SKU' );

			$generated_sku = get_post_meta( $product_id, '_generated_sku', true);

			if( empty($generated_sku) ){
				$generated_sku = 'A-'.$product_id . time();
        		update_post_meta( $product_id, '_generated_sku', $generated_sku );
			}

			$sku = get_post_meta( $product_id, '_sku', true);

			if( empty($sku) ){
			
        		update_post_meta( $product_id, '_sku', $generated_sku );
				
			}

		}

	}

}

new lbSku();
