<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}


add_action( 'vc_before_init', 'zmbNewsLetter' );
function zmbNewsLetter() {
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
			'name'     => esc_html__( 'Zan Newsletter', 'zanmb' ),
			'base'     => 'zmb_news_letter', // shortcode
			'class'    => '',
			'category' => esc_html__( 'Zan', 'zanmb' ),
			'params'   => array(
				array(
					'type'       => 'textfield',
					'holder'     => 'div',
					'class'      => '',
					'heading'    => esc_html__( 'Title', 'zanmb' ),
					'param_name' => 'title',
					'std'        => esc_html__( 'Join Our Newsletter', 'zanmb' ),
				),
				array(
					'type'       => 'textfield',
					'holder'     => 'div',
					'class'      => '',
					'heading'    => esc_html__( 'Short Description', 'zanmb' ),
					'param_name' => 'short_desc',
					'std'        => esc_html__( 'Sign up our newsletter and get more events &amp; promotions!', 'zanmb' ),
				),
				array(
					'type'       => 'textfield',
					'holder'     => 'div',
					'class'      => '',
					'heading'    => esc_html__( 'Submit Text', 'zanmb' ),
					'param_name' => 'submit_text',
				),
				array(
					'type'       => 'textfield',
					'holder'     => 'div',
					'class'      => '',
					'heading'    => esc_html__( 'Placeholder Text', 'zanmb' ),
					'param_name' => 'placeholder',
					'std'        => esc_html__( 'Enter your email here', 'zanmb' ),
				),
				array(
					'type'       => 'textfield',
					'holder'     => 'div',
					'class'      => '',
					'heading'    => esc_html__( 'Success Message', 'zanmb' ),
					'param_name' => 'success_message',
					'std'        => esc_html__( 'Your email added...', 'zanmb' ),
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
				array(
					'type'       => 'colorpicker',
					'holder'     => 'div',
					'class'      => '',
					'heading'    => esc_html__( 'Input Text Color', 'zanmb' ),
					'param_name' => 'input_text_color',
					'std'        => '#7c7c7c',
				),
				array(
					'type'       => 'colorpicker',
					'holder'     => 'div',
					'class'      => '',
					'heading'    => esc_html__( 'Input Background Color', 'zanmb' ),
					'param_name' => 'input_bg_color',
					'std'        => '#efefef',
				),
				array(
					'type'       => 'colorpicker',
					'holder'     => 'div',
					'class'      => '',
					'heading'    => esc_html__( 'Submit Button Color', 'zanmb' ),
					'param_name' => 'submit_btn_color',
					'std'        => '#fff',
				),
				array(
					'type'       => 'dropdown',
					'class'      => '',
					'heading'    => esc_html__( 'Text Align', 'zanmb' ),
					'param_name' => 'text_align',
					'value'      => array(
						esc_html__( 'Left', 'zanmb' )    => 'left',
						esc_html__( 'Right', 'zanmb' )   => 'right',
						esc_html__( 'Center', 'zanmb' )  => 'center',
						esc_html__( 'Inherit', 'zanmb' ) => 'inherit',
					),
					'std'        => 'left',
				),
				array(
					'type'        => 'textfield',
					'holder'      => 'div',
					'class'       => '',
					'heading'     => esc_html__( 'Mailchimp API Key', 'zanmb' ),
					'param_name'  => 'api_key',
					'description' => wp_kses( sprintf( __( '<a href="%s" target="__blank">Click here to get your Mailchimp API key</a>', 'zanmb' ), 'https://admin.mailchimp.com/account/api' ), $allowed_tags ),
					'group'       => esc_html__( 'Mailchimp Settings', 'zanmb' ),
				),
				array(
					'type'        => 'textfield',
					'holder'      => 'div',
					'class'       => '',
					'heading'     => esc_html__( 'Mailchimp List ID', 'zanmb' ),
					'param_name'  => 'list_id',
					'description' => wp_kses( sprintf( __( '<a href="%s" target="__blank">How to find Mailchimp list ID</a>', 'zanmb' ), 'http://kb.mailchimp.com/lists/managing-subscribers/find-your-list-id' ), $allowed_tags ),
					'group'       => esc_html__( 'Mailchimp Settings', 'zanmb' ),
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
		)
	);
}

function zmb_news_letter( $atts ) {

	$atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'zmb_news_letter', $atts ) : $atts;

	extract(
		shortcode_atts(
			array(
				'title'            => '',
				'short_desc'       => '',
				'submit_text'      => '',
				'placeholder'      => '',
				'success_message'  => '',
				'title_color'      => '#fff',
				'short_desc_color' => '#fff',
				'input_text_color' => '#fff',
				'input_bg_color'   => 'rgba(255,255,255,0.2)',
				'submit_btn_color' => '#fff',
				'text_align'       => 'left',
				'api_key'          => '',
				'list_id'          => '',
				'css_animation'    => '',
				'animation_delay'  => '0.4',   //In second
				'css'              => '',
			), $atts
		)
	);

	$css_class = 'zmb-newsleter-wrap wow ' . $css_animation;
	if ( function_exists( 'vc_shortcode_custom_css_class' ) ):
		$css_class .= ' ' . apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), '', $atts );
	endif;

	if ( !is_numeric( $animation_delay ) ) {
		$animation_delay = 0;
	}
	$animation_delay = $animation_delay . 's';

	$action_url = ZMB_BASE_URL . 'core/shortcodes/zmb_news_letter.php';

	$html = '';
	$title_html = '';
	$short_desc_html = '';

	if ( trim( $title ) != '' ) {
		$title_html .= '<h4 class="news-letter-title" style="color: ' . esc_attr( $title_color ) . ';">' . sanitize_text_field( $title ) . '</h4>';
	}
	if ( trim( $short_desc ) != '' ) {
		$short_desc_html .= '<p class="short-desc" style="color: ' . esc_attr( $short_desc_color ) . ';" >' . sanitize_text_field( $short_desc ) . '</p>';
	}

	$html .= '<div class="' . esc_attr( $css_class ) . '" data-wow-delay="' . esc_attr( $animation_delay ) . '">
				<div class="zmb-newsletter zan-newsletter text-' . esc_attr( $text_align ) . '">
					' . $title_html . '
					' . $short_desc_html . '
					<div class="newsletter-form-wrap">
						<form action="' . esc_url( $action_url ) . '" name="zmb_news_letter" class="form-newsletter">
	                        <input type="hidden" name="api_key" value="' . esc_html( $api_key ) . '" />
	                        <input type="hidden" name="list_id" value="' . esc_html( $list_id ) . '" />
	                        <input type="hidden" name="success_message" value="' . sanitize_text_field( $success_message ) . '" />
							<input type="text" name="email" placeholder="' . sanitize_text_field( $placeholder ) . '" style="color: ' . esc_attr( $input_text_color ) . '; background-color: ' . esc_attr( $input_bg_color ) . ';" >
							<button type="submit" name="submit_button" class="zmb-btn submit-newsletter" style="color: ' . esc_attr( $submit_btn_color ) . ';"><i class="fa fa-envelope-o"></i><span class="submit-text">' . sanitize_text_field( $submit_text ) . '</span></button>
						</form>
					</div><!-- /.newsletter-form-wrap -->
				</div><!-- /.zmb-newsletter -->
			</div><!-- /.' . esc_attr( $css_class ) . ' -->';

	return $html;

}

add_shortcode( 'zmb_news_letter', 'zmb_news_letter' );

function zmb_core_submit_mailchimp_via_ajax() {

	if ( !class_exists( 'MCAPI' ) ) {
		zmb_require_once( ZMB_CLASSES . 'mailchimp/MCAPI.class.php' );
	}

	$response = array(
		'html'    => '',
		'message' => '',
		'success' => 'no'
	);

	$api_key = isset( $_POST['api_key'] ) ? $_POST['api_key'] : '';
	$list_id = isset( $_POST['list_id'] ) ? $_POST['list_id'] : '';
	$success_message = isset( $_POST['success_message'] ) ? $_POST['success_message'] : '';
	$email = isset( $_POST['email'] ) ? $_POST['email'] : '';

	$response['message'] = esc_html__( 'Failed', 'zanmb' );

	$merge_vars = array();

	if ( class_exists( 'MCAPI' ) ) {
		$api = new MCAPI( $api_key );
		if ( $api->listSubscribe( $list_id, $email, $merge_vars ) === true ) {
			$response['message'] = sanitize_text_field( $success_message );
			$response['success'] = 'yes';
		}
		else {
			// Sending failed
			$response['message'] = $api->errorMessage;
		}
	}

	wp_send_json( $response );
	die();
}

add_action( 'wp_ajax_zmb_core_submit_mailchimp_via_ajax', 'zmb_core_submit_mailchimp_via_ajax' );
add_action( 'wp_ajax_nopriv_zmb_core_submit_mailchimp_via_ajax', 'zmb_core_submit_mailchimp_via_ajax' );


if ( isset( $_POST['news_letter'] ) ) {

	if ( !class_exists( 'MCAPI' ) ) {
		zmb_require_once( ZMB_CLASSES . 'mailchimp/MCAPI.class.php' );
	}

	$api_key = isset( $_POST['api_key'] ) ? $_POST['api_key'] : '';
	$list_id = isset( $_POST['list_id'] ) ? $_POST['list_id'] : '';
	$success_message = isset( $_POST['success_message'] ) ? $_POST['success_message'] : esc_html__( 'Failed', 'zanmb' );
	$email = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
	//$merge_vars      = array( 'FIRSTNAME' => $fname, 'LASTNAME' => $lname );
	$merge_vars = array();

	if ( trim( $api_key ) != '' && trim( $list_id ) != '' && is_email( $email ) && class_exists( 'MCAPI' ) ) {
		$api = new MCAPI( $api_key );
		if ( $api->listSubscribe( $list_id, $email, $merge_vars ) === true ) {
			//echo  '<div class="mailchip-success">' . $success_message . '</div>';
		}
		else {
			//echo  '<div class="mailchip-success">' . $api->errorMessage . '</div>';
		}
	}
}


