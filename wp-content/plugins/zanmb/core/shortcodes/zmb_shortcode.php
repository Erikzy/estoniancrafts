<?php

if ( !defined( 'ABSPATH' ) ) {
	exit; // disable direct access
}

function zmb_nav_menu_shortcode( $atts ) {
	extract(
		shortcode_atts(
			array(
				'location' => '',
			), $atts
		)
	);

	$html = '';

	$registered_locations = get_nav_menu_locations();

	// If menu location exist
	if ( isset( $registered_locations[$location] ) ) {
		ob_start();
		apply_filters( 'zmb_menu_location', array( 'theme_location' => $location ) );
		$html .= ob_get_clean();
	}

	if ( trim( $html ) != '' ) {
		$html = '<div class="zmb-shortcode-nav-wrap">' . $html . '</div>';
	}

	return $html;
}

add_shortcode( 'wpmm', 'zmb_nav_menu_shortcode' );
add_shortcode( 'wp_mega_menu', 'zmb_nav_menu_shortcode' );
add_shortcode( 'zmb_nav_menu', 'zmb_nav_menu_shortcode' );