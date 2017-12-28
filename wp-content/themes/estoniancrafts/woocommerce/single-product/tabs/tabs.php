<?php
/**
 * Single Product tabs
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/tabs/tabs.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Filter tabs and allow third parties to add their own.
 *
 * Each tab is an array containing title, callback and priority.
 * @see woocommerce_default_product_tabs()
 */
$tabs = apply_filters( 'woocommerce_product_tabs', array() );

$tabs_layout = (basel_product_design() == 'compact') ? 'accordion' : 'tabs';

$user = wp_get_current_user();
$isLoggedIn = $user && $user->ID;
global $product;

$o = get_post_meta( $product->id, '_wc_review_count', true );


if ( ! empty( $tabs ) ) : ?>

	<div class="woocommerce-tabs wc-tabs-wrapper tabs-layout-<?php echo esc_attr( $tabs_layout ); ?>">
		<ul class="tabs wc-tabs">
			<?php  foreach ( $tabs as $key => $tab ) : ?>
                <?php

                 if ($key != 'ask_information' || ($key == 'ask_information' && $isLoggedIn)) { ?>
                		 	<?php

						   if( (esc_attr( $key ) == 'reviews' && ( (int) $o > 0  || (wc_customer_bought_product( '', get_current_user_id(), $product->id    ) && commented_before( wp_get_current_user()->user_login, $product->id  ) == false  )  ) ) || esc_attr( $key ) != 'reviews'   ):
						 	?>
						<li class="<?php echo esc_attr( $key ); ?>_tab">
							<a href="#tab-<?php echo esc_attr( $key ); ?>" id="iden-<?php echo esc_attr( $key ); ?>"><?php echo apply_filters( 'woocommerce_product_' . $key . '_tab_title', esc_html( $tab['title'] ), $key );?> </a>
						</li>
						<?php
							endif;
						?>
                <?php }  //echo get_current_user_id(). ' '. $product->id. ' '. $product->post->post_author. ' '.wc_customer_bought_product( get_current_user_id(), $product->id, $product->post->post_author ); ?>
			<?php endforeach; ?>
		</ul>
		<?php foreach ( $tabs as $key => $tab ) : ?>
	
			 	<?php
			 	/*	if( (esc_attr( $key ) == 'reviews' && $o !== '0' && wc_customer_bought_product( get_current_user_id(), $product->id, $product->post->post_author ) === false )  || esc_attr( $key ) != 'reviews'  ):*/

			 	?>
				<div class="basel-tab-wrapper">
					<a href="#tab-<?php echo esc_attr( $key ); ?>" class="basel-accordion-title tab-title-<?php echo esc_attr( $key ); ?>"><?php echo apply_filters( 'woocommerce_product_' . $key . '_tab_title', esc_html( $tab['title'] ), $key ); ?></a>
					<div class="woocommerce-Tabs-panel woocommerce-Tabs-panel--<?php echo esc_attr( $key ); ?> panel entry-content wc-tab" id="tab-<?php echo esc_attr( $key ); ?>">

					
						<?php call_user_func( $tab['callback'], $key, $tab ); ?>
					</div>
				</div>
			<?php //endif; ?>
		<?php endforeach; ?>
	</div>

<?php endif; ?>

<script>
	if(jQuery("#iden-reviews").html() === "Reviews (0) ")
		jQuery(".reviews_tab").css("display","none");
</script>
