<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}


add_action( 'vc_before_init', 'zmbContent' );
function zmbContent() {
	global $zan_vc_anim_effects_in;
	$allowed_tags = array(
		'em'     => array(),
		'i'      => array(),
		'b'      => array(),
		'strong' => array(),
		'a'      => array(
			'href'   => array(),
			'target' => array(),
			'class'  => array(),
			'id'     => array(),
			'title'  => array(),
		),
	);
	vc_map(
		array(
			'name'     => esc_html__( 'Zan Content', 'zanmb' ),
			'base'     => 'zmb_content', // shortcode
			'class'    => '',
			'category' => esc_html__( 'Zan', 'zanmb' ),
			'params'   => array(
				array(
					'type'       => 'textarea_html',
					'holder'     => 'div',
					'class'      => '',
					'heading'    => esc_html__( 'Content', 'zanmb' ),
					'param_name' => 'content',
					'std'        => '',
				),
				array(
					'type'       => 'dropdown',
					'class'      => '',
					'heading'    => esc_html__( 'Enable Content Image', 'zanmb' ),
					'param_name' => 'enable_content_img',
					'value'      => array(
						esc_html__( 'Yes', 'zammb' ) => 'yes',
						esc_html__( 'No', 'zanmb' )  => 'no'
					),
					'std'        => 'yes'
				),
				array(
					'type'        => 'attach_image',
					'holder'      => 'div',
					'class'       => '',
					'heading'     => esc_html__( 'Image', 'zanmb' ),
					'param_name'  => 'img_id',
					'description' => esc_html__( 'Choose image', 'zanmb' ),
					'dependency'  => array(
						'element' => 'enable_content_img',
						'value'   => 'yes',
					),
				),
				array(
					'type'        => 'textfield',
					'holder'      => 'div',
					'class'       => '',
					'heading'     => esc_html__( 'Image Size', 'zanmb' ),
					'param_name'  => 'img_size',
					'std'         => '263x188',
					'description' => wp_kses( __( '<em>{width}x{height}</em>. Example: <em>263x188</em>, etc...', 'zanmb' ), $allowed_tags ),
					'dependency'  => array(
						'element' => 'enable_content_img',
						'value'   => 'yes',
					),
				),
				array(
					'type'       => 'dropdown',
					'class'      => '',
					'heading'    => esc_html__( 'Enable Do Shortcode', 'zanmb' ),
					'param_name' => 'enable_do_shortcode',
					'value'      => array(
						esc_html__( 'Yes', 'zammb' ) => 'yes',
						esc_html__( 'No', 'zanmb' )  => 'no'
					),
					'std'        => 'yes'
				),
				array(
					'type'       => 'dropdown',
					'holder'     => 'div',
					'class'      => '',
					'heading'    => esc_html__( 'CSS Animation', 'zanmb' ),
					'param_name' => 'css_animation',
					'value'      => $zan_vc_anim_effects_in,
					'std'        => 'fadeInUp'
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
			)
		)
	);
}


function zmb_content( $atts, $content = null ) {

	$atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'zmb_content', $atts ) : $atts;

	extract(
		shortcode_atts(
			array(
				'enable_content_img'  => 'yes',
				'img_id'              => '',
				'img_size'            => '263x188',
				'enable_do_shortcode' => 'yes',
				'css_animation'       => '',
				'animation_delay'     => '0.4',  // In second
				'css'                 => '',
			), $atts
		)
	);

	$css_class = 'zanmb-content zan-content wow ' . $css_animation;
	if ( function_exists( 'vc_shortcode_custom_css_class' ) ):
		$css_class .= ' ' . apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), '', $atts );
	endif;

	if ( !is_numeric( $animation_delay ) ) {
		$animation_delay = '0';
	}
	$animation_delay = $animation_delay . 's';

	$html = '';
	$img_html = '';

	if ( $enable_content_img == 'yes' ) {
		// Banner image size (background)
		$img_size_x = 263;
		$img_size_y = 188;
		if ( trim( $img_size ) != '' ) {
			$img_size = explode( 'x', $img_size );
		}
		$img_size_x = isset( $img_size[0] ) ? max( 0, intval( $img_size[0] ) ) : $img_size_x;
		$img_size_y = isset( $img_size[1] ) ? max( 0, intval( $img_size[1] ) ) : $img_size_y;

		// Banner image (background)
		$img = array(
			'url'    => zmb_no_image( array( 'width' => $img_size_x, 'height' => $img_size_y ), false, true ),
			'width'  => $img_size_x,
			'height' => $img_size_y,
		);

		if ( intval( $img_id ) > 0 ) {
			$img = zmb_resize_image( $img_id, null, $img_size_x, $img_size_y, true, true, false );
		}

		$img_html .= '<div class="content-img">';
		$img_html .= '<img src="' . esc_url( $img['url'] ) . '" width="' . esc_attr( $img['width'] ) . '" height="' . esc_attr( $img['height'] ) . '" alt="" />';
		$img_html .= '</div><!-- /.content-img -->';

	}

	$content = function_exists( 'wpb_js_remove_wpautop' ) ? wpb_js_remove_wpautop( $content, true ) : $content;

	$html .= '<div class="' . esc_attr( $css_class ) . '" data-wow-delay="' . esc_attr( $animation_delay ) . '">';
	$html .= '<div class="content-inner">';
	$html .= $img_html;
	$html .= '<div class="content-detail">';
	if ( trim( $enable_do_shortcode ) == 'yes' ) {
		$html .= $content;
	}
	else {
		$html .= $content;
	}
	$html .= '</div><!-- /.content-detail -->';
	$html .= '</div><!-- /.content-inner -->';
	$html .= '</div><!-- /.' . esc_attr( $css_class ) . ' -->';

	return $html;

}

add_shortcode( 'zmb_content', 'zmb_content' );
