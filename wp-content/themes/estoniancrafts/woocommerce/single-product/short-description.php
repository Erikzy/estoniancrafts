<?php
/**
 * Single product short description
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/short-description.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post;

if ( ! $post->post_excerpt ) {
	return;
}

?>
<div itemprop="description">
	<?php echo apply_filters( 'woocommerce_short_description', $post->post_excerpt ) ?>
</div>
  		    <?php
				$_size_chart = get_post_meta( $post->id, '_size_chart', true );
				if($_size_chart == "yes"){
					echo '<div class="sold-list">';
					echo '<a href="'.get_site_url(null, 'size-chart').'">Size chart</a>';
					echo '</div>';
				}	
			?>  <?php
				$_shoe_size_chart = get_post_meta( $post->id, '_shoe_size_chart', true );
				if($_shoe_size_chart == "yes"){
					echo '<div class="sold-list">';
					echo '<a href="'.get_site_url(null, 'shoe-size-chart').'">Shoe size chart</a>';
					echo '</div>';
				}	
			?>