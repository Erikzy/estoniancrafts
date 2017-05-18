<?php if ( ! defined('BASEL_THEME_DIR')) exit('No direct script access allowed');

/**
 * Register widget based on VC_MAP parameters that displays user panel
 *
 */

if ( ! class_exists( 'EC_Myaccount_Sidebar_Menu_Widget' ) ) {
class EC_Myaccount_Sidebar_Menu_Widget extends WPH_Widget
{
	function __construct()
	{
		if( ! function_exists( 'basel_get_user_panel_params' ) ) return;

		// Configure widget array
		$args = array( 
			'label' => __( 'EC Myaccount Sidebar Menu', 'ktt' ), 
			'description' => __( 'Sidebar menu to use in My Account area', 'ktt' ),
			'fields' => array()
		 );

		// create widget
		$this->create_widget( $args );
	}

	// Output function
	function widget( $args, $instance )
	{
		extract($args);
		$menu = apply_filters('ec_get_myaccount_menu', null);

		echo $before_widget;

		if(!empty($instance['title'])) { echo $before_title . $instance['title'] . $after_title; };

		do_action( 'wpiw_before_widget', $instance );

		include(locate_template('templates/myaccount/sidebar_menu_widget.php'));

		do_action( 'wpiw_after_widget', $instance );

		echo $after_widget;
	}

	function form( $instance )
	{
		parent::form( $instance );
	}
}
}