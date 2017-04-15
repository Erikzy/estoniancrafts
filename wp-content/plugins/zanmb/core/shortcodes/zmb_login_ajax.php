<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}


add_action( 'vc_before_init', 'zmbLoginAjax' );
function zmbLoginAjax() {
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
			'name'     => esc_html__( 'Zan Login Ajax', 'zanmb' ),
			'base'     => 'zmb_login_ajax', // shortcode
			'class'    => '',
			'category' => esc_html__( 'Zan', 'zanmb' ),
			'params'   => array(
				array(
					'type'        => 'textfield',
					'holder'      => 'div',
					'class'       => '',
					'heading'     => esc_html__( 'Welcome Text', 'zanmb' ),
					'param_name'  => 'welcome_text',
					'std'         => esc_html__( 'Welcome {user_name}' ),
					'description' => wp_kses( __( 'The text display if user is logged in. {user_name} will be replced with user display name.', 'zanmb' ), array( 'strong' => array(), 'b' => array() ) ),
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

function zmb_login_ajax( $atts ) {

	$atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'zmb_login_ajax', $atts ) : $atts;

	extract(
		shortcode_atts(
			array(
				'welcome_text' => '',
				'css'          => '',
			), $atts
		)
	);

	$css_class = 'zmb-login-ajax-wrap';
	if ( function_exists( 'vc_shortcode_custom_css_class' ) ):
		$css_class .= ' ' . apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), '', $atts );
	endif;

	$html = '';

	if ( is_user_logged_in() ) {

		if ( trim( $welcome_text ) != '' ) {
			global $current_user;
			$css_class .= ' is-user-logged-in';
			$welcome_text = str_replace( '{user_name}', '<a href="' . esc_url( get_edit_user_link( $current_user->ID ) ) . '">' . $current_user->display_name . '</a>', esc_html( $welcome_text ) );
			$html .= '<h3 class="zmb-welcome-user-title">' . $welcome_text . '</h3>';
		}

	}
	else {
		$html = zmb_custom_login();
	}

	if ( $html != '' ) {
		$html = '<div class="' . $css_class . '">' . $html . '</div>';
	}

	return $html;

}

add_shortcode( 'zmb_login_ajax', 'zmb_login_ajax' );


if ( !function_exists( 'zmb_custom_login' ) ) {

	/**
	 * Custom login inherit from wp_login_form
	 **/

	/**
	 * Provides a simple login form for use anywhere within WordPress. By default, it echoes
	 * the HTML immediately. Pass array('echo'=>false) to return the string instead.
	 *
	 * @since 3.0.0
	 *
	 * @param array $args Configuration options to modify the form output.
	 *
	 * @return string|null String when retrieving, null when displaying.
	 */
	function zmb_custom_login( $args = array() ) {
		$defaults = array(
			'echo'                => false,
			'redirect'            => ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], // Default redirect is back to the current page
			'form_id'             => 'zmb_login_form',
			'label_username'      => esc_html__( 'Username', 'zanmb' ),
			'label_password'      => esc_html__( 'Password', 'zanmb' ),
			'label_remember'      => esc_html__( 'Remember Me', 'zanmb' ),
			'label_log_in'        => esc_html__( 'Log In', 'zanmb' ),
			'id_username'         => 'user_login',
			'id_password'         => 'user_pass',
			'id_remember'         => 'rememberme',
			'id_submit'           => 'wp-submit',
			'remember'            => true,
			'value_username'      => '',
			'value_remember'      => false, // Set this to true to default the "Remember me" checkbox to checked
			'lost_pass_link'      => ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], // Default redirect is back to the current page
			'show_lost_pass_link' => true,
			'show_register_link'  => true,
		);

		/**
		 * Filter the default login form output arguments.
		 *
		 * @since 3.0.0
		 *
		 * @see   wp_login_form()
		 *
		 * @param array $defaults An array of default login form arguments.
		 */
		$args = wp_parse_args( $args, apply_filters( 'login_form_defaults', $defaults ) );

		/**
		 * Filter content to display at the top of the login form.
		 *
		 * The filter evaluates just following the opening form tag element.
		 *
		 * @since 3.0.0
		 *
		 * @param string $content Content to display. Default empty.
		 * @param array  $args    Array of login form arguments.
		 */
		$login_form_top = apply_filters( 'login_form_top', '', $args );

		/**
		 * Filter content to display in the middle of the login form.
		 *
		 * The filter evaluates just following the location where the 'login-password'
		 * field is displayed.
		 *
		 * @since 3.0.0
		 *
		 * @param string $content Content to display. Default empty.
		 * @param array  $args    Array of login form arguments.
		 */
		$login_form_middle = apply_filters( 'login_form_middle', '', $args );

		/**
		 * Filter content to display at the bottom of the login form.
		 *
		 * The filter evaluates just preceding the closing form tag element.
		 *
		 * @since 3.0.0
		 *
		 * @param string $content Content to display. Default empty.
		 * @param array  $args    Array of login form arguments.
		 */
		$login_form_bottom = apply_filters( 'login_form_bottom', '', $args );

		$lost_pass_link = '';
		if ( $args['show_lost_pass_link'] === true ) {
			$lost_pass_link = '<a class="lost-pass-link" href="' . esc_url( wp_lostpassword_url( get_permalink() ) ) . '" title="' . esc_html__( 'Forgot Your Password', 'zanmb' ) . '">' . esc_html__( 'Forgot Your Password', 'zanmb' ) . '</a>';
		}

		$register_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$register_url = esc_url( add_query_arg( array( 'action' => 'register' ), $register_url ) );

		$form = '
    		<form name="' . esc_attr( $args['form_id'] ) . '" id="' . esc_attr( $args['form_id'] ) . '" class="zmb-login-form login-form" action="' . esc_url( site_url( 'wp-login.php', 'login_post' ) ) . '" method="post">
    			' . $login_form_top . '
    			<div class="login-username form-group">
    				<label for="' . esc_attr( $args['id_username'] ) . '" class="lb-user-login">' . esc_html( $args['label_username'] ) . '</label>
    				<input type="text" name="log" id="' . esc_attr( $args['id_username'] ) . '" class="input form-control" value="' . esc_attr( $args['value_username'] ) . '" size="20" />
    			</div><!-- /.login-username -->
    			<div class="login-password form-group">
    				<label for="' . esc_attr( $args['id_password'] ) . '" class="lb-user-pw">' . esc_html( $args['label_password'] ) . '</label>
    				<input type="password" name="pwd" id="' . esc_attr( $args['id_password'] ) . '" class="input form-control" value="" size="20" />
    			</div><!-- /.login-password -->
                <div class="login-submit form-group">
                    <button type="submit">' . sanitize_text_field( $args['label_log_in'] ) . '</button>
    				<input type="hidden" name="redirect_to" value="' . esc_url( $args['redirect'] ) . '" />
    			</div><!-- /.login-submit -->
    			' . $login_form_middle . '
                <div class="bottom-login">
    			' . ( $args['remember'] ? '<div class="checkbox-remember"><label class="lb-remember"><input name="rememberme" type="checkbox" id="' . esc_attr( $args['id_remember'] ) . '" value="forever"' . ( $args['value_remember'] ? ' checked="checked"' : '' ) . ' /> ' . esc_html( $args['label_remember'] ) . '</label></div>' : '' ) . '
                ' . $lost_pass_link . '
                ' . wp_nonce_field( 'ajax-login-nonce', 'login-ajax-nonce', true, false ) . '
                </div><!-- /.bottom-login -->
    			' . $login_form_bottom . '
    		</form>';

		if ( $args['echo'] ) {
			echo wptexturize( $form );
		}
		else {
			return wptexturize( $form );
		}

	}

}

/**
 * Do login via ajax
 **/
function zmb_do_login_via_ajax() {
	global $current_user;

	$response = array(
		'html'         => '',
		'is_logged_in' => is_user_logged_in() ? 'yes' : 'no',
		'message'      => '',
	);

	if ( $response['is_logged_in'] == 'yes' ) {
		$response['message'] = '<p class="text-primary bg-primary login-message">' . esc_html__( 'You are logged in!', 'zanmb' ) . '</p>';
		wp_send_json( $response );
		die();
	}

	$user_login = isset( $_POST['user_login'] ) ? $_POST['user_login'] : '';
	$user_pass = isset( $_POST['user_pass'] ) ? $_POST['user_pass'] : '';
	$rememberme = isset( $_POST['rememberme'] ) ? $_POST['rememberme'] == 'yes' : false;
	//$redirect_to = isset( $_POST['redirect_to'] ) ? esc_url( $_POST['redirect_to'] ) : '';
	$login_nonce = isset( $_POST['login_nonce'] ) ? $_POST['login_nonce'] : '';

	if ( !wp_verify_nonce( $login_nonce, 'ajax-login-nonce' ) ) {
		$response['message'] = '<p class="text-danger bg-danger login-message">' . esc_html__( 'Security check error!', 'zanmb' ) . '</p>';
		wp_send_json( $response );
		die();
	}

	if ( trim( $user_login ) == '' ) {
		$response['message'] = '<p class="text-danger bg-danger login-message">' . esc_html__( 'User name can not be empty!', 'zanmb' ) . '</p>';
		wp_send_json( $response );
		die();
	}

	$info = array();
	$info['user_login'] = $user_login;
	$info['user_password'] = $user_pass;
	$info['remember'] = $rememberme;

	$user_signon = wp_signon( $info, false );

	if ( is_wp_error( $user_signon ) ) {
		$response['message'] = '<p class="text-danger bg-danger login-message">' . esc_html__( 'Wrong username or password.', 'zanmb' ) . '</p>';
	}
	else {
		$response['is_logged_in'] = 'yes';
		$response['message'] = '<p class="text-success bg-success login-message">' . esc_html__( 'Logged in successfully', 'zanmb' ) . '</p>';
		$response['html'] = '<h3>' . esc_html__( 'Welcome', 'zanmb' ) . '</h3>
                            <p>' . sprintf( esc_html__( 'Hello %s!', 'zanmb' ), $current_user->display_name ) . '</p>';
	}

	wp_send_json( $response );

	die();
}

add_action( 'wp_ajax_nopriv_zmb_do_login_via_ajax', 'zmb_do_login_via_ajax' );
add_action( 'wp_ajax_zmb_do_login_via_ajax', 'zmb_do_login_via_ajax' );