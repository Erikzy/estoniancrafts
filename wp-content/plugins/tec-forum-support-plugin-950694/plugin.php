<?php
/**
 * Plugin Name:       The Events Calendar: Snippet 950694
 * Plugin URI:        https://github.com/bordoni/tec-forum-support/tree/plugin-950694
 * Description:       The Events Calendar Support Addon
 * Version:           0.1.4
 * Author:            Gustavo Bordoni
 * Author URI:        http://bordoni.me
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ){
	die;
}

class TEC_Forum_950694 {

	public static $ID = 950694;

	public static $_instance = null;

	public $instances = array();

	public function __construct() {
		if ( class_exists( 'Tribe__Events__Main' ) ){
			$this->instances['tec'] = Tribe__Events__Main::instance();
		} else {
			$this->instances['tec'] = TribeEvents::instance();
		}

		add_filter( 'tribe_general_settings_tab_fields', array( $this, 'options_field' ), 15, 1 );
		add_filter( 'tribe_community_events_form_errors', array( $this, 'community_events_form_errors' ), 10, 1 );
	}

	public static function instance() {
		if ( ! ( self::$_instance instanceof self ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public function community_events_form_errors( $messages ) {
		if ( is_array( $messages ) && ! empty( $messages ) ) {
			$first_message = reset( $messages );
			if ( 'update' == $first_message['type'] ) {
				add_action( 'parse_request', array( $this, 'redirect_after_community_submission' ), 15, 1 );
			}
		}
		return $messages;
	}

	public function redirect_after_community_submission( $wp ) {
		if (
			isset( $wp->query_vars[ WP_Router::QUERY_VAR ] )
			&& (
				'ce-add-route' == $wp->query_vars[ WP_Router::QUERY_VAR ]
				|| 'ce-edit-route' == $wp->query_vars[ WP_Router::QUERY_VAR ]
			)
			&& ! empty( $_POST )
		) {
			$redirect = $this->instances['tec']->getOption( self::$ID . '_redirect', null );
			$home = home_url();
			if ( empty( $redirect ) ){
				return;
			}

			if ( false !== strpos( $redirect, 'http://' ) || false !== strpos( $redirect, 'https://' ) ){
				$redirect = home_url( str_replace( $home, '', $redirect ) );
			}
			exit( wp_redirect( $redirect ) );
		}
	}

	public function options_field( $fields = array() ) {
		// Creates the field configurations
		$field = array(
			self::$ID . '_redirect' => array(
				'type'            => 'text',
				'label'           => esc_attr__( 'Redirect after Submit', 'tribe-events-calendar' ),
				'tooltip'         => esc_attr__( 'Where should the user be redirected to after the Community Submit', 'tribe-events-calendar' ),
				'validation_type' => 'html',
			)
		);

		// Places this field in the right spot
		$key = array_search( 'multiDayCutoff', array_keys( $fields ) );
		$total = count( $fields );

		$fields = array_slice( $fields, 0, $key, true ) + $field + array_slice( $fields, 3, $total - 1, true );
		return $fields;
	}

}
add_action( 'init', array( 'TEC_Forum_950694', 'instance' ) );

