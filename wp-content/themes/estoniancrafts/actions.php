<?php

class EC_Actions
{
	public static function init()
	{
		// Google analytics
		add_action( 'wp_head', array(__CLASS__, 'wp_head_google_analytics_action') );

		add_action( 'wp_enqueue_scripts', array(__CLASS__, 'ec_custom_styles_js_action') );
		add_action( 'wp_loaded', array(__CLASS__, 'wp_loaded_action') );
		add_action( 'wp_loaded', array(__CLASS__, 'wp_loaded_debug_action'), 9999 );

		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price' );
//		add_action( 'woocommerce_after_shop_loop_item_title', array(__CLASS__, 'shop_loop_item_categories_action'), 9 );
		add_action( 'woocommerce_after_shop_loop_item_title', array(__CLASS__, 'shop_loop_item_price_action'), 10 );
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

		// After header
		remove_action( 'basel_after_header', 'basel_page_title' );
		add_action( 'basel_after_header', array(__CLASS__, 'basel_page_title'), 10 );

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

	public static function shop_loop_item_price_action()
	{
		woocommerce_template_loop_price();
	}

	public static function shop_loop_item_categories_action()
	{
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

	public static function basel_page_title()
	{
        global $wp_query, $post;

        // Remove page title for dokan store list page
        if( function_exists( 'dokan_is_store_page' )  && dokan_is_store_page() ) {
        	return '';
        }

		$page_id = 0;

		$disable     = false;
		$page_title  = true;
		$breadcrumbs = basel_get_opt( 'breadcrumbs' );

		$image = '';

		$style = '';

		$page_for_posts    = get_option( 'page_for_posts' );
		$page_for_shop     = get_option( 'woocommerce_shop_page_id' );
		$page_for_projects = basel_tpl2id( 'portfolio.php' );

		$title_class = 'page-title-';

		$title_color = $title_type = $title_size = 'default';

		// Get default styles from Options Panel
		$title_design = basel_get_opt( 'page-title-design' );

		$title_size = basel_get_opt( 'page-title-size' );

		$title_color = basel_get_opt( 'page-title-color' );

		$shop_title = basel_get_opt( 'shop_title' );
		$shop_categories = basel_get_opt( 'shop_categories' );


		// Set here page ID. Will be used to get custom value from metabox of specific PAGE | BLOG PAGE | SHOP PAGE.
		$page_id = basel_page_ID();
		if( $page_id != 0 ) {
			// Get meta value for specific page id
			$disable = get_post_meta( $page_id, '_basel_title_off', true );

			$image = get_post_meta( $page_id, '_basel_title_image', true );

			$custom_title_color = get_post_meta( $page_id, '_basel_title_color', true );
			$custom_title_bg_color = get_post_meta( $page_id, '_basel_title_bg_color', true );


			if( $image != '' ) {
				$style .= "background-image: url(" . $image . ");";
			}

			if( $custom_title_bg_color != '' ) {
				$style .= "background-color: " . $custom_title_bg_color . ";";
			}

			if( $custom_title_color != '' && $custom_title_color != 'default' ) {
				$title_color = $custom_title_color;
			}
		}

		if( $title_design == 'disable' ) $page_title = false;

		if( ! $page_title && ! $breadcrumbs ) $disable = true;

		if( $disable ) return;

		$title_class .= $title_type;
		$title_class .= ' title-size-'  . $title_size;
		$title_class .= ' color-scheme-' . $title_color;
		$title_class .= ' title-design-' . $title_design;

/* Sven: disabled for now
		// Heading for pages
		if( is_singular( 'page' ) && ( ! $page_for_posts || ! is_page( $page_for_posts ) ) ):
			$title = get_the_title();

			?>
			<div class="ec-full-slider-top"><?php echo do_shortcode( '[html_block id="537"]' ); ?></div>
				<div class="page-title <?php echo esc_attr( $title_class ); ?>" style="<?php echo esc_attr( $style ); ?>">
					<div class="container">
						<header class="entry-header">
							<?php if( $breadcrumbs ) basel_breadcrumbs(); ?>
							<?php if( $page_title ): ?><h1 class="entry-title"><?php echo esc_html( $title ); ?></h1><?php endif; ?>
						</header><!-- .entry-header -->
					</div>
				</div>
			<?php
			return;
		endif;
*/

		// Heading for blog and archives
		if( is_home() || is_singular( 'post' ) || is_search() || is_tag() || is_category() || is_date() || is_author() ):

			$title = ( ! empty( $page_for_posts ) ) ? get_the_title( $page_for_posts ) : esc_html__( 'Blog', 'basel' );

			if( is_tag() ) {
				$title = esc_html__( 'Tag Archives: ', 'basel')  . single_tag_title( '', false ) ;
			}

			if( is_category() ) {
				$title = '<span>' . single_cat_title( '', false ) . '</span>'; //esc_html__( 'Category Archives: ', 'basel') . 
			}

			if( is_date() ) {
				if ( is_day() ) :
					$title = esc_html__( 'Daily Archives: ', 'basel') . get_the_date();
				elseif ( is_month() ) :
					$title = esc_html__( 'Monthly Archives: ', 'basel') . get_the_date( _x( 'F Y', 'monthly archives date format', 'basel' ) );
				elseif ( is_year() ) :
					$title = esc_html__( 'Yearly Archives: ', 'basel') . get_the_date( _x( 'Y', 'yearly archives date format', 'basel' ) );
				else :
					$title = esc_html__( 'Archives', 'basel' );
				endif;
			}

			if ( is_author() ) {
				/*
				 * Queue the first post, that way we know what author
				 * we're dealing with (if that is the case).
				 *
				 * We reset this later so we can run the loop
				 * properly with a call to rewind_posts().
				 */
				the_post();

				$title = esc_html__( 'Posts by ', 'basel' ) . '<span class="vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '" title="' . esc_attr( get_the_author() ) . '" rel="me">' . get_the_author() . '</a></span>';

				/*
				 * Since we called the_post() above, we need to
				 * rewind the loop back to the beginning that way
				 * we can run the loop properly, in full.
				 */
				rewind_posts();
			}

			if( is_search() ) {
				$title = esc_html__( 'Search Results for: ', 'basel' ) . get_search_query();
			}

			?>
				<div class="page-title <?php echo esc_attr( $title_class ); ?> title-blog" style="<?php echo esc_attr( $style ); ?>">
					<div class="container">
						<header class="entry-header">
							<?php if( $page_title ): ?><h1 class="entry-title"><?php echo  $title; ?></h1><?php endif; ?>
							<?php if( $breadcrumbs ) basel_breadcrumbs(); ?>
						</header><!-- .entry-header -->
					</div>
				</div>
			<?php
			return;
		endif;

		// Heading for portfolio
		if( is_post_type_archive( 'portfolio' ) || is_singular( 'portfolio' ) || is_tax( 'project-cat' ) ):

			$title = get_the_title( $page_for_projects );

			if( is_tax( 'project-cat' ) ) {
				$title = single_term_title( '', false );
			}

			?>
				<div class="page-title <?php echo esc_attr( $title_class ); ?> title-blog" style="<?php echo esc_attr( $style ); ?>">
					<div class="container">
						<header class="entry-header">
							<?php if( $page_title ): ?><h1 class="entry-title"><?php echo esc_html( $title ); ?></h1><?php endif; ?>
							<?php if( $breadcrumbs ) basel_breadcrumbs(); ?>
						</header><!-- .entry-header -->
					</div>
				</div>
			<?php
			return;
		endif;

		// Page heading for shop page
		if( basel_woocommerce_installed() && ( is_shop() || is_product_category() || is_product_tag() || is_singular( "product" ) )
			&& ( $shop_categories || $shop_title )
		 ):

			if( is_product_category() ) {

		        $cat = $wp_query->get_queried_object();

				$cat_image = basel_get_category_page_title_image( $cat );

				if( $cat_image != '') {
					$style = "background-image: url(" . $cat_image . ")";
				}
			}

			if( ! $shop_title ) {
				$title_class .= ' without-title';
			}

			?>
				<?php if ( apply_filters( 'woocommerce_show_page_title', true ) && ! is_singular( "product" ) ) : ?>
					<div class="page-title <?php echo esc_attr( $title_class ); ?> title-shop" style="<?php echo esc_attr( $style ); ?>">
						<div class="container">
							<div class="nav-shop">
								
								<?php if ( is_product_category() || is_product_tag() ): ?>
									<?php basel_back_btn(); ?>
								<?php endif ?>

								<?php if ( $shop_title ): ?>
									<h1><?php woocommerce_page_title(); ?></h1>
								<?php endif ?>
								
								<?php if( ! is_singular( "product" ) && $shop_categories ) basel_product_categories_nav(); ?>

							</div>
						</div>
					</div>
				<?php endif; ?>

			<?php
			
			return;
		endif;
	}

}
EC_Actions::init();