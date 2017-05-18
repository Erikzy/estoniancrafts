<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'vc_before_init', 'zmbSingleProduct' );
function zmbSingleProduct() {

	if ( !class_exists( 'WooCommerce' ) ) {
		return;
	}

	global $zan_vc_anim_effects_in;
	vc_map(
		array(
			'name'     => esc_html__( 'Zan Single Product', 'zanmb' ),
			'base'     => 'zmb_single_product', // shortcode
			'class'    => '',
			'category' => esc_html__( 'Zan', 'zanmb' ),
			'params'   => array(

				array(
					'type'        => 'autocomplete',
					'heading'     => esc_html__( 'Product', 'zanmb' ),
					'param_name'  => 'product_id',
					'settings'    => array(
						'multiple' => false,
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
					'std'        => '#cecece',
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
add_filter( 'vc_autocomplete_zmb_single_product_product_id_callback', 'zmb_vc_include_field_product_search', 10, 1 ); // Get suggestion(find). Must return an array
add_filter( 'vc_autocomplete_zmb_single_product_product_id_render', 'zmb_vc_include_field_render', 10, 1 ); // Render exact product. Must return an array (label,value)

function zmb_single_product( $atts ) {

	$atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'zmb_single_product', $atts ) : $atts;

	if ( !class_exists( 'WooCommerce' ) ) {
		return '';
	}

	extract(
		shortcode_atts(
			array(
				'product_id'      => '',
				'title_color'     => '#cecece',
				'price_color'     => '#49c3df',
				'css_animation'   => '',
				'animation_delay' => '0.4',  // In second
				'css'             => '',
			), $atts
		)
	);

	$css_class = 'zmb-single-product-wrap zan-single-product-wrap ' . $css_animation;

	if ( trim( $css_animation ) != '' ) {
		$css_class .= ' wow';
	}

	if ( function_exists( 'vc_shortcode_custom_css_class' ) ):
		$css_class .= ' ' . apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), '', $atts );
	endif;

	if ( !is_numeric( $animation_delay ) ) {
		$animation_delay = 0;
	}

	$product_id = intval( $product_id );

	$animation_delay = $animation_delay . 's';
	$html = '';

	if ( $product_id > 0 ) {

		$product = wc_get_product( $product_id );

		if ( $product ) {
			$img = zmb_resize_image( get_post_thumbnail_id( $product->id ), null, 262, 295, true, true, false );

			$html .= '<div class="single-product-inner">
							<figure>
								<img src="' . esc_url( $img['url'] ) . '" width="' . esc_attr( $img['width'] ) . '" height="' . esc_attr( $img['height'] ) . '" alt="' . esc_attr( $product->get_title() ) . '">
							</figure>
							<h3 style="color: ' . esc_attr( $title_color ) . ';">' . esc_html( $product->get_title() ) . '</h3>
							<span class="price" style="color: ' . esc_attr( $price_color ) . ';">' . $product->get_price_html() . '</span>
						</div>';

		}

		if ( trim( $html ) != '' ) {

			$html = '<div class="' . esc_attr( $css_class ) . '" data-wow-delay="' . esc_attr( $animation_delay ) . '">
                       ' . $html . '
                    </div>';

		}
	}

	return $html;

}

add_shortcode( 'zmb_single_product', 'zmb_single_product' );
