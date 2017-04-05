<?php

class EC_Actions
{
	public static function init()
	{
		add_action( 'wp_enqueue_scripts', array(__CLASS__, 'ec_custom_styles_js_action') );
		add_action( 'wp_loaded', array(__CLASS__, 'wp_loaded_action') );
	}

	/**
	 * Include styles and scripts
	 */
	public static function ec_custom_styles_js_action()
	{
		$template_url = get_template_directory_uri();
		$child_theme_url = get_stylesheet_directory_uri();

		// Public assets
		wp_enqueue_style('ec-public-style', $child_theme_url.'/ec-assets/style_public.css');
		wp_enqueue_script('ec-public-script', $child_theme_url.'/ec-assets/script_public.js');

		// Merchant assets
		wp_enqueue_style('bootstrap337-style', $child_theme_url.'/ec-assets/bootstrap-3.3.7/css/bootstrap.min.css');
		wp_enqueue_script('bootstrap337-script', $child_theme_url.'/ec-assets/bootstrap-3.3.7/js/bootstrap.min.js');

		wp_enqueue_style('ec-merchant-style', $child_theme_url.'/ec-assets/style_merchant.css');
		wp_enqueue_script('ec-merchant-script', $child_theme_url.'/ec-assets/script_merchant.js');

		// Unregister font-awsome css registered by dokan plugin
		wp_deregister_style('fontawesome');
		wp_deregister_style('bootstrap');
	}

	public static function wp_loaded_action()
	{
//		remove_action('dokan_dashboard_content_before', 'get_dashboard_side_navigation');

		// Remove woocimmerce account navigation
		remove_action( 'woocommerce_account_navigation', 'woocommerce_account_navigation' );

		// Remove default myaccount page title and replace with custom one
		remove_action( 'woocommerce_account_navigation', 'basel_before_my_account_navigation', 1 );
		add_action( 'woocommerce_account_navigation', array(__CLASS__, 'woocommerce_account_navigation_action'), 1 );
	}

	public static function woocommerce_account_navigation_action()
	{
		echo '<div class="basel-my-account-sidebar">';
	}
}
EC_Actions::init();