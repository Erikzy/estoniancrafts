<?php
/**
 * Cross-sells
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product, $woocommerce_loop;

$crosssells = WC()->cart->get_cross_sells();

if ( sizeof( $crosssells ) == 0 ) return;

$meta_query = WC()->query->get_meta_query();

$args = array(
	'post_type'           => 'product',
	'ignore_sticky_posts' => 1,
	'no_found_rows'       => 1,
	'posts_per_page'      => apply_filters( 'woocommerce_cross_sells_total', $posts_per_page ),
	'orderby'             => $orderby,
	'post__in'            => $crosssells,
	'meta_query'          => $meta_query
);


$products = new WP_Query( $args );

$slider_args = array(
	'slides_per_view' => apply_filters( 'basel_cross_sells_products_per_view', 3 ),
	'hide_pagination_control' => true,
	'hide_prev_next_buttons' => true,
);

?>
<div class="cross-sells">
	<h3><?php _e( 'You may be interested in&hellip;', 'woocommerce' ) ?></h3>
	<?php echo basel_generate_posts_slider( $slider_args, $products ); ?>
</div>