<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'vc_before_init', 'lkSinglePost' );
function lkSinglePost() {
	global $zan_vc_anim_effects_in;

	$params = array(
		array(
			'type'        => 'autocomplete',
			'holder'      => '',
			'class'       => '',
			'heading'     => esc_html__( 'Post', 'zanmb' ),
			'param_name'  => 'post_id',
			'std'         => '',
			'description' => esc_html__( 'Input post ID or post title to see suggestions.', 'zanmb' ),
		),
		array(
			'type'        => 'textfield',
			'holder'      => 'div',
			'class'       => '',
			'heading'     => esc_html__( 'Thumbnail Size', 'zanmb' ),
			'param_name'  => 'img_size',
			'std'         => '262x96',
			'description' => wp_kses( __( 'Format {width}x{height}. Default <strong>262x96</strong>.', 'zanmb' ), array( 'strong' => array(), 'b' => array() ) ),
		),
		array(
			'type'        => 'textfield',
			'holder'      => 'div',
			'class'       => '',
			'heading'     => esc_html__( 'Excerpt Max Words', 'zanmb' ),
			'param_name'  => 'excerpt_max_words',
			'std'         => 10,
			'description' => esc_html__( 'Maximum number of post excerpt words', 'zanmb' ),
		),
		array(
			'type'       => 'textfield',
			'holder'     => 'div',
			'class'      => '',
			'heading'    => esc_html__( 'Read More Text', 'zanmb' ),
			'param_name' => 'read_more_text',
			'std'        => esc_html__( 'Read more...', 'zanmb' ),
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
				'heading'    => esc_html__( 'Title Color', 'luckyshop-core' ),
				'param_name' => 'title_color',
				'std'        => '#000',
			),
			array(
				'type'       => 'colorpicker',
				'holder'     => 'div',
				'class'      => '',
				'heading'    => esc_html__( 'Text Color', 'luckyshop-core' ),
				'param_name' => 'text_color',
				'std'        => '#7c7c7c',
			),
			array(
				'type'       => 'colorpicker',
				'holder'     => 'div',
				'class'      => '',
				'heading'    => esc_html__( 'Read More Color', 'luckyshop-core' ),
				'param_name' => 'read_more_color',
				'std'        => '#fff',
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
			'name'     => esc_html__( 'Zan Single Post', 'zanmb' ),
			'base'     => 'zmb_post_single', // shortcode
			'class'    => '',
			'category' => esc_html__( 'Zan', 'zanmb' ),
			'params'   => $params
		)
	);
}

// vc_autocomplete_{short_code_name}_{param_name}_callback
add_filter( 'vc_autocomplete_zmb_post_single_post_id_callback', 'zmb_vc_include_field_post_search', 10, 1 ); // Get suggestion(find). Must return an array
add_filter( 'vc_autocomplete_zmb_post_single_post_id_render', 'zmb_vc_include_field_render', 10, 1 ); // Render exact post. Must return an array (label,value)

function zmb_post_single( $atts ) {

	$atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'zmb_post_single', $atts ) : $atts;

	extract(
		shortcode_atts(
			array(
				'post_id'            => 0,
				'img_size'           => '262x96',
				'excerpt_max_words'  => 10,
				'read_more_text'     => '',
				'icon_type'          => 'zansimpleline',
				'icon_fontawesome'   => '',
				'icon_openiconic'    => '',
				'icon_typicons'      => '',
				'icon_entypo'        => '',
				'icon_linecons'      => '',
				'icon_monosocial'    => '',
				'icon_zansimpleline' => '',
				'title_color'        => '#000',
				'text_color'         => '#7c7c7c',
				'read_more_color'    => '#000',
				'css_animation'      => '',
				'animation_delay'    => '0.4',   //In second
				'css'                => '',
			), $atts
		)
	);

	$css_class = 'zmb-single-post-wrap zan-single-post-wrap ' . $css_animation;
	if ( trim( $css_animation ) != '' ) {
		$css_class .= ' wow';
	}

	if ( function_exists( 'vc_shortcode_custom_css_class' ) ):
		$css_class .= ' ' . apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), '', $atts );
	endif;

	$icon_class_var = 'icon_' . esc_attr( $icon_type );
	$icon_class = $$icon_class_var;

	$html = '';
	$img_html = '';
	$icon_html = '';
	$title_html = '';
	$excerpt_html = '';
	$read_more_html = '';

	$post_id = intval( $post_id );
	if ( $post_id > 0 ) {
		$post = get_post( $post_id );

		if ( $post ) {

			if ( trim( $icon_class ) != '' ) {
				$css_class .= ' has-icon';
				$icon_html = '<div class="icon-wrap">
		                        <span class="' . esc_attr( $icon_class ) . '"></span>
		                     </div>';
			}

			$img_size_x = 262;
			$img_size_y = 96;
			if ( trim( $img_size ) != '' ) {
				$img_size = explode( 'x', $img_size );
			}
			$img_size_x = isset( $img_size[0] ) ? max( 1, intval( $img_size[0] ) ) : $img_size_x;
			$img_size_y = isset( $img_size[1] ) ? max( 1, intval( $img_size[1] ) ) : $img_size_y;

			$img = zmb_resize_image( get_post_thumbnail_id( $post->ID ), null, $img_size_x, $img_size_y, true, true, false );

			$img_html .= '<figure>
							<img width="' . esc_attr( $img['width'] ) . '"
							     height="' . esc_attr( $img['height'] ) . '"
							     src="' . esc_url( $img['url'] ) . '" alt="' . esc_attr( get_the_title() ) . '"/>
					        ' . $icon_html . '
						</figure>';
			$title_html = '<h5 class="zmb-small-title" style="color: ' . esc_attr( $title_color ) . ';"><a href="' . esc_url( get_permalink( $post->ID ) ) . '">' . esc_html( $post->post_title ) . '</a></h5>';

			$excerpt_html .= '<div class="post-excerpt-wrap" style="color:' . esc_attr( $text_color ) . ';">
								' . zmb_trim_excerpt( $post->post_content, $excerpt_max_words ) . '
							</div>';

			if ( trim( $read_more_text ) != '' ) {
				$read_more_html .= '<a href="' . esc_url( get_permalink( $post->ID ) ) . '" style="color:' . esc_attr( $read_more_color ) . ';" class="zmb-read-more">' . sanitize_text_field( $read_more_text ) . '</a>';
			}

		}

	}

	$html .= '<div class="' . esc_attr( $css_class ) . '" data-wow-delay="' . esc_attr( $animation_delay ) . '">';
	$html .= '<div class="zmb-single-post-inner">';
	$html .= $img_html;
	$html .= $title_html;
	$html .= $excerpt_html;
	$html .= $read_more_html;
	$html .= '</div><!-- /.zmb-single-post-inner -->';
	$html .= '</div>';

	return $html;

}

add_shortcode( 'zmb_post_single', 'zmb_post_single' );
