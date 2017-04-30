<?php
	/**
	 * The Template for displaying all single posts.
	 *
	 * @package dokan
	 * @package dokan - 2014 1.0
	 */
	
	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
	$store_user   = get_userdata( get_query_var( 'author' ) );
	$store_info   = dokan_get_store_info( $store_user->ID );
	$map_location = isset( $store_info['location'] ) ? esc_attr( $store_info['location'] ) : '';
	
	get_header( 'shop' );
	?>
<?php do_action( 'woocommerce_before_main_content' ); ?>
<?php if ( dokan_get_option( 'enable_theme_store_sidebar', 'dokan_general', 'off' ) == 'off' ) { ?>
<div id="dokan-secondary" class="dokan-clearfix dokan-w3 dokan-store-sidebar" role="complementary" style="margin-right:3%;">
	<div class="profile-image" style="background-image: url(https://estoniancrafts.client.creativemeka.ee/wp-content/uploads/2017/03/mina-2.jpg)"></div>
	<h5 class="widget-title">
		<a href="#">Storename</a>
	</h5>
	<a href="www.saaremaasepad.ee">www.saaremaasepad.ee</a>
	<ul class="social-nav">
		<li class="facebook"><a href="#" target="_blank"><i class="fa fa-facebook fa-lg"></i></a></li>
		<li class="twitter"><a href="#" target="_blank"><i class="fa fa-twitter fa-lg"></i></a></li>
		<li class="instagram"><a href="#" target="_blank"><i class="fa fa-instagram fa-lg"></i></a></li>
		<li class="linkedin"><a href="#" target="_blank"><i class="fa fa-linkedin fa-lg"></i></a></li>
		<li class="youtube"><a href="#" target="_blank"><i class="fa fa-youtube fa-lg"></i></a></li>
		<li class="instagram"><a href="#" target="_blank"><i class="fa fa-instagram fa-lg"></i></a></li>
		<li class="flickr"><a href="#" target="_blank"><i class="fa fa-flickr fa-lg"></i></a></li>
	</ul>
	<div class="co-workers">
		<ul>
			<li class="co-worker">
				<a href="#">
					<img class="co-worker-img" src="https://estoniancrafts.client.creativemeka.ee/wp-content/uploads/2017/03/mina-2.jpg">
					<h4>Raivo Ramm</h4>
					<em>Moekunstnik</em>
				</a>
			</li>
			<li class="co-worker">
				<a href="#">
					<img class="co-worker-img" src="https://estoniancrafts.client.creativemeka.ee/wp-content/uploads/2017/03/mina-2.jpg">
					<h4>Raivo Ramm</h4>
					<em>Moekunstnik</em>
				</a>
			</li>
		</ul>
	</div>
	<div class="dokan-widget-area widget-collapse">
		<?php do_action( 'dokan_sidebar_store_before', $store_user, $store_info ); ?>
		<?php
			if ( ! dynamic_sidebar( 'sidebar-store' ) ) {
			
			    $args = array(
			        'before_widget' => '<aside class="widget">',
			        'after_widget'  => '</aside>',
			        'before_title'  => '<h3 class="widget-title">',
			        'after_title'   => '</h3>',
			    );
			
			    if ( class_exists( 'Dokan_Store_Location' ) ) {
			        the_widget( 'Dokan_Store_Category_Menu', array( 'title' => __( 'Store Category', 'dokan' ) ), $args );
			
			        if ( dokan_get_option( 'store_map', 'dokan_general', 'on' ) == 'on' ) {
			            the_widget( 'Dokan_Store_Location', array( 'title' => __( 'Store Location', 'dokan' ) ), $args );
			        }
			
			        if ( dokan_get_option( 'contact_seller', 'dokan_general', 'on' ) == 'on' ) {
			            the_widget( 'Dokan_Store_Contact_Form', array( 'title' => __( 'Contact Seller', 'dokan' ) ), $args );
			        }
			    }
			
			}
			?>
		<?php do_action( 'dokan_sidebar_store_after', $store_user, $store_info ); ?>
	</div>
</div>
<!-- #secondary .widget-area -->
<?php
	} else {
	    get_sidebar( 'store' );
	}
	?>
<div id="dokan-primary" class="dokan-single-store dokan-w8">
	<div id="dokan-content" class="store-page-wrap woocommerce" role="main">
		<div class="user-hero profile-info-summery-wrapper dokan-clearfix" style="background-image: url(https://estoniancrafts.client.creativemeka.ee/wp-content/uploads/2017/03/cropped-testblogi-pilt.jpg)">
			<div class="class-effect">
				<div class="float-right">
					<h1 class="store_name">Storename</h1>
					<p class="ext_shop">Ettevõtte kirjeldus siia. Lorem ipsum dodlor sit amaet. Lorem lipsum dolor sit amet.</p>
				</div>
				<div class="bottom-bar">
				</div>
			</div>
		</div>
		<?php dokan_get_template_part( 'store-header' ); ?>
		<?php do_action( 'dokan_store_profile_frame_after', $store_user, $store_info ); ?>
		<?php if ( have_posts() ) { ?>
		<div class="seller-items">
			<?php woocommerce_product_loop_start(); ?>
			<?php while ( have_posts() ) : the_post(); ?>
			<?php wc_get_template_part( 'content', 'product' ); ?>
			<?php endwhile; // end of the loop. ?>
			<?php woocommerce_product_loop_end(); ?>
		</div>
		<?php dokan_content_nav( 'nav-below' ); ?>
		<?php } else { ?>
		<p class="dokan-info"><?php _e( 'No products were found of this seller!', 'dokan' ); ?></p>
		<?php } ?>
	</div>
</div>
<!-- .dokan-single-store -->
<div style="background:pink; width: 33.3333%; padding:15px; float: left;">
	<strong>$store_user array VÄLJUNDID</strong>
	<hr>
	<?php print_r($store_user) ?>
</div>
<div style="background:yellow; width: 33.3333%; padding:15px; float: left;">
	<strong>$store_info array VÄLJUNDID</strong>
	<hr>
	<?php print_r($store_info) ?>
</div>
<div style="background:orange; width: 33.3333%; padding:15px; float: left;">
	<strong>$map_location array VÄLJUNDID</strong>
	<hr>
	<?php print_r($map_location) ?>
</div>
<?php do_action( 'woocommerce_after_main_content' ); ?>
<?php get_footer( 'shop' ); ?>