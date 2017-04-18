<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}


add_action( 'vc_before_init', 'zmbSimpleTitleDesc' );
function zmbSimpleTitleDesc() {
	global $zan_vc_anim_effects_in;

	$params = array(
		array(
			'type'       => 'dropdown',
			'class'      => '',
			'heading'    => esc_html__( 'Style', 'zanmb' ),
			'param_name' => 'style',
			'value'      => array(
				esc_html__( 'Style 1', 'zanmb' )               => 'style_1',
				esc_html__( 'Style 2', 'zanmb' )               => 'style_2',
				esc_html__( 'Style 3 (Icon Big)', 'zanmb' )    => 'style_3',
				esc_html__( 'Style 4 (Icon Border)', 'zanmb' ) => 'style_4',
			),
			'std'        => 'style_1',
		),
		array(
			'type'       => 'textfield',
			'holder'     => 'div',
			'class'      => '',
			'heading'    => esc_html__( 'Title', 'zanmb' ),
			'param_name' => 'title',
			'std'        => esc_html__( 'Menu builder for Visual Composer', 'zanmb' ),
		),
		array(
			'type'       => 'textfield',
			'holder'     => 'div',
			'class'      => '',
			'heading'    => esc_html__( 'Short Description', 'zanmb' ),
			'param_name' => 'short_desc',
			'std'        => '',
		),
		array(
			'type'       => 'vc_link',
			'holder'     => 'div',
			'class'      => '',
			'heading'    => esc_html__( 'Link', 'zanmb' ),
			'param_name' => 'link',
		),
		array(
			'type'       => 'dropdown',
			'class'      => '',
			'heading'    => esc_html__( 'Title Tag', 'zanmb' ),
			'param_name' => 'title_tag',
			'value'      => array(
				esc_html__( 'h1', 'zanmb' )    => 'h1',
				esc_html__( 'h2', 'zanmb' )    => 'h2',
				esc_html__( 'h3', 'zanmb' )    => 'h3',
				esc_html__( 'h4', 'zanmb' )    => 'h4',
				esc_html__( 'h5', 'zanmb' )    => 'h5',
				esc_html__( 'h6', 'zanmb' )    => 'h6',
				esc_html__( 'span', 'zanmb' )  => 'span',
				esc_html__( 'label', 'zanmb' ) => 'label',
				esc_html__( 'p', 'zanmb' )     => 'p',
			),
			'std'        => 'h3'
		),
		array(
			'type'       => 'colorpicker',
			'holder'     => 'div',
			'class'      => '',
			'heading'    => esc_html__( 'Title Color', 'zanmb' ),
			'param_name' => 'title_color',
			'std'        => '#000',
		),
		array(
			'type'       => 'colorpicker',
			'holder'     => 'div',
			'class'      => '',
			'heading'    => esc_html__( 'Short Description Color', 'zanmb' ),
			'param_name' => 'short_desc_color',
			'std'        => '#7c7c7c',
		),
	);

	$icon_params = zmb_iconpicker_build_param();
	if ( !empty( $icon_params ) ) {
		$params = array_merge( $params, $icon_params );
	}

	$params = array_merge(
		$params,
		array(
			array(
				'type'       => 'colorpicker',
				'holder'     => 'div',
				'class'      => '',
				'heading'    => esc_html__( 'Icon Color', 'luckyshop-core' ),
				'param_name' => 'icon_color',
				'std'        => '#7c7c7c',
			),
			array(
				'type'       => 'dropdown',
				'holder'     => 'div',
				'class'      => '',
				'heading'    => esc_html__( 'Text Align', 'zanmb' ),
				'param_name' => 'text_align',
				'value'      => array(
					esc_html__( 'Left', 'zanmb' )    => 'left',
					esc_html__( 'Right', 'zanmb' )   => 'right',
					esc_html__( 'Center', 'zanmb' )  => 'center',
					esc_html__( 'Inherit', 'zanmb' ) => 'inherit'
				),
				'std'        => 'left',
			),
			array(
				'type'        => 'textfield',
				'holder'      => 'div',
				'class'       => '',
				'heading'     => esc_html__( 'Additional Class', 'zanmb' ),
				'param_name'  => 'additional_class',
				'std'         => '',
				'description' => esc_html__( 'Ex: address.', 'zanmb' ),
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
		)
	);

	vc_map(
		array(
			'name'     => esc_html__( 'Zan Simple Title With Description', 'zanmb' ),
			'base'     => 'zmb_simple_title_desc', // shortcode
			'class'    => '',
			'category' => esc_html__( 'Zan', 'zanmb' ),
			'params'   => $params
		)
	);
}

function zmb_simple_title_desc( $atts ) {

	$atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'zmb_simple_title_desc', $atts ) : $atts;

	extract(
		shortcode_atts(
			array(
				'style'              => 'style_1',
				'title'              => '',
				'short_desc'         => '',
				'link'               => '',
				'title_tag'          => 'h3',
				'title_color'        => '#000',
				'short_desc_color'   => '#7c7c7c',
				'icon_type'          => 'zansimpleline',
				'icon_fontawesome'   => '',
				'icon_openiconic'    => '',
				'icon_typicons'      => '',
				'icon_entypo'        => '',
				'icon_linecons'      => '',
				'icon_monosocial'    => '',
				'icon_zansimpleline' => '',
				'icon_color'         => '',
				'text_align'         => 'left',
				'additional_class'   => '',
				'css_animation'      => '',
				'animation_delay'    => '0.4',   //In second
				'css'                => '',
			), $atts
		)
	);

	$css_class = 'zmb-simple-title-wrap zan-simple-title-wrap wow ' . $css_animation . ' text-' . $text_align . ' simple-title-' . $style . ' ' . $additional_class;
	if ( function_exists( 'vc_shortcode_custom_css_class' ) ):
		$css_class .= ' ' . apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), '', $atts );
	endif;

	$link_default = array(
		'url'    => '',
		'title'  => '',
		'target' => ''
	);

	if ( function_exists( 'vc_build_link' ) ):
		$link = vc_build_link( $link );
	else:
		$link = $link_default;
	endif;

	if ( !is_numeric( $animation_delay ) ) {
		$animation_delay = 0;
	}
	$animation_delay = $animation_delay . 's';

	$allowed_title_tags = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'label', 'span', 'p' );
	if ( !in_array( $title_tag, $allowed_title_tags ) ) {
		$title_tag = 'h3';
	}

	$icon_class_var = 'icon_' . esc_attr( $icon_type );
	$icon_class = $$icon_class_var;

	$html = '';

	//	ob_start();
	//	echo "<pre>";
	//	print_r( $atts );
	//	echo "</pre>";
	//	$html .= ob_get_clean();
	//
	//	return $html;

	$title_html = '';
	$short_desc_html = '';
	$icon_html = '';

	$title_style = trim( $title_color ) != '' ? 'style="color: ' . esc_attr( $title_color ) . ';"' : '';
	$title_class = 'simple-title';

	if ( trim( $link['url'] ) != '' ) {
		$title_class .= ' has-link';
		$title_html .= '<' . $title_tag . ' ' . $title_style . ' class="' . esc_attr( $title_class ) . '" ><a href="' . esc_url( $link['url'] ) . '" target="' . esc_attr( $link['target'] ) . '" title="' . esc_attr( $link['title'] ) . '">' . sanitize_text_field( $title ) . '</a></' . $title_tag . '>';
	}
	else {
		$title_class .= ' no-link';
		$title_html .= '<' . $title_tag . ' ' . $title_style . ' class="' . esc_attr( $title_class ) . '" >' . sanitize_text_field( $title ) . '</' . $title_tag . '>';
	}

	if ( trim( $short_desc ) != '' ) {
		$short_desc_html .= '<span class="short-desc">' . esc_html( $short_desc ) . '</span>';
	}

	if ( trim( $icon_class ) != '' ) {
		$css_class .= ' has-icon';
		$icon_style = trim( $icon_color ) != '' ? 'color: ' . esc_attr( $icon_color ) . ';' : '';
		if ( trim( $icon_style ) != '' ) {
			$icon_style = 'style="' . $icon_style . '"';
		}
		$icon_html = '<div class="icon-wrap" ' . $icon_style . '>
                        <span class="' . esc_attr( $icon_class ) . '"></span>
                     </div>';
	}

	$html .= '<div class="' . esc_attr( $css_class ) . '" data-wow-delay="' . esc_attr( $animation_delay ) . '">
				<div class="simple-title-inner">
					' . $icon_html . '
					<div class="text-wrap">
					' . $title_html . '
					' . $short_desc_html . '
					</div><!-- /.text-wrap -->
				</div><!-- /.simple-title-inner -->
			</div>';

	return $html;

}

add_shortcode( 'zmb_simple_title_desc', 'zmb_simple_title_desc' );
