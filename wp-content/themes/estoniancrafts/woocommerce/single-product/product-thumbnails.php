<?php
/**
 * Single Product Thumbnails
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

$image_view = '<a href="%s" class="%s" title="%s" data-rel="product-images[product-gallery]" data-single-image="%s" data-width="%s" data-height="%s">%s</a>';

$thumb_image_size = 'shop_thumbnail';

$show_main = true;

$count = count($attachment_ids) + 1;

// Full size images for sticky product design
if( $product_design == 'sticky' ) {
	$thums_position = 'bottom';
	$thumb_image_size = 'shop_single';
	$show_main = false;
}

if ( $attachment_ids ) {
	$loop 		= 0;
	$columns 	= apply_filters( 'woocommerce_product_thumbnails_columns', 3 );
	?>

	<div class="thumbnails <?php echo 'columns-' . $columns; ?> <?php if ( $product_design == 'sticky' ): ?>thumbnails-large<?php endif ?> count-<?php echo esc_attr($count); ?>">
	   <?php
	   		

			//basel_product_video_car_button($post->ID);
		?>
		<?php
			
		$classes = array( 'image-link' );

		if( $show_main ) {
			basel_product_video_car_button($post->ID);
			$main_attachment_id = get_post_thumbnail_id( $post->ID );

			$image        = wp_get_attachment_image( $main_attachment_id, apply_filters( 'single_product_small_thumbnail_size', $thumb_image_size ) );
			$image_single = wp_get_attachment_image_src( $main_attachment_id, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ) );
			$image_class  = esc_attr( implode( ' ', array_merge($classes, array( 'current-image' )) ) );
			$image_title  = esc_attr( get_the_title( $main_attachment_id ) );
			$image_link   = wp_get_attachment_image_src( $main_attachment_id, 'full' );

			echo apply_filters( 
				'woocommerce_single_product_image_thumbnail_html', 
				sprintf( 
					$image_view, 
					esc_url( $image_link[0] ), 
					esc_attr( $image_class ), 
					esc_attr( $image_title ), 
					$image_single[0], 
					$image_link[1], 
					$image_link[2], 
					$image 
				), 
				$main_attachment_id, 
				$post->ID, 
				$image_class 
			);
		}

		foreach ( $attachment_ids as $attachment_id ) {

			$image_link = wp_get_attachment_image_src( $attachment_id, 'full' );

			if ( ! $image_link )
				continue;

			$image        = wp_get_attachment_image( $attachment_id, apply_filters( 'single_product_small_thumbnail_size', $thumb_image_size ) );
			$image_single = wp_get_attachment_image_src( $attachment_id, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ) );
			$image_class  = esc_attr( implode( ' ', $classes ) );
			$image_title  = esc_attr( get_the_title( $attachment_id ) );

			echo apply_filters( 
				'woocommerce_single_product_image_thumbnail_html', 
				sprintf( 
					$image_view, 
					esc_url( $image_link[0] ), 
					esc_attr( $image_class ), 
					esc_attr( $image_title ), 
					$image_single[0], 
					$image_link[1], 
					$image_link[2], 
					$image 
				), 
				$attachment_id, 
				$post->ID, 
				$image_class 
			);

			$loop++;
		}

	?>	</div>

	<?php // No slider for sticky product (large full size images) ?>
	<?php if ($product_design != 'sticky'): ?>

		<?php
			$items = array();
			$items['desktop'] = 4;
			$items['desktop_small'] = 3;
			$items['tablet'] = 4;
			$items['mobile'] = 3;
		?>


		<script type="text/javascript">

			var baselThumbsOwlCarousel = function() {
				jQuery('.images .thumbnails').owlCarousel({
		            items: <?php echo esc_js( $items['desktop'] ); ?>, 
		            itemsDesktop: [1199,<?php echo esc_js( $items['desktop'] ); ?>],
		            itemsDesktopSmall: [979,<?php echo esc_js( $items['desktop_small'] ); ?>],
		            itemsTablet: [768,<?php echo esc_js( $items['tablet'] ); ?>],
		            itemsMobile: [479,<?php echo esc_js( $items['mobile'] ); ?>],
					pagination:false,
					navigation: true,
					navigationText: false,
				});
			};

			var baselThumbsSlickCarousel = function() {
				jQuery('.images .thumbnails').slick({
					slidesToShow: 3,
					slidesToScroll: 3,
					vertical: true,
					verticalSwiping: true,
					infinite: false,
				});
			}

		</script>

		<?php if ( $thums_position == 'left' && ! $is_quick_view ): ?>
			<script type="text/javascript">

				jQuery(document).ready(function(){
					if( jQuery(window).width() > 991 ) {
						baselThumbsSlickCarousel();
						setTimeout(function() {
							jQuery('.images .thumbnails').slick('setPosition');
						}, 1500);
					} else {
						baselThumbsOwlCarousel();
					}
				});

			</script>
		<?php else: ?>
			<script type="text/javascript">

				jQuery(document).ready(function(){
					baselThumbsOwlCarousel();
				});

			</script>

		<?php endif ?>
	<?php endif ?>

	<?php
}
