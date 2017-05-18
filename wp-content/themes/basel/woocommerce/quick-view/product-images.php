<?php
/**
 * Single Product Images
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $product, $woocommerce, $woocommerce_loop;

$is_quick_view = (isset($woocommerce_loop['view']) && $woocommerce_loop['view'] == 'quick-view');

$attachment_ids = $product->get_gallery_attachment_ids();

$attachment_count = count( $attachment_ids );

$thums_position = basel_get_opt('thums_position');

?>
<div class="images">
	

	<div class="product-images-slider">
		<?php
			if ( has_post_thumbnail() ) {

				$image_title = esc_attr( get_the_title( get_post_thumbnail_id() ) );
				
				echo get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ), array(
					'title' => $image_title
					) );


				if ( $attachment_count > 0 ) {
					foreach ( $attachment_ids as $attachment_id ) {

						echo wp_get_attachment_image( $attachment_id, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ) );

					}
				}

			} else {

				echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<img src="%s" alt="%s" />', wc_placeholder_img_src(), __( 'Placeholder', 'woocommerce' ) ), $post->ID );

			}

		?>
	</div>
<?php 

	if ( $attachment_count > 0 ) {
		?>

			<script type="text/javascript">

				jQuery('.product-images-slider').owlCarousel({
		            items: 1, 
		            itemsDesktop: [1199, 1],
		            itemsDesktopSmall: [979, 1],
		            itemsTablet: [768, 1],
		            itemsMobile: [479, 1],
					pagination:false,
					navigation: true,
					navigationText: false,
				});

			</script>
		<?php
	}

 ?>
</div>
