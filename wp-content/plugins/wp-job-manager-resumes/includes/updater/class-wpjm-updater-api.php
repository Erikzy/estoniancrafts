<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WPJM_Updater_UI
 *
 * @version 3.0
 * @author  Mike Jolley
 */
class WPJM_Updater_API {

	private static $api_url = 'http://wpjobmanager.com/?wc-api=wp_plugin_licencing_update_api';

	/**
	 * Sends and receives data to and from the server API
	 * @return object $response
	 */
	public static function plugin_update_check( $args ) {
		$defaults = array(
			'request'        => 'pluginupdatecheck',
			'instance'       => site_url(),
			'plugin_name'    => '',
			'version'        => '',
			'api_product_id' => '',
			'licence_key'    => '',
			'email'          => ''
		);

		$args    = wp_parse_args( $args, $defaults );
		$request = wp_remote_get( self::$api_url . '&' . http_build_query( $args, '', '&' ) );

		if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
			return false;
		}

		$response = maybe_unserialize( wp_remote_retrieve_body( $request ) );

		if ( is_object( $response ) ) {
			return $response;
		} else {
			return false;
		}
	}

	/**
	 * Sends and receives data to and from the server API
	 * @return object $response
	 */
	public static function plugin_information( $args ) {
		$defaults = array(
			'request'        => 'plugininformation',
			'instance'       => site_url(),
			'plugin_name'    => '',
			'version'        => '',
			'api_product_id' => '',
			'licence_key'    => '',
			'email'          => ''
		);

		$args    = wp_parse_args( $args, $defaults );
		$request = wp_remote_get( self::$api_url . '&' . http_build_query( $args, '', '&' ) );

		if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
			return false;
		}

		$response = maybe_unserialize( wp_remote_retrieve_body( $request ) );

		if ( is_object( $response ) ) {
			return $response;
		} else {
			return false;
		}
	}
}
