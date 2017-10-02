<?php
/**
 * Related Products
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product, $woocommerce_loop;

if ( empty( $product ) || ! $product->exists() ) {
	return;
}

$posts_per_page = 12;

$related = $product->get_related( $posts_per_page );

if ( sizeof( $related ) == 0 ) return;

$args = apply_filters( 'woocommerce_related_products_args', array(
	'post_type'            => 'product',
	'ignore_sticky_posts'  => 1,
	'no_found_rows'        => 1,
	'posts_per_page'       => $posts_per_page,
	'orderby'              => $orderby,
	'post__in'             => $related,
	'post__not_in'         => array( $product->id )
) );


$products = new WP_Query( $args );

$slider_args = array(
	'slides_per_view' => apply_filters( 'basel_related_products_per_view', 4 ),
	'title' => __( 'Related Products', 'woocommerce' ),
	'img_size' => 'shop_catalog'
);
echo 'test';

echo basel_generate_posts_slider( $slider_args, $products );
