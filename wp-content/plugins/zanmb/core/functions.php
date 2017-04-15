<?php

if ( !defined( 'ABSPATH' ) ) {
	exit; // disable direct access
}


if ( !function_exists( 'zmb_filter_wp_nav_menu_args' ) ) {
	function zmb_filter_wp_nav_menu_args( $args ) {
		global $zanmb;

		$menu_locations = isset( $zanmb['enable_on_menu_locations'] ) ? (array)$zanmb['enable_on_menu_locations'] : array();
		$vertial_menu_locations = isset( $zanmb['enable_vertical_menu_for_locations'] ) ? (array)$zanmb['enable_vertical_menu_for_locations'] : array();

		if ( !empty( $menu_locations ) ) {
			if ( in_array( $args['theme_location'], $menu_locations ) && class_exists( 'zmbNavWalker' ) ) {
				$enable_sticky = isset( $zanmb[$args['theme_location'] . '_enable_sticky'] ) ? $zanmb[$args['theme_location'] . '_enable_sticky'] == 1 : false;
				$args['container_class'] .= ' zmb-wrap zmb-' . esc_attr( $args['theme_location'] ) . '-wrap zmb-menu-location-' . esc_attr( $args['theme_location'] );
				if ( $enable_sticky ) {
					$args['container_class'] .= ' zmb-is-sticky';
				}
				if ( in_array( $args['theme_location'], $vertial_menu_locations ) ) {
					$args['container_class'] .= ' zmb-is-vertical-menu';
				}
				$args['menu_class'] .= ' zmb-menu';
				$args['container'] = 'div';
				$args['walker'] = new zmbNavWalker();
			}
		}

		return $args;
	}

	add_filter( 'wp_nav_menu_args', 'zmb_filter_wp_nav_menu_args', 99 );
}

if ( !function_exists( 'zmb_menu_nav_walker' ) ) {
	function zmb_menu_nav_walker( $args ) {

		if ( class_exists( 'zmbNavWalker' ) ) {
			$args['walker'] = new zmbNavWalker();
		}

		wp_nav_menu(
			$args
		);
	}

	add_filter( 'zmb_menu_location', 'zmb_menu_nav_walker', 10, 1 );
}

if ( !function_exists( 'zmb_sliding_content' ) ) {
	function zmb_sliding_content() {
		global $zanmb;

		$enable_sliding_menu = isset( $zanmb['enable_sliding_menu'] ) ? $zanmb['enable_sliding_menu'] == 1 : false;

		$html = '';

		if ( $enable_sliding_menu ) {
			$sliding_menu_content_id = isset( $zanmb['sliding_menu_content_id'] ) ? intval( $zanmb['sliding_menu_content_id'] ) : 0;

			if ( $sliding_menu_content_id > 0 ) {
				$sliding_content = '';

				$use_apply_filter_the_content = isset( $zanmb['apply_filter_the_content'] ) ? $zanmb['apply_filter_the_content'] == 1 : true;
				if ( $use_apply_filter_the_content ) {
					$sliding_content = apply_filters( 'the_content', get_post_field( 'post_content', $sliding_menu_content_id ) );
				}
				else {
					$sliding_content = do_shortcode( get_post_field( 'post_content', $sliding_menu_content_id ) );
				}

				$sliding_width = isset( $zanmb['sliding_width'] ) ? max( 0, intval( $zanmb['sliding_width'] ) ) : 350;

				$html .= '<div class="zmb-sliding-content-wrap" data-width="' . esc_attr( $sliding_width ) . '" style="width: ' . esc_attr( $sliding_width ) . 'px">';
				$html .= '<div class="zmb-sliding-content-inner">';
				$html .= '<a href="#" class="zmb-close-sliding"><span>X</span></a>';
				$html .= $sliding_content;
				$html .= '</div><!-- /.zmb-sliding-content-inner -->';
				$html .= '</div><!-- /.zmb-sliding-content-wrap -->';

			}

		}

		echo $html;

	}

	add_action( 'wp_footer', 'zmb_sliding_content' );
}

if ( !function_exists( 'zmb_menu_output_filter' ) ) {
	function zmb_menu_output_filter( $nav_menu, $args ) {
		global $zanmb;

		$enable_on_menu_locations = isset( $zanmb['enable_on_menu_locations'] ) ? (array)$zanmb['enable_on_menu_locations'] : array();

		$_show_logo_on_menu_locations = isset( $zanmb['show_logo_on_menu_locations'] ) ? (array)$zanmb['show_logo_on_menu_locations'] : array();
		$theme_location = isset( $args->theme_location ) ? $args->theme_location : '';
		$enable_sticky = isset( $zanmb[$theme_location . '_enable_sticky'] ) ? $zanmb[$theme_location . '_enable_sticky'] == 1 : false;

		if ( !in_array( $theme_location, $enable_on_menu_locations ) ) {
			return $nav_menu;
		}

		$zmb_container_class = 'zmb-container zmb-container-' . $theme_location;
		$logo_html = '';

		if ( !class_exists( 'zmbNavWalker' ) ) {
			return $nav_menu;
		}

		if ( in_array( $args->theme_location, $_show_logo_on_menu_locations ) ) {
			$logo_default = array(
				'url'       => ZMB_BASE_URL . 'assets/images/logo.png',
				'id'        => '',
				'height'    => '',
				'width'     => '',
				'thumbnail' => ''
			);

			$logo = $logo_default;

			if ( isset( $zanmb['logo'] ) ) {
				$logo = wp_parse_args( $zanmb['logo'], $logo );

				if ( trim( $logo['id'] ) != '' ) {

					$logo_size = isset( $zanmb['logo_size'] ) ? $zanmb['logo_size'] : '';
					if ( trim( $logo_size ) != '' ) {
						$logo_size = explode( 'x', $logo_size );
					}
					$logo['width'] = isset( $logo_size[0] ) ? max( 1, intval( $logo_size[0] ) ) : $logo['width'];
					$logo['height'] = isset( $logo_size[1] ) ? max( 1, intval( $logo_size[1] ) ) : $logo['height'];

					$logo_img = zmb_resize_image( $logo['id'], null, $logo['width'], $logo['height'], false, true, false );
					$logo['url'] = $logo_img['url'];
					$logo['width'] = $logo_img['width'];
					$logo['height'] = $logo_img['height'];
				}
			}

			$logo_sticky = $logo;

			if ( isset( $zanmb['logo_sticky'] ) ) {
				$logo_sticky = wp_parse_args( $zanmb['logo_sticky'], $logo_sticky );

				if ( trim( $logo_sticky['id'] ) != '' ) {

					$logo_size = isset( $zanmb['logo_sticky_size'] ) ? $zanmb['logo_sticky_size'] : '';
					if ( trim( $logo_size ) != '' ) {
						$logo_size = explode( 'x', $logo_size );
					}
					$logo_sticky['width'] = isset( $logo_size[0] ) ? max( 1, intval( $logo_size[0] ) ) : $logo_sticky['width'];
					$logo_sticky['height'] = isset( $logo_size[1] ) ? max( 1, intval( $logo_size[1] ) ) : $logo_sticky['height'];

					$logo_img = zmb_resize_image( $logo_sticky['id'], null, $logo_sticky['width'], $logo_sticky['height'], false, true, false );
					$logo_sticky['url'] = $logo_img['url'];
					$logo_sticky['width'] = $logo_img['width'];
					$logo_sticky['height'] = $logo_img['height'];
				}
			}

			$logo_position = isset( $zanmb['logo_position'] ) ? $zanmb['logo_position'] : 'left';

			if ( $theme_location != '' ) {
				if ( in_array( $theme_location, $_show_logo_on_menu_locations ) ) {
					$logo_html .= '<div class="zmb-logo"><a class="zmb-logo-link" data-href-sticky="' . esc_attr( $logo_sticky['url'] ) . '" data-width-sticky="' . esc_attr( $logo_sticky['width'] ) . '" data-height-sticky="' . esc_attr( $logo_sticky['height'] ) . '" href="' . esc_url( home_url( '/' ) ) . '"><img width="' . esc_attr( $logo['width'] ) . '" height="' . esc_attr( $logo['height'] ) . '" src="' . esc_url( $logo['url'] ) . '" alt="logo" /></a></div>';
					$zmb_container_class .= ' has-logo logo-position-' . $logo_position;
				}
			}
		}

		if ( $enable_sticky ) {
			$zmb_container_class .= ' zmb-container-sticky';
		}

		$nav_menu = '<div class="' . esc_attr( $zmb_container_class ) . '">' . $logo_html . $nav_menu . '</div>';

		return $nav_menu;
	}

	add_filter( 'wp_nav_menu', 'zmb_menu_output_filter', 10, 2 );
}

if ( !function_exists( 'zmb_update_menu_location_settings_via_ajax' ) ) {
	function zmb_update_menu_location_settings_via_ajax() {

		$response = array(
			'message' => '',
			'html'    => ''
		);

		$menu_location = isset( $_POST['menu_location'] ) ? esc_attr( $_POST['menu_location'] ) : '';
		$is_enable_mega = isset( $_POST['is_enable_mega'] ) ? $_POST['is_enable_mega'] : 'no';
		$break_point = isset( $_POST['break_point'] ) ? intval( $_POST['break_point'] ) : 767;

		$registered_locations = get_nav_menu_locations();
		if ( isset( $registered_locations[$menu_location] ) ) {
			global $zanmb;

			$zmb_db_options = get_option( 'zanmb' );
			$current_enable_on_menu_locations = isset( $zanmb['enable_on_menu_locations'] ) ? $zanmb['enable_on_menu_locations'] : array();

			if ( $is_enable_mega == 'yes' ) {
				if ( !in_array( $menu_location, $current_enable_on_menu_locations ) ) {
					$current_enable_on_menu_locations[] = $menu_location;
					$zmb_db_options['enable_on_menu_locations'] = $current_enable_on_menu_locations;
					$response['message'] = sprintf( esc_html__( 'Enable mega menu for %s', 'zanmb' ), $menu_location );
				}
			}
			else {
				if ( in_array( $menu_location, $current_enable_on_menu_locations ) ) {
					$current_enable_on_menu_locations = array_diff( $current_enable_on_menu_locations, array( $menu_location ) );
					$zmb_db_options['enable_on_menu_locations'] = $current_enable_on_menu_locations;
					$response['message'] = sprintf( esc_html__( 'Disable mega menu for %s', 'zanmb' ), $menu_location );
				}
			}

			// Set new break point
			$zmb_db_options[$menu_location . '_break_point'] = $break_point;

			// Update menu options
			update_option( 'zanmb', $zmb_db_options );

		}

		wp_send_json( $response );
		die();
	}

	add_action( 'wp_ajax_zmb_update_menu_location_settings_via_ajax', 'zmb_update_menu_location_settings_via_ajax', 99 );
}

if ( !function_exists( 'zmb_init_vc_global' ) ) {
	function zmb_init_vc_global() {
		// Check if Visual Composer is installed
		if ( !defined( 'WPB_VC_VERSION' ) ) {
			return;
		}

		if ( version_compare( WPB_VC_VERSION, '4.2', '<' ) ) {

			add_action( 'init', 'zmb_add_vc_global_params', 100 );

		}
		else {

			add_action( 'vc_after_mapping', 'zmb_add_vc_global_params' );

		}
	}

	add_action( 'after_setup_theme', 'zmb_init_vc_global', 1 );
}

if ( !function_exists( 'zmb_add_vc_global_params' ) ) {
	function zmb_add_vc_global_params() {

		// Check if Visual Composer is installed
		if ( !defined( 'WPB_VC_VERSION' ) ) {
			return;
		}

		vc_add_shortcode_param( 'zmb_select_cat_field', 'zmb_select_cat_field' );

	}
}

if ( !function_exists( 'zmb_select_cat_field' ) ) {
	function zmb_select_cat_field( $settings, $value ) {

		return '<div class="select_post_cat_block">'
		       . zmb_custom_tax_slug_select(
			       array( $value ),
			       array(
				       'tax'               => 'category',
				       'class'             => 'wpb_vc_param_value',
				       'name'              => $settings['param_name'],
				       'first_option'      => true,
				       'first_option_val'  => '',
				       'first_option_text' => esc_html__( ' --- All Categories --- ', 'zanmb' )
			       )
		       )
		       . '</div>';
	}
}

if ( !function_exists( 'zmb_custom_tax_slug_select' ) ) {
	function zmb_custom_tax_slug_select( $selected_tax_slugs = array(), $settings = array() ) {

		$term_args = array();
		$default_settings = array(
			'tax'               => 'category',
			'multiple'          => false,
			'id'                => '',
			'name'              => '',
			'class'             => '',
			'first_option'      => false,
			'first_option_val'  => '',
			'first_option_text' => __( ' --------------- ', 'zanmb' )
		);

		$settings = wp_parse_args( $settings, $default_settings );

		if ( !taxonomy_exists( $settings['tax'] ) ):

			return false;

		endif;

		$attrs = '';
		$attrs .= ( trim( $settings['id'] ) != '' ) ? 'id="' . esc_attr( $settings['id'] ) . '"' : '';
		$attrs .= ( trim( $settings['name'] ) != '' ) ? ' name="' . esc_attr( $settings['name'] ) . '"' : '';
		$attrs .= ( $settings['multiple'] === true ) ? ' multiple="true"' : '';

		zmb_custom_taxonomy_opt_walker( $term_args, $settings['tax'] );

		$html = '';
		if ( !empty( $term_args ) ):

			$html .= '<select ' . $attrs . ' class="zmb-select zan-select ' . esc_attr( $settings['class'] ) . '">';

			if ( $settings['first_option'] ):
				$html .= '<option ' . selected( in_array( 0, $selected_tax_slugs ), true, false ) . ' value="' . esc_attr( $settings['first_option_val'] ) . '">' . sanitize_text_field( $settings['first_option_text'] ) . '</option>';
			endif;

			foreach ( $term_args as $term_id => $term_name ):

				$term = get_term( $term_id, $settings['tax'] );
				if ( !is_wp_error( $term ) ) {
					$html .= '<option ' . selected( in_array( $term->slug, $selected_tax_slugs ), true, false ) . ' value="' . esc_attr( $term->slug ) . '">' . $term_name . '</option>';
				}

			endforeach;
			$html .= '</select>';

		endif;

		return $html;

	}
}

if ( !function_exists( 'zmb_custom_taxonomy_opt_walker' ) ) {
	/**
	 *  Return terms array
	 *
	 * @since 1.0
	 **/
	function zmb_custom_taxonomy_opt_walker( &$terms_select_opts = array(), $taxonomy, $parent = 0, $lv = 0 ) {

		$terms = get_terms( $taxonomy, array( 'parent' => $parent, 'hide_empty' => false ) );

		if ( $parent > 0 ):
			$lv++;
		endif;

		//If there are terms
		if ( count( $terms ) > 0 ):
			$prefix = '';
			if ( $lv > 0 ):
				for ( $i = 1; $i <= $lv; $i++ ):
					$prefix .= '-- ';
				endfor;
			endif;

			//Cycle though the terms
			foreach ( $terms as $term ):
				$terms_select_opts[$term->term_id] = htmlentities2( $prefix . $term->name );

				//Function calls itself to display child elements, if any
				zmb_custom_taxonomy_opt_walker( $terms_select_opts, $taxonomy, $term->term_id, $lv );
			endforeach;

		endif;

	}
}

if ( !function_exists( 'zmb_get_shortcode_custom_css_no_wrap' ) ) {
	function zmb_get_shortcode_custom_css_no_wrap( $post_id = 0 ) {

		if ( !class_exists( 'Vc_Manager' ) ) {
			return '';
		}

		$output = $shortcodes_custom_css = '';
		$id = $post_id;
		if ( preg_match( '/^\d+$/', $id ) ) {
			$shortcodes_custom_css = get_post_meta( $id, '_wpb_shortcodes_custom_css', true );
		}

		$output .= $shortcodes_custom_css;

		return $output;
	}
}

if ( !function_exists( 'zmb_get_shortcode_custom_css' ) ) {
	function zmb_get_shortcode_custom_css( $post_id = 0 ) {

		$output = $shortcodes_custom_css = '';

		$shortcodes_custom_css .= zmb_get_shortcode_custom_css_no_wrap( $post_id );

		if ( !empty( $shortcodes_custom_css ) ) {
			$shortcodes_custom_css = strip_tags( $shortcodes_custom_css );
			$output .= '<style type="text/css" data-type="vc_shortcodes-custom-css" class="zmb-custom-shortcodes-css">';
			$output .= $shortcodes_custom_css;
			$output .= '</style>';
		}

		return $output;
	}
}

if ( !function_exists( 'zmb_inline_css' ) ) {
	function zmb_inline_css() {
		global $zanmb;

		$menu_locations = isset( $zanmb['enable_on_menu_locations'] ) ? (array)$zanmb['enable_on_menu_locations'] : array();

		$custom_css = '';

		if ( !empty( $menu_locations ) ) {
			foreach ( $menu_locations as $location ) {
				$breakpoint = isset( $zanmb[$location . '_break_point'] ) ? max( 1, intval( $zanmb[$location . '_break_point'] ) ) : 991;

				$custom_css .= '@media (max-width: ' . esc_attr( $breakpoint ) . 'px) {
							.zmb-container-' . $location . ' .zmb-wrap, .zmb-sticky-menu-wrap .zmb-sticky-menu-inner .zmb-container-' . $location . ' .zmb-wrap {
								width: auto;
							}
							.zmb-sticky-menu-wrap .zmb-sticky-menu-inner .zmb-container-' . $location . ' .zmb-logo {
								padding: 10px 15px;
							}
							.zmb-sticky-menu-wrap .zmb-sticky-menu-inner .zmb-toggle-menu-mobile {
								padding: 10px 15px;
							}
							.zmb-' . $location . '-wrap .zmb-toggle-menu-mobile {
								display: block;
							}
							.zmb-' . $location . '-wrap > .zmb-menu {
								display: none;
							}
							.zmb-container-' . $location . ' .zmb-logo {
							    position: relative;
							    top: auto;
							    bottom: auto;
							    left: auto;
							    right: auto;
							    -webkit-transform: none;
							    -ms-transform: none;
							    -o-transform: none;
							    transform: none;
							    float: left;
							}
						}
						';

			}
		}

		$enable_sliding_menu = isset( $zanmb['enable_sliding_menu'] ) ? $zanmb['enable_sliding_menu'] == 1 : false;

		if ( $enable_sliding_menu ) {
			$sliding_menu_content_id = isset( $zanmb['sliding_menu_content_id'] ) ? intval( $zanmb['sliding_menu_content_id'] ) : 0;

			if ( $sliding_menu_content_id > 0 ) {

				$custom_css .= zmb_get_shortcode_custom_css_no_wrap( $sliding_menu_content_id );

			}

		}

		// Base color
		$base_color = isset( $zanmb['base_color'] ) ? $zanmb['base_color'] : array( 'color' => '#49c3df', 'alpha' => 1 );
		$root_base_color = $base_color['color'];
		if ( !isset( $base_color['alpha'] ) ) {
			$base_color['alpha'] = 1;
		}
		if ( trim( $base_color['alpha'] ) == '' ) {
			$base_color['alpha'] = 1;
		}
		$base_color = zmb_color_hex2rgba( $base_color['color'], $base_color['alpha'] );

		$custom_css .= '.zanmenu-content .zmb-btn a:hover {
				            background-color: ' . $base_color . ' !important;}
			            .zanmenu-content .zmb-btn a.zmb-btn-cart:hover {
					        background-color: ' . $base_color . '; }
				        .zanmenu-content .zmb-mini-cart-wrap .button:hover {
						    background-color: ' . $base_color . '; }
					    .zmb-single-post-wrap .zmb-single-post-inner figure .icon-wrap {
					        background-color: ' . $base_color . '; }
				        .zmb-products-list-wrap .product-item .price {
				            color: ' . $base_color . '; }
			            .zmb-single-product-wrap .single-product-inner h3 {
		                    color: ' . $base_color . '; }
		                .zmb-single-product-wrap .single-product-inner .price {
					        color: ' . $base_color . '; }
				        .zmb-img-item-with-text-wrap .item-inner .text-wrap .img-item-title a:hover {
		                    color: ' . $base_color . '; }
		                .zmb-wrap .zmb-toggle-menu-mobile:hover .icon {
		                    color: ' . $base_color . '; }
		                .zmb-wrap .zmb-toggle-menu-mobile:hover .icon span {
		                    background-color: ' . $base_color . '; }
		                .zmb-wrap .zmb-menu > li .sub-menu {
		                    border-top: 3px solid ' . $base_color . ';}
		                .zmb-wrap .zmb-menu > li .sub-menu > .menu-item a:hover {
		                    color: ' . $base_color . '; }
		                .zmb-wrap .zmb-menu > li .sub-menu .vc_tta-container .vc_tta-tabs-container .vc_tta-tabs-list .vc_tta-tab.vc_active > a {
			                color: ' . $base_color . '; }
		                .zmb-wrap .zmb-menu > li .sub-menu .vc_tta-panels-container .vc_tta-panel-body .widget ul > li:hover {
		                    color: ' . $base_color . '; }
		                .zmb-wrap .zmb-menu > li .sub-menu .vc_tta-panels-container .zmb-btn:hover {
				            background-color: ' . $base_color . ';}
			            .zmb-wrap .zmb-menu > li .sub-menu .vc_tta-panels-container .zmb-read-more {
				            color: ' . $base_color . ';}
			            .zmb-wrap .zmb-menu > li .sub-menu .vc_tta-panels-container .zmb-read-more a:hover {
		                    border-bottom: 1px solid ' . $base_color . '; }
		                .zmb-wrap .zmb-menu > li .sub-menu .zmb-btn.zmb-btn-white-o:hover a {
		                    background-color: ' . $base_color . '; }
		                .zmb-wrap .zmb-menu > li .sub-menu .widget ul > li:hover {
		                    color: ' . $base_color . '; }
		                .zmb-wrap .zmb-menu > li.current_page_item > a {
						    background-color: ' . $base_color . '; }
					    .zmb-wrap .zmb-menu > li:hover {
		                    background-color: ' . $base_color . '; }
		                .zmb-wrap .zmb-menu > li.zmb-cart .zmb-cart-icon .zmb-mini-cart-count {
					        background-color: ' . $base_color . ';}
				        .zmb-wrap .wpcf7 .wpcf7-form .wpcf7-submit:hover {
		                    background-color: ' . $base_color . '; }
		                .zmb-clone-wrap > .zmb-panels > .zmb-panel .zmb-menu > .menu-item > a:hover, .zmb-clone-wrap > .zmb-panels > .zmb-panel .sub-menu > .menu-item > a:hover {
		                    color: ' . $base_color . '; }
		                .zmb-clone-wrap > .zmb-panels > .zmb-panel .zmb-menu .zmb-cart .zmb-cart-icon .zmb-mini-cart-count, .zmb-clone-wrap > .zmb-panels > .zmb-panel .sub-menu .zmb-cart .zmb-cart-icon .zmb-mini-cart-count {
				            background-color: ' . $base_color . ';}
			            .zmb-clone-wrap > .zmb-panels > .zmb-panel .wpcf7 .wpcf7-form .wpcf7-submit:hover {
		                    background-color: ' . $base_color . '; }
		                .zmb-clone-wrap > .zmb-panels > .zmb-panel .widget ul > li > a:hover {
		                    color: ' . $base_color . '; }
		                .zmb-clone-wrap > .zmb-panels > .zmb-panel .zmb-btn.zmb-with-100.zmb-btn-white-o:hover a {
		                    background-color: ' . $base_color . '; }
		                .zmb-clone-wrap > .zmb-panels > .zmb-panel .vc_tta-panel .zmb-btn:hover {
				            background-color: ' . $base_color . ';}
			            .zmb-clone-wrap > .zmb-panels > .zmb-panel .vc_tta-panel .zmb-read-more {
					        color: ' . $base_color . ';}
				        .zmb-clone-wrap > .zmb-panels > .zmb-panel .vc_tta-panel .zmb-read-more a:hover {
		                    border-bottom: 1px solid ' . $base_color . '; }
						';

		wp_add_inline_style( 'zmb-frontend-style', $custom_css );

	}

	add_action( 'wp_enqueue_scripts', 'zmb_inline_css', 99 );
}

if ( !function_exists( 'zmb_trim_excerpt' ) ) {
	/**
	 * Generates an excerpt from the content, if needed.
	 *
	 * The excerpt word amount will be 10 words and if the amount is greater than
	 * that, then the string ' [&hellip;]' will be appended to the excerpt. If the string
	 * is less than 10 words, then the content will be returned as is.
	 */
	function zmb_trim_excerpt( $text = '', $words_length = 10, $excerpt_more = '...' ) {
		$raw_excerpt = $text;
		if ( '' == $text ) {
			$text = get_the_content( '' );

			$text = strip_shortcodes( $text );

			/** This filter is documented in wp-includes/post-template.php */
			$text = apply_filters( 'the_content', $text );
			$text = str_replace( ']]>', ']]&gt;', $text );
		}

		if ( trim( $text ) != '' ) {
			$words_length = max( 1, intval( $words_length ) );
			$excerpt_more = esc_html( $excerpt_more );
			$text = wp_trim_words( $text, $words_length, $excerpt_more );
		}

		return $text;
	}
}

if ( !function_exists( 'zmb_vc_include_field_post_search' ) ) {
	/**
	 * @param $search_string
	 *
	 * @return array
	 */
	function zmb_vc_include_field_post_search( $search_string ) {
		$query = $search_string;
		$data = array();
		$args = array( 's' => $query, 'post_type' => 'post' );
		$args['vc_search_by_title_only'] = true;
		$args['numberposts'] = -1;
		if ( strlen( $args['s'] ) == 0 ) {
			unset( $args['s'] );
		}
		add_filter( 'posts_search', 'vc_search_by_title_only', 500, 2 );
		$posts = get_posts( $args );
		if ( is_array( $posts ) && !empty( $posts ) ) {
			foreach ( $posts as $post ) {
				$data[] = array(
					'value' => $post->ID,
					'label' => $post->post_title,
					'group' => $post->post_type,
				);
			}
		}

		return $data;
	}
}

if ( !function_exists( 'zmb_vc_include_field_render' ) ) {
	/**
	 * @param $value
	 *
	 * @return array|bool
	 */
	function zmb_vc_include_field_render( $value ) {
		$post = get_post( $value['value'] );

		return is_null( $post ) ? false : array(
			'label' => $post->post_title,
			'value' => $post->ID,
			'group' => $post->post_type
		);
	}
}

/**
 * @param $search_string
 *
 * @return array
 */
function zmb_vc_include_field_product_search( $search_string ) {
	$query = $search_string;
	$data = array();
	$args = array( 's' => $query, 'post_type' => 'product' );
	$args['vc_search_by_title_only'] = true;
	$args['numberposts'] = -1;
	if ( strlen( $args['s'] ) == 0 ) {
		unset( $args['s'] );
	}
	add_filter( 'posts_search', 'vc_search_by_title_only', 500, 2 );
	$posts = get_posts( $args );
	if ( is_array( $posts ) && !empty( $posts ) ) {
		foreach ( $posts as $post ) {
			$data[] = array(
				'value' => $post->ID,
				'label' => $post->post_title,
				'group' => $post->post_type,
			);
		}
	}

	return $data;
}

if ( !function_exists( 'zmb_no_image' ) ) {

	/**
	 * No image generator
	 *
	 * @since 1.0
	 *
	 * @param $size : array, image size
	 * @param $echo : bool, echo or return no image url
	 **/
	function zmb_no_image( $size = array( 'width' => 500, 'height' => 500 ), $echo = false, $transparent = false
	) {

		$noimage_dir = ZMB_DIR_PATH;
		$noimage_uri = ZMB_BASE_URL;

		$suffix = ( $transparent ) ? '_transparent' : '';

		if ( !is_array( $size ) || empty( $size ) ):
			$size = array( 'width' => 500, 'height' => 500 );
		endif;

		if ( !is_numeric( $size['width'] ) && $size['width'] == '' || $size['width'] == null ):
			$size['width'] = 'auto';
		endif;

		if ( !is_numeric( $size['height'] ) && $size['height'] == '' || $size['height'] == null ):
			$size['height'] = 'auto';
		endif;

		// base image must be exist
		$img_base_fullpath = $noimage_dir . '/assets/images/noimage/no_image' . $suffix . '.png';
		$no_image_src = $noimage_uri . '/assets/images/noimage/no_image' . $suffix . '.png';


		// Check no image exist or not
		if ( !file_exists( $noimage_dir . '/assets/images/noimage/no_image' . $suffix . '-' . $size['width'] . 'x' . $size['height'] . '.png' ) && is_writable( $noimage_dir . '/assets/images/noimage/' ) ):

			$no_image = wp_get_image_editor( $img_base_fullpath );

			if ( !is_wp_error( $no_image ) ):
				$no_image->resize( $size['width'], $size['height'], true );
				$no_image_name = $no_image->generate_filename( $size['width'] . 'x' . $size['height'], $noimage_dir . '/assets/images/noimage/', null );
				$no_image->save( $no_image_name );
			endif;

		endif;

		// Check no image exist after resize
		$noimage_path_exist_after_resize = $noimage_dir . '/assets/images/noimage/no_image' . $suffix . '-' . $size['width'] . 'x' . $size['height'] . '.png';

		if ( file_exists( $noimage_path_exist_after_resize ) ):
			$no_image_src = $noimage_uri . '/assets/images/noimage/no_image' . $suffix . '-' . $size['width'] . 'x' . $size['height'] . '.png';
		endif;

		if ( $echo ):
			echo esc_url( $no_image_src );
		else:
			return esc_url( $no_image_src );
		endif;

	}
}


if ( !function_exists( 'zmb_resize_image' ) ) {
	/**
	 * @param int    $attach_id
	 * @param string $img_url
	 * @param int    $width
	 * @param int    $height
	 * @param bool   $crop
	 * @param bool   $place_hold        Using place hold image if the image does not exist
	 * @param bool   $use_real_img_hold Using real image for holder if the image does not exist
	 * @param string $solid_img_color   Solid placehold image color (not text color). Random color if null
	 *
	 * @since 1.0
	 * @return array
	 */
	function zmb_resize_image(
		$attach_id = null, $img_url = null, $width, $height, $crop = false, $place_hold = true,
		$use_real_img_hold = true, $solid_img_color = null
	) {

		// If is singular and has post thumbnail and $attach_id is null, so we get post thumbnail id automatic (there is a bug, don't use)
		//		if ( is_singular() && !$attach_id && !$img_url ) {
		//			if ( has_post_thumbnail() && !post_password_required() ) {
		//				$attach_id = get_post_thumbnail_id();
		//			}
		//		}

		// this is an attachment, so we have the ID
		$image_src = array();
		if ( $attach_id ) {
			$image_src = wp_get_attachment_image_src( $attach_id, 'full' );
			$actual_file_path = get_attached_file( $attach_id );
			// this is not an attachment, let's use the image url
		}
		else {
			if ( $img_url ) {
				$file_path = str_replace( get_site_url(), ABSPATH, $img_url );
				$actual_file_path = rtrim( $file_path, '/' );
				if ( !file_exists( $actual_file_path ) ) {
					$file_path = parse_url( $img_url );
					$actual_file_path = rtrim( ABSPATH, '/' ) . $file_path['path'];
				}
				if ( file_exists( $actual_file_path ) ) {
					$orig_size = getimagesize( $actual_file_path );
					$image_src[0] = $img_url;
					$image_src[1] = $orig_size[0];
					$image_src[2] = $orig_size[1];
				}
				else {
					$image_src[0] = '';
					$image_src[1] = 0;
					$image_src[2] = 0;
				}
			}
		}
		if ( !empty( $actual_file_path ) && file_exists( $actual_file_path ) ) {
			$file_info = pathinfo( $actual_file_path );
			$extension = '.' . $file_info['extension'];

			// the image path without the extension
			$no_ext_path = $file_info['dirname'] . '/' . $file_info['filename'];

			$cropped_img_path = $no_ext_path . '-' . $width . 'x' . $height . $extension;

			// checking if the file size is larger than the target size
			// if it is smaller or the same size, stop right here and return
			if ( $image_src[1] > $width || $image_src[2] > $height ) {

				// the file is larger, check if the resized version already exists (for $crop = true but will also work for $crop = false if the sizes match)
				if ( file_exists( $cropped_img_path ) ) {
					$cropped_img_url = str_replace( basename( $image_src[0] ), basename( $cropped_img_path ), $image_src[0] );
					$vt_image = array( 'url' => $cropped_img_url, 'width' => $width, 'height' => $height, );

					return $vt_image;
				}

				// $crop = false
				if ( $crop == false ) {
					// calculate the size proportionaly
					$proportional_size = wp_constrain_dimensions( $image_src[1], $image_src[2], $width, $height );
					$resized_img_path = $no_ext_path . '-' . $proportional_size[0] . 'x' . $proportional_size[1] . $extension;

					// checking if the file already exists
					if ( file_exists( $resized_img_path ) ) {
						$resized_img_url = str_replace( basename( $image_src[0] ), basename( $resized_img_path ), $image_src[0] );

						$vt_image = array( 'url' => $resized_img_url, 'width' => $proportional_size[0], 'height' => $proportional_size[1], );

						return $vt_image;
					}
				}

				// no cache files - let's finally resize it
				$img_editor = wp_get_image_editor( $actual_file_path );

				if ( is_wp_error( $img_editor ) || is_wp_error( $img_editor->resize( $width, $height, $crop ) ) ) {
					return array( 'url' => '', 'width' => '', 'height' => '', );
				}

				$new_img_path = $img_editor->generate_filename();

				if ( is_wp_error( $img_editor->save( $new_img_path ) ) ) {
					return array( 'url' => '', 'width' => '', 'height' => '', );
				}
				if ( !is_string( $new_img_path ) ) {
					return array( 'url' => '', 'width' => '', 'height' => '', );
				}

				$new_img_size = getimagesize( $new_img_path );
				$new_img = str_replace( basename( $image_src[0] ), basename( $new_img_path ), $image_src[0] );

				// resized output
				$vt_image = array( 'url' => $new_img, 'width' => $new_img_size[0], 'height' => $new_img_size[1], );

				return $vt_image;
			}

			// default output - without resizing
			$vt_image = array( 'url' => $image_src[0], 'width' => $image_src[1], 'height' => $image_src[2], );

			return $vt_image;
		}
		else {
			if ( $place_hold ) {
				$width = intval( $width );
				$height = intval( $height );

				// Real image place hold (https://unsplash.it/)
				if ( $use_real_img_hold ) {
					$random_time = time() + rand( 1, 100000 );
					$vt_image = array( 'url' => 'https://unsplash.it/' . $width . '/' . $height . '?random&time=' . $random_time, 'width' => $width, 'height' => $height, );
				}
				else {
					$color = $solid_img_color;
					if ( is_null( $color ) || trim( $color ) == '' ) {

						// Show no image (gray)
						$vt_image = array( 'url' => zmb_no_image( array( 'width' => $width, 'height' => $height ) ), 'width' => $width, 'height' => $height, );
					}
					else {
						if ( $color == 'transparent' ) { // Show no image transparent
							$vt_image = array( 'url' => zmb_no_image( array( 'width' => $width, 'height' => $height ), false, true ), 'width' => $width, 'height' => $height, );
						}
						else { // No image with color from placehold.it
							$vt_image = array( 'url' => 'http://placehold.it/' . $width . 'x' . $height . '/' . $color . '/ffffff/', 'width' => $width, 'height' => $height, );
						}
					}
				}

				return $vt_image;
			}
		}

		return false;
	}
}

if ( !function_exists( 'zmb_color_hex2rgba' ) ) {
	function zmb_color_hex2rgba( $hex, $alpha = 1 ) {
		$hex = str_replace( "#", "", $hex );

		if ( strlen( $hex ) == 3 ) {
			$r = hexdec( substr( $hex, 0, 1 ) . substr( $hex, 0, 1 ) );
			$g = hexdec( substr( $hex, 1, 1 ) . substr( $hex, 1, 1 ) );
			$b = hexdec( substr( $hex, 2, 1 ) . substr( $hex, 2, 1 ) );
		}
		else {
			$r = hexdec( substr( $hex, 0, 2 ) );
			$g = hexdec( substr( $hex, 2, 2 ) );
			$b = hexdec( substr( $hex, 4, 2 ) );
		}
		$rgb = array( $r, $g, $b );

		return 'rgba( ' . implode( ', ', $rgb ) . ', ' . $alpha . ' )'; // returns the rgb values separated by commas
	}
}

if ( !function_exists( 'zmb_color_rgb2hex' ) ) {
	function zmb_color_rgb2hex( $rgb ) {
		$hex = '#';
		$hex .= str_pad( dechex( $rgb[0] ), 2, '0', STR_PAD_LEFT );
		$hex .= str_pad( dechex( $rgb[1] ), 2, '0', STR_PAD_LEFT );
		$hex .= str_pad( dechex( $rgb[2] ), 2, '0', STR_PAD_LEFT );

		return $hex; // returns the hex value including the number sign (#)
	}
}

// Ensure cart contents update when products are added to the cart via AJAX (place the following in functions.php)
function zmb_woocommerce_header_add_to_cart_fragment( $fragments ) {

	$fragments['zmb_cart_count'] = WC()->cart->get_cart_contents_count();

	return $fragments;
}

add_filter( 'add_to_cart_fragments', 'zmb_woocommerce_header_add_to_cart_fragment' );