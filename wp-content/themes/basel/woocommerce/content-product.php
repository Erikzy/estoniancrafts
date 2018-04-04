<?php
/**
 * The template for displaying product content within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version 	2.6.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product, $woocommerce_loop, $basel_loop;

$slider = false;

// Store loop count we're currently on
if ( empty( $woocommerce_loop['loop'] ) )
	$woocommerce_loop['loop'] = 0;

if ( empty( $woocommerce_loop['quick_view_loop'] ) )
	$woocommerce_loop['quick_view_loop'] = 0;

// Store column count for displaying the grid
if ( empty( $woocommerce_loop['columns'] ) )
	$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );

// if content in slider carousel
if ( ! empty( $woocommerce_loop['slider'] ) )
	$slider = true;

// Ensure visibility
if ( ! $product || ( ! $slider && ! $product->is_visible() ) )
	return;

// Increase loop count
//$woocommerce_loop['loop']++;
$woocommerce_loop['quick_view_loop']++;

// Extra post classes
$classes = array( 'product-grid-item' );

$hover = 1;

if( basel_get_opt( 'products_hover' ) != '' ) {
	$hover = basel_get_opt( 'products_hover' );
}

if( ! empty( $woocommerce_loop['product_hover'] ) ) {
	$hover = $woocommerce_loop['product_hover'];
}

$classes[] = 'product'; 
$classes[] = 'basel-hover-' . $hover; 

$isotope 		 = ( basel_get_opt( 'products_masonry' ) || ! empty( $woocommerce_loop['masonry'] ) );
$different_sizes = basel_get_opt( 'products_different_sizes' );

$items_wide = basel_get_wide_items_array( $different_sizes );
if( $different_sizes && ( in_array( $woocommerce_loop['loop'] - 1, $items_wide ) ) ) { 
	$basel_loop['double_size'] = true;
}

if( ! $slider )
	$classes[] = basel_get_grid_el_class($woocommerce_loop['loop'] - 1, $woocommerce_loop['columns'], $different_sizes);

?>
<div <?php post_class( $classes ); ?>>
	<?php wc_get_template_part( 'content', 'product-' . $hover ); ?>

</div>
<?php $basel_loop['double_size'] = false; ?>
<?php if( ! $slider && ! $isotope ) echo basel_get_grid_clear($woocommerce_loop['loop'], $woocommerce_loop['columns']); ?>