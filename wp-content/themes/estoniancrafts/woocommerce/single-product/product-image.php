<?php
/**
 * Single Product Image
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.6.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $product, $woocommerce, $woocommerce_loop;

$is_quick_view = (isset($woocommerce_loop['view']) && $woocommerce_loop['view'] == 'quick-view');

$attachment_ids = $product->get_gallery_attachment_ids();

$thums_position = basel_get_opt('thums_position');
$product_design = basel_product_design();

// Full size images for sticky product design
if( $product_design == 'sticky' ) {
	$thums_position = 'bottom';
}

?>
<!--
<script type="text/javascript" src="/jquery.fancybox-1.3.4/jquery.fancybox-1.3.4/fancybox/jquery.fancybox-1.3.4.js"></script>
<link rel="stylesheet" type="text/css" href="/jquery.fancybox-1.3.4/jquery.fancybox-1.3.4/fancybox/jquery.fancybox-1.3.4.css" media="screen" />
-->

<div class="images row thumbs-position-<?php echo esc_attr( $thums_position ); ?>">

	<div class="<?php if ( $attachment_ids && $thums_position == 'left' && ! $is_quick_view ): ?>col-md-9 col-md-push-3<?php else: ?>col-sm-12<?php endif ?>">
		<?php
			if ( has_post_thumbnail() ) {

				$image_title = esc_attr( get_the_title( get_post_thumbnail_id() ) );
				$image_link  = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
				$image       = get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ), array(
					'title' => $image_title
					) );

				$attachment_count = count( $product->get_gallery_attachment_ids() );

				if ( $attachment_count > 0 ) {
					$gallery = '[product-gallery]';
				} else {
					$gallery = '';
				}

				echo apply_filters( 
					'woocommerce_single_product_image_html', 
					sprintf( 
						'<a href="%s" itemprop="image" class="woocommerce-main-image zoom image-link" title="%s" data-rel="product-images%s" data-width="%s" data-height="%s">%s</a>', 
						esc_url( $image_link[0] ), 
						esc_attr( $image_title ), 
						$gallery,
						$image_link[1], 
						$image_link[2], 
						$image 
					), 
					$post->ID 
				);

			} else {

				echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<img src="%s" alt="%s" />', wc_placeholder_img_src(), __( 'Placeholder', 'woocommerce' ) ), $post->ID );

			}
		?>
	</div>

	<?php if ( $attachment_ids ): ?>
		<div class="<?php if ( $thums_position == 'left' && ! $is_quick_view ): ?>col-md-3 col-md-pull-9<?php else: ?>col-sm-12<?php endif ?>">
			<?php do_action( 'woocommerce_product_thumbnails' ); ?>
		</div>
	<?php endif; ?>

</div>
