<?php
/*
 * Plugin Name: WordPress Mega Menu
 * Plugin URI: http://zmb.zanthemes.net/
 * Description: Horizontal/Vertical menu builder for WordPress site. One of the best mega menu plugin integration with most of page builder plugins.
 * Author: Le Manh Linh
 * Version: 2.2
 * Author URI: http://zmb.zanthemes.net/
 * Text Domain: zanmb
 * Domain Path: languages
 */

if ( !defined( 'ABSPATH' ) ) {
	exit; // disable direct access
}

define( 'ZMB_VERSION', '2.2' );
define( 'ZMB_BASE_URL', trailingslashit( plugins_url( 'zanmb' ) ) );
define( 'ZMB_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'ZMB_CLASSES', ZMB_DIR_PATH . '/classes/' );
define( 'ZMB_CLASSES_URL', ZMB_BASE_URL . '/classes/' );
define( 'ZMB_CORE', ZMB_DIR_PATH . '/core/' );
define( 'ZMB_VENDORS', ZMB_DIR_PATH . '/assets/vendors/' );
define( 'ZMB_VENDORS_URL', ZMB_BASE_URL . 'assets/vendors/' );
define( 'ZMB_CSS_URL', ZMB_BASE_URL . 'assets/css/' );
define( 'ZMB_SKINS_URL', ZMB_CSS_URL . 'menu-skins/' );
define( 'ZMB_JS_URL', ZMB_BASE_URL . 'assets/js/' );
define( 'ZMB_IMG_URL', ZMB_BASE_URL . 'assets/images/' );


/**
 * Load Redux Framework
 */
if ( !class_exists( 'ReduxFramework' ) && file_exists( ZMB_DIR_PATH . 'reduxframework/ReduxCore/framework.php' ) ) {
	require_once( ZMB_DIR_PATH . 'reduxframework/ReduxCore/framework.php' );
}

/**
 * Load plugin textdomain
 */
if ( !function_exists( 'zmb_load_textdomain' ) ) {
	function zmb_load_textdomain() {
		load_plugin_textdomain( 'zanmb', false, ZMB_DIR_PATH . 'languages' );
	}

	add_action( 'plugins_loaded', 'zmb_load_textdomain' );
}

function zmb_add_admin_caps() {
	// gets the administrator role
	$role = get_role( 'administrator' );

	if ( class_exists( 'Vc_Manager' ) ) {
		if ( isset( $role->capabilities['vc_access_rules_post_types'] ) ) {
			if ( $role->capabilities['vc_access_rules_post_types'] !== 'custom' ) {
				$role->add_cap( 'vc_access_rules_post_types', 'custom' );
				$role->add_cap( 'vc_access_rules_post_types/zanmenu', true );
			}
		}
	}

	if ( class_exists( 'KingComposer' ) ) {
		global $kc;
		$kc->add_content_type( 'zanmenu' );
	}
}

add_action( 'init', 'zmb_add_admin_caps' );


/**
 * Require file
 **/
function zmb_require_once( $file_path ) {

	if ( file_exists( $file_path ) ) {
		require_once $file_path;
	}
}

if ( !function_exists( 'zmb_load_options' ) ) {
	function zmb_load_options() {
		zmb_require_once( ZMB_CORE . 'zanmb-options.php' );
	}
}
add_action( 'init', 'zmb_load_options', 99 );

if ( !function_exists( 'zmb_initialize_cmb_meta_boxes' ) ) {
	/**
	 * Initialize the metabox class.
	 */
	function zmb_initialize_cmb_meta_boxes() {

		if ( !class_exists( 'cmb_Meta_Box' ) ) {
			zmb_require_once( ZMB_CLASSES . '/metaboxes/init.php' );
		};
	}

	add_action( 'init', 'zmb_initialize_cmb_meta_boxes', 999 );
}

if ( !function_exists( 'zmb_core_cmb2_render_callback_for_spectrum_color_picker' ) ) {
	/**
	 *
	 * Field type is 'zmb_color_picker'
	 *
	 * @param $field                The current CMB2_Field object.
	 * @param $escaped_value        The value of this field passed through the escaping filter. It defaults to 'sanitize_text_field'. If you need the unescaped value, you can access it via $field_type_object->value()
	 * @param $object_id            The id of the object you are working with. Most commonly, the post id.
	 * @param $object_type          The type of object you are working with. Most commonly, post (this applies to all post-types), but could also be comment, user or options-page
	 * @param $field_type_object    This is an instance of the CMB2_Types object and gives you access to all of the methods that CMB2 uses to build its field types.
	 *
	 * @since  1.0
	 *
	 * @author Le Manh Linh
	 *
	 */
	function zmb_core_cmb2_render_callback_for_spectrum_color_picker(
		$field, $escaped_value, $object_id, $object_type, $field_type_object
	) {
		echo $field_type_object->input(
			array(
				'type'  => 'text',
				'class' => 'regular-text zmb-color-picker zan-color-picker spectrum-color-picker'
			)
		);
	}

	add_action( 'cmb2_render_zmb_color_picker', 'zmb_core_cmb2_render_callback_for_spectrum_color_picker', 10, 5 );
}

if ( !function_exists( 'zmb_core_cmb2_sanitize_zmb_color_picker_callback' ) ) {
	function zmb_core_cmb2_sanitize_zmb_color_picker_callback( $override_value, $value ) {
		$value = esc_attr( $value );

		return $value;
	}

	add_filter( 'cmb2_sanitize_zmb_color_picker', 'zmb_core_cmb2_sanitize_zmb_color_picker_callback', 10, 2 );
}

if ( !function_exists( 'zmb_fonts_url' ) ) {
	/**
	 * Register Google fonts for Twenty Fifteen.
	 *
	 * @since Lucky Shop 1.0
	 *
	 * @return string Google fonts URL for the theme.
	 */
	function zmb_fonts_url() {
		$fonts_url = '';
		$fonts = array();
		$subsets = 'latin,latin-ext';

		/*
		 * Translators: If there are characters in your language that are not supported
		 * by Open Sans, translate this to 'off'. Do not translate into your own language.
		 */
		if ( 'off' !== _x( 'on', 'Open Sans font: on or off', 'zanmb' ) ) {
			$fonts[] = 'Open Sans:400,400italic,700,700italic';
		}

		/*
		 * Translators: To add an additional character subset specific to your language,
		 * translate this to 'greek', 'cyrillic', 'devanagari' or 'vietnamese'. Do not translate into your own language.
		 */
		$subset = _x( 'no-subset', 'Add new subset (greek, cyrillic, devanagari, vietnamese)', 'zanmb' );

		if ( 'cyrillic' == $subset ) {
			$subsets .= ',cyrillic,cyrillic-ext';
		}
		elseif ( 'greek' == $subset ) {
			$subsets .= ',greek,greek-ext';
		}
		elseif ( 'devanagari' == $subset ) {
			$subsets .= ',devanagari';
		}
		elseif ( 'vietnamese' == $subset ) {
			$subsets .= ',vietnamese';
		}

		if ( $fonts ) {
			$fonts_url = add_query_arg(
				array(
					'family' => urlencode( implode( '|', $fonts ) ),
					'subset' => urlencode( $subsets ),
				), 'https://fonts.googleapis.com/css'
			);
		}

		return $fonts_url;
	}
};

function zmb_enqueue_script() {
	global $zanmb;

	// Add custom fonts, used in the main stylesheet.
	//wp_enqueue_style( 'zmb-fonts', zmb_fonts_url(), array(), null );

	$load_simple_line_font_icons = isset( $zanmb['load_simple_line_font_icons'] ) ? $zanmb['load_simple_line_font_icons'] == 1 : true;
	if ( $load_simple_line_font_icons ) {
		wp_register_style( 'simple-line-icons', ZMB_VENDORS_URL . 'simple-line-icons/simple-line-icons.css', false, ZMB_VERSION, 'all' );
		wp_enqueue_style( 'simple-line-icons' );
	}

	$load_awesome_font_icons = isset( $zanmb['load_awesome_font_icon'] ) ? $zanmb['load_awesome_font_icon'] == 1 : true;
	if ( $load_awesome_font_icons ) {
		wp_register_style( 'font-awesome.min', ZMB_VENDORS_URL . 'font-awesome/css/font-awesome.min.css', false, ZMB_VERSION, 'all' );
		wp_enqueue_style( 'font-awesome.min' );
	}

	$zmb_localize = array(
		'has_woocommerce'      => 'no',
		'mini_cart_html'       => '',
		'cart_count'           => 0,
		'cart_url'             => '#',
		'vertical_menu_info'   => array(),
		'sliding_btn_selector' => '',
		'show_sliding_on'      => ''
	);

	if ( isset( $zanmb['sliding_btn_target'] ) ) {
		$zmb_localize['sliding_btn_selector'] = trim( $zanmb['sliding_btn_target'] );
	}

	if ( isset( $zanmb['show_sliding_on'] ) ) {
		$zmb_localize['show_sliding_on'] = trim( $zanmb['show_sliding_on'] );
	}

	if ( class_exists( 'WooCommerce' ) ) {
		global $woocommerce;
		$zmb_localize['has_woocommerce'] = 'yes';
		//$zmb_localize['mini_cart_html']
		ob_start();
		//wc_get_template( 'cart/mini-cart.php' );
		woocommerce_mini_cart();
		$zmb_localize['mini_cart_html'] = ob_get_clean();

		$zmb_localize['cart_count'] = intval( WC()->cart->get_cart_contents_count() );
		$zmb_localize['cart_url'] = esc_url( $woocommerce->cart->get_cart_url() );
	}

	$vertial_menu_locations = isset( $zanmb['enable_vertical_menu_for_locations'] ) ? (array)$zanmb['enable_vertical_menu_for_locations'] : array();
	if ( !empty( $vertial_menu_locations ) ) {
		foreach ( $vertial_menu_locations as $menu_location ) {
			$vertical_menu_breakpoint = isset( $zanmb[$menu_location . '_break_point'] ) ? max( 1, intval( $zanmb[$menu_location . '_break_point'] ) ) : 991;
			$zmb_localize['vertical_menu_info'][$menu_location] = array(
				'base_width_type'    => 'closest',
				'closest_selector'   => '.page',
				'lv0_width'          => 320,
				'base_width'         => 1170,
				'menu_wrap_selector' => '.zmb-menu-location-' . esc_attr( $menu_location ),
				'break_point'        => $vertical_menu_breakpoint
			);

			if ( isset( $zanmb[$menu_location . '_vertical_base_width_type'] ) ) {
				$zmb_localize['vertical_menu_info'][$menu_location]['base_width_type'] = esc_attr( $zanmb[$menu_location . '_vertical_base_width_type'] );
			}
			if ( isset( $zanmb[$menu_location . '_vertical_lv0_width'] ) ) {
				$zmb_localize['vertical_menu_info'][$menu_location]['lv0_width'] = intval( $zanmb[$menu_location . '_vertical_lv0_width'] );
			}
			if ( isset( $zanmb[$menu_location . '_vertical_base_width_closest'] ) ) {
				$zmb_localize['vertical_menu_info'][$menu_location]['closest_selector'] = esc_attr( $zanmb[$menu_location . '_vertical_base_width_closest'] );
			}
			if ( isset( $zanmb[$menu_location . '_vertical_base_width'] ) ) {
				$zmb_localize['vertical_menu_info'][$menu_location]['base_width'] = intval( $zanmb[$menu_location . '_vertical_base_width'] );
			}
		}
	}

	wp_register_style( 'zmb-effects', ZMB_SKINS_URL . 'effects.css', false, ZMB_VERSION, 'all' );
	wp_enqueue_style( 'zmb-effects' );

	wp_register_style( 'zmb-skin-default', ZMB_SKINS_URL . 'default.css', false, ZMB_VERSION, 'all' );
	wp_enqueue_style( 'zmb-skin-default' );

	wp_register_style( 'zmb-frontend-style', ZMB_CSS_URL . 'frontend-style.css', false, ZMB_VERSION, 'all' );
	wp_enqueue_style( 'zmb-frontend-style' );

	wp_register_script( 'zmb-frontend', ZMB_JS_URL . 'frontend.js', array( 'jquery' ), ZMB_VERSION, true );
	wp_enqueue_script( 'zmb-frontend' );

	wp_localize_script( 'zmb-frontend', 'zmb_ajaxurl', get_admin_url() . '/admin-ajax.php' );
	wp_localize_script( 'zmb-frontend', 'zmb', $zmb_localize );

	$script = '';
	if ( isset( $zanmb['custom_js_code'] ) ) {
		$script .= stripslashes( $zanmb['custom_js_code'] );
	}

	if ( trim( $script ) != '' ) {
		$custom_js = 'jQuery(document).ready(function($){
		                    ' . stripslashes( $script ) . '
		                });';
		wp_add_inline_script( 'zmb-frontend', $custom_js );
	}

}

add_action( 'wp_enqueue_scripts', 'zmb_enqueue_script', 20 );

if ( !function_exists( 'zmb_custom_menu_style' ) ) {
	/*
	 * Custom menu style and breakpoint
	 */
	function zmb_custom_menu_style() {
		global $zanmb;
		$custom_css = isset( $zanmb['custom_css_code'] ) ? $zanmb['custom_css_code'] : '';
		wp_add_inline_style( 'zmb-frontend-style', $custom_css );

	}

	add_action( 'wp_enqueue_scripts', 'zmb_custom_menu_style', 21 );
}


function zmb_core_enqueue_admin_script() {
	global $zanmb, $pagenow;

	$page = isset( $_REQUEST['page'] ) ? $_REQUEST['page'] : '';

	$load_simple_line_font_icons = isset( $zanmb['load_simple_line_font_icons'] ) ? $zanmb['load_simple_line_font_icons'] == 1 : true;
	if ( $load_simple_line_font_icons ) {
		wp_register_style( 'simple-line-icons', ZMB_VENDORS_URL . 'simple-line-icons/simple-line-icons.css', false, ZMB_VERSION, 'all' );
		wp_enqueue_style( 'simple-line-icons' );
	}

	wp_register_style( 'spectrum', ZMB_VENDORS_URL . 'spectrum/spectrum.css', array(), ZMB_VERSION, 'all' );
	wp_enqueue_style( 'spectrum' );

	if ( $page == 'zanmb_options' ) {
		wp_register_style( 'zmb-redux', ZMB_CSS_URL . 'redux.css', false, ZMB_VERSION, 'all' );
		wp_enqueue_style( 'zmb-redux' );
	}

	if ( $pagenow == 'nav-menus.php' ) {
		wp_register_style( 'font-awesome.min', ZMB_VENDORS_URL . 'font-awesome/css/font-awesome.min.css', false, ZMB_VERSION, 'all' );
		wp_enqueue_style( 'font-awesome.min' );
		wp_register_script( 'zmb-backend-edit-menu', ZMB_JS_URL . 'backend-edit-menu.js', array( 'jquery' ), ZMB_VERSION, true );
		wp_enqueue_script( 'zmb-backend-edit-menu' );

		$nav_menu_term_meta_html = '<h3>' . esc_html__( 'Mega Menu', 'zanmb' ) . '</h3>';

		if ( current_theme_supports( 'menus' ) ) {
			$locations = get_registered_nav_menus();
			$enable_mega_on_locations = isset( $zanmb['enable_on_menu_locations'] ) ? (array)$zanmb['enable_on_menu_locations'] : array();

			ob_start();
			?>
			<dl class="menu-theme-locations zmb-menu-theme-locations">
				<dt class="howto"><?php _e( 'Mega Menu Settings' ); ?></dt>
				<?php foreach ( $locations as $location => $description ) : ?>
					<?php
					$location_breakpoint = isset( $zanmb[$location . '_break_point'] ) ? $zanmb[$location . '_break_point'] : '767';
					?>
					<dd data-location="<?php echo esc_attr( $location ); ?>"
					    class="checkbox-input zmb-menu-location-setting">
						<input
							type="checkbox"<?php checked( in_array( $location, $enable_mega_on_locations ) ); ?>
							id="zmb-location-<?php echo esc_attr( $location ); ?>"
							class="zmb-enable-mega-location"
							value="<?php echo esc_attr( $location ); ?>"/>
						<label
							for="zmb-location-<?php echo esc_attr( $location ); ?>"><?php echo sprintf( esc_html__( 'Enable mega for %s (%s)', 'zanmb' ), '<b>' . $location . '</b>', $description ); ?></label>
						<br>
						<label for="zmb-location-breakpoint-<?php echo esc_attr( $location ); ?>">
							<?php esc_html_e( 'Break Point', 'zanmb' ); ?>
							<input type="text" id="zmb-location-breakpoint-<?php echo esc_attr( $location ); ?>"
							       class="zmb-location-breakpoint"
							       value="<?php echo esc_attr( $location_breakpoint ); ?>">
							<em class="desc"><?php esc_html_e( 'Enter integer number', 'zanmb' ); ?></em>
						</label>
						<br>
					</dd>
				<?php endforeach; ?>
			</dl>
			<?php
			$nav_menu_term_meta_html .= ob_get_clean();

		}

		$zmb_backend_localize = array(
			'nav_menu_term_meta_html' => $nav_menu_term_meta_html,
			'message'                 => array(
				'updating' => esc_html__( 'Updating...', 'zanmb' ),
				'success'  => esc_html__( 'Success', 'zanmb' ),
				'fail'     => esc_html__( 'Fail', 'zanmb' )
			)
		);
		wp_localize_script( 'zmb-backend-edit-menu', 'zmb_admin', $zmb_backend_localize );

	}

	wp_register_style( 'zmb-backend-style', ZMB_CSS_URL . 'backend-style.css', false, ZMB_VERSION, 'all' );
	wp_enqueue_style( 'zmb-backend-style' );

	// Backend JS
	wp_register_script( 'spectrum', ZMB_VENDORS_URL . 'spectrum/spectrum.js', array( 'jquery' ), ZMB_VERSION, true );
	wp_enqueue_script( 'spectrum' );

	wp_register_script( 'zmb-backend', ZMB_JS_URL . 'backend.js', array( 'jquery' ), ZMB_VERSION, true );
	wp_enqueue_script( 'zmb-backend' );
}

add_action( 'admin_enqueue_scripts', 'zmb_core_enqueue_admin_script' );


/**
 * Load custom post type
 */
$postTypeArgs = array( 'zanmenu' );
if ( !empty( $postTypeArgs ) ):
	foreach ( $postTypeArgs as $postType ):
		$postType = sanitize_key( $postType );
		$filePath = ZMB_CORE . 'post-types/post-' . $postType . '.php';
		if ( file_exists( $filePath ) ):
			include_once $filePath;
		endif;
	endforeach;
endif;

/**
 * Load custom taxonomies
 */
$taxonomiesArgs = array();
if ( !empty( $taxonomiesArgs ) ):
	foreach ( $taxonomiesArgs as $taxonomy ):
		$taxonomy = sanitize_key( $taxonomy );
		$filePath = ZMB_CORE . 'taxonomies/taxonomy-' . $taxonomy . '.php';
		if ( file_exists( $filePath ) ):
			include_once $filePath;
		endif;
	endforeach;
endif;

/**
 * Load Post type metaboxes
 */
//include_once ZMB_CORE . 'metaboxes/post-type-metaboxes/global-metaboxes.php';

$postTypeMetaboxesArgs = array( 'zanmenu' );
if ( !empty( $postTypeMetaboxesArgs ) ):
	foreach ( $postTypeMetaboxesArgs as $post_type ):
		$post_type = sanitize_key( $post_type );
		$filePath = ZMB_CORE . 'metaboxes/post-type-metaboxes/metaboxes-' . $post_type . '.php';
		if ( file_exists( $filePath ) ):
			include_once $filePath;
		endif;
	endforeach;
endif;

/**
 * Load Menu edit custom
 */

if ( file_exists( ZMB_CLASSES . 'zmbNavMenuEditCustom.php' ) ) {
	require_once ZMB_CLASSES . 'zmbNavMenuEditCustom.php';
}

/**
 *  Load Zan Menu Builder Walker
 */
zmb_require_once( ZMB_CLASSES . 'zmb_navwalker.php' );

/*
 * Load Zan Menu Font Icons Picker
 */
zmb_require_once( ZMB_CORE . 'font-icons-picker.php' );

/*
 * Load Zan Menu Builder functions
 */
zmb_require_once( ZMB_CORE . 'functions.php' );

/**
 * Load VC Global Custom
 */
zmb_require_once( ZMB_CORE . 'shortcodes/zan-vc-global.php' );

/**
 * Load shortcode for location any where
 */
zmb_require_once( ZMB_CORE . 'shortcodes/zmb_shortcode.php' );

/**
 *  Load all shortcodes for VC
 **/
zmb_require_once( ZMB_CORE . 'shortcodes/zmb_content.php' );
zmb_require_once( ZMB_CORE . 'shortcodes/zmb_custom_nav_menu.php' );
zmb_require_once( ZMB_CORE . 'shortcodes/zmb_simple_title_desc.php' );
zmb_require_once( ZMB_CORE . 'shortcodes/zmb_img_item_with_text.php' );
zmb_require_once( ZMB_CORE . 'shortcodes/zmb_posts_list.php' );
zmb_require_once( ZMB_CORE . 'shortcodes/zmb_post_single.php' );
zmb_require_once( ZMB_CORE . 'shortcodes/zmb_products_list.php' );
zmb_require_once( ZMB_CORE . 'shortcodes/zmb_single_product.php' );
zmb_require_once( ZMB_CORE . 'shortcodes/zmb_news_letter.php' );
zmb_require_once( ZMB_CORE . 'shortcodes/zmb_login_ajax.php' );