<?php

class EC_Actions
{
	public static function init()
	{
		add_action( 'wp_enqueue_scripts', array(__CLASS__, 'ec_custom_styles_js_action') );
		add_action( 'wp_loaded', array(__CLASS__, 'wp_loaded_action') );
		add_action( 'wp_loaded', array(__CLASS__, 'wp_loaded_debug_action'), 9999 );

//		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price' );
//		add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
		add_action( 'woocommerce_after_shop_loop_item_title', array(__CLASS__, 'woocommerce_after_shop_loop_item_title_action'), 10 );
	
		// Google analytics
		add_action( 'wp_head', array(__CLASS__, 'wp_head_google_analytics_action') );
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

	public static function woocommerce_after_shop_loop_item_title_action()
	{
		woocommerce_template_loop_price();
		basel_product_categories();
	}

	public static function wp_loaded_debug_action()
	{
		global $wp_filter, $wp_actions, $merged_filters, $wp_current_filter;
//		ec_debug_to_console('$wp_actions', $wp_actions);
//		ec_debug_to_console('$wp_filter', $wp_filter);
//		ec_debug_to_console('$merged_filters', $merged_filters);
//		ec_debug_to_console('$wp_current_filter', $wp_current_filter);
	}

	public static function wp_head_google_analytics_action()
	{
		$html = <<<HTML
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-85465038-1', 'auto');
  ga('send', 'pageview');

</script>
HTML;
		print $html;
	}
}
EC_Actions::init();