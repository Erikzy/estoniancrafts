<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'vc_before_init', 'zmbProductsList' );
function zmbProductsList() {

	if ( !class_exists( 'WooCommerce' ) ) {
		return;
	}

	global $zan_vc_anim_effects_in;
	vc_map(
		array(
			'name'     => esc_html__( 'Zan Products List', 'zanmb' ),
			'base'     => 'zmb_products_list', // shortcode
			'class'    => '',
			'category' => esc_html__( 'Zan', 'zanmb' ),
			'params'   => array(

				array(
					'type'        => 'autocomplete',
					'heading'     => esc_html__( 'Products', 'zanmb' ),
					'param_name'  => 'product_ids',
					'settings'    => array(
						'multiple' => true,
						'sortable' => true,
					),
					'save_always' => true,
					'std'         => '',
					'description' => esc_html__( 'Input product ID or product title to see suggestions.', 'zanmb' ),
				),
				array(
					'type'       => 'colorpicker',
					'holder'     => 'div',
					'class'      => '',
					'heading'    => esc_html__( 'Product Title Color', 'zanmb' ),
					'param_name' => 'title_color',
					'std'        => '#1a1a1a',
				),
				array(
					'type'       => 'colorpicker',
					'holder'     => 'div',
					'class'      => '',
					'heading'    => esc_html__( 'Price Color', 'zanmb' ),
					'param_name' => 'price_color',
					'std'        => '#49c3df',
				),
				array(
					'type'       => 'dropdown',
					'holder'     => 'div',
					'class'      => '',
					'heading'    => esc_html__( 'CSS Animation', 'zanmb' ),
					'param_name' => 'css_animation',
					'value'      => $zan_vc_anim_effects_in,
					'std'        => 'fadeInUp',
				),
				array(
					'type'        => 'textfield',
					'holder'      => 'div',
					'class'       => '',
					'heading'     => esc_html__( 'Animation Delay', 'zanmb' ),
					'param_name'  => 'animation_delay',
					'std'         => '0.4',
					'description' => esc_html__( 'Delay unit is second.', 'zanmb' ),
					'dependency'  => array(
						'element'   => 'css_animation',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'css_editor',
					'heading'    => esc_html__( 'Css', 'zanmb' ),
					'param_name' => 'css',
					'group'      => esc_html__( 'Design options', 'zanmb' ),
				)
			),
		)
	);
}

// vc_autocomplete_{short_code_name}_{param_name}_callback
add_filter( 'vc_autocomplete_zmb_products_list_product_ids_callback', 'zmb_vc_include_field_product_search', 10, 1 ); // Get suggestion(find). Must return an array
add_filter( 'vc_autocomplete_zmb_products_list_product_ids_render', 'zmb_vc_include_field_render', 10, 1 ); // Render exact product. Must return an array (label,value)

function zmb_products_list( $atts ) {

	$atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'zmb_products_list', $atts ) : $atts;

	if ( !class_exists( 'WooCommerce' ) ) {
		return '';
	}

	extract(
		shortcode_atts(
			array(
				'product_ids'     => '',
				'title_color'     => '#1a1a1a',
				'price_color'     => '#49c3df',
				'css_animation'   => '',
				'animation_delay' => '0.4',  // In second
				'css'             => '',
			), $atts
		)
	);

	$css_class = 'zmb-products-list-wrap zan-products-list-wrap ' . $css_animation;

	if ( trim( $css_animation ) != '' ) {
		$css_class .= ' wow';
	}

	if ( function_exists( 'vc_shortcode_custom_css_class' ) ):
		$css_class .= ' ' . apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), '', $atts );
	endif;

	if ( !is_numeric( $animation_delay ) ) {
		$animation_delay = 0;
	}

	$animation_delay = $animation_delay . 's';
	$html = '';

	if ( trim( $product_ids ) != '' ) {
		$product_ids = explode( ',', $product_ids );

		$query_args = array(
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'post__in'            => $product_ids,
			'posts_per_page'      => -1,
		);

		$products = new WP_Query( $query_args );
		$total_products = $products->found_posts;

		if ( $products->have_posts() ) :
			// Open first ul
			$html .= '<ul class="products-list">';
			while ( $products->have_posts() ) : $products->the_post();
				$product = wc_get_product( get_the_ID() );
				$img = zmb_resize_image( get_post_thumbnail_id(), null, 82, 82, true, true, false );
				$price_html = '';
				if ( $product->get_price_html() ) {
					$price_html = '<span class="price" style="color: ' . esc_attr( $price_color ) . ';">' . $product->get_price_html() . '</span>';
				}
				$html .= '<li class="product-item">
								<figure>
									<img width="' . esc_attr( $img['width'] ) . '"
									     height="' . esc_attr( $img['height'] ) . '"
									     src="' . esc_url( $img['url'] ) . '" alt="' . esc_attr( get_the_title() ) . '"/>
								</figure>
								<div class="product-info-wrap">
									<a href="' . esc_url( get_permalink() ) . '" class="product-item-title" style="color: ' . esc_attr( $title_color ) . ';">
										' . esc_html( get_the_title() ) . '
									</a>
									' . $price_html . '
								</div>
							</li>';
			endwhile;
			$html .= '</ul>';

		endif; // End if ( $products->have_posts() )
		wp_reset_postdata();

		if ( trim( $html ) != '' ) {

			$html = '<div class="' . esc_attr( $css_class ) . '" data-wow-delay="' . esc_attr( $animation_delay ) . '">
                       ' . $html . '
                    </div>';

		}
	}

	return $html;

}

add_shortcode( 'zmb_products_list', 'zmb_products_list' );
