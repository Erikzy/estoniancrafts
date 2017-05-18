<?php

/**
 * Class Name: zmbNavWalker
 */
class zmbNavWalker extends Walker_Nav_Menu
{

	/**
	 * @see   Walker::start_lvl()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Depth of page. Used for padding.
	 */
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat( "\t", $depth );
		$output .= "\n$indent<ul role=\"menu\" class=\" sub-menu\">\n";
	}

	/**
	 * @see   Walker::start_el()
	 * @since 3.0.0
	 *
	 * @param string $output       Passed by reference. Used to append additional content.
	 * @param object $item         Menu item data object.
	 * @param int    $depth        Depth of menu item. Used for padding.
	 * @param int    $current_page Menu item ID.
	 * @param object $args
	 */
	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		/**
		 * Dividers, Headers or Disabled
		 * =============================
		 * Determine whether the item is a Divider, Header, Disabled or regular
		 * menu item. To prevent errors we use the strcasecmp() function to so a
		 * comparison that is not case sensitive. The strcasecmp() function returns
		 * a 0 if the strings are equal.
		 */
		if ( strcasecmp( $item->attr_title, 'divider' ) == 0 && $depth === 1 ) {
			$output .= $indent . '<li role="presentation" class="divider">';
		}
		else if ( strcasecmp( $item->title, 'divider' ) == 0 && $depth === 1 ) {
			$output .= $indent . '<li role="presentation" class="divider">';
		}
		else if ( strcasecmp( $item->attr_title, 'dropdown-header' ) == 0 && $depth === 1 ) {
			$output .= $indent . '<li role="presentation" class="dropdown-header">' . esc_attr( $item->title );
		}
		else if ( strcasecmp( $item->attr_title, 'disabled' ) == 0 ) {
			$output .= $indent . '<li role="presentation" class="disabled"><a href="#">' . esc_attr( $item->title ) . '</a>';
		}
		else {

			$class_names = $value = '';

			$classes = empty( $item->classes ) ? array() : (array)$item->classes;
			$classes[] = 'menu-item-' . $item->ID;
			$classes[] = $item->object . '-menu-item';

			// Set "current-menu-item" class for custom url of Zan Menu item
			if ( $item->object == 'zanmenu' && isset( $_SERVER['HTTP_HOST'] ) ) {
				global $wp_rewrite;
				$_root_relative_current = untrailingslashit( $_SERVER['REQUEST_URI'] );
				$current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_root_relative_current );
				$raw_item_url = strpos( $item->mega_url, '#' ) ? substr( $item->mega_url, 0, strpos( $item->mega_url, '#' ) ) : $item->mega_url;
				$item_url = set_url_scheme( untrailingslashit( $raw_item_url ) );
				$_indexless_current = untrailingslashit( preg_replace( '/' . preg_quote( $wp_rewrite->index, '/' ) . '$/', '', $current_url ) );

				if ( $raw_item_url && in_array( $item_url, array( $current_url, $_indexless_current, $_root_relative_current ) ) ) {
					$classes[] = 'current-menu-item';
					if ( in_array( home_url(), array( untrailingslashit( $current_url ), untrailingslashit( $_indexless_current ) ) ) ) {
						// Back compat for home link to match wp_page_menu()
						$classes[] = 'current_page_item';
					}

					// give front page item current-menu-item class when extra query arguments involved
				}
				elseif ( $item_url == home_url() && is_front_page() ) {
					$classes[] = 'current-menu-item';
				}

				if ( untrailingslashit( $item_url ) == home_url() ) {
					$classes[] = 'menu-item-home';
				}
			}

			$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );

			if ( $item->object == 'custom' ) {
				$class_names .= ' zmb-custom-item';

				// Is mini cart
				if ( strpos( $class_names, 'zmb-cart' ) > 0 || trim( $class_names ) == 'zmb-cart' ) {
					if ( class_exists( 'WooCommerce' ) ) {

					}
				}

			}

			if ( $args->has_children || $item->object == 'zanmenu' || $item->enable_mega == 'yes' ) {
				$class_names .= ' dropdown';
				$class_names .= ' menu-item-has-children';
				if ( $item->enable_mega == 'yes' ) {
					$class_names .= ' zanmenu-menu-item zanmenu-menu-item-force';
				}
			}

			if ( in_array( 'current-menu-item', $classes ) ) {
				$class_names .= ' active';
			}

			$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

			$id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args );
			$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

			$output .= $indent . '<li' . $id . $value . $class_names . '>';

			$atts = array();
			$atts['title'] = !empty( $item->title ) ? $item->title : '';
			$atts['target'] = !empty( $item->target ) ? $item->target : '';
			$atts['rel'] = !empty( $item->xfn ) ? $item->xfn : '';
			$atts['class'] = 'zmb-item-title';
			$atts['data-title'] = esc_attr( $atts['title'] );
			$atts['data-obj-id'] = isset( $item->object_id ) ? esc_attr( $item->object_id ) : '';

			// If item has_children add atts to a.
			if ( $args->has_children && $depth === 0 || $item->object == 'zanmenu' && 0 === $depth || $item->enable_mega == 'yes' && 0 === $depth ) {
				$atts['href'] = !empty( $item->url ) && $item->object != 'zanmenu' ? $item->url : '';
				if ( $item->object == 'zanmenu' ) {
					$zmb_item_url = $item->mega_url;
					$atts['href'] = $zmb_item_url != '' ? $zmb_item_url : '#';
				}
				//$atts['data-toggle'] = 'dropdown';
				$atts['class'] .= ' zmb-dropdown-toggle';
			}
			else {
				$atts['href'] = !empty( $item->url ) ? $item->url : '';
				if ( $item->object == 'zanmenu' ) {
					$zmb_item_url = $item->mega_url;
					$atts['href'] = $zmb_item_url != '' ? $zmb_item_url : '#';
				}
			}

			$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args );

			$attributes = '';
			foreach ( $atts as $attr => $value ) {
				if ( !empty( $value ) ) {
					$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
					$attributes .= ' ' . $attr . '="' . $value . '"';
				}
			}

			$item_output = $args->before;

			/*
			 * Glyphicons
			 * ===========
			 * Since the the menu item is NOT a Divider or Header we check the see
			 * if there is a value in the attr_title property. If the attr_title
			 * property is NOT null we apply it as the class name for the glyphicon.
			 */
			if ( !empty( $item->attr_title ) ) {
				$item_output .= '<a' . $attributes . '><span class="glyphicon ' . esc_attr( $item->attr_title ) . '"></span>&nbsp;';
			}
			else {
				$item_output .= '<a' . $attributes . '>';
			}

			// Item font icon
			if ( trim( $item->icon_classes ) != '' ) {
				$zmb_item_icon = '<span class="zmb-social-icon item-icon item-font-icon ' . esc_attr( $item->icon_classes ) . '"></span>';
				$item_output .= apply_filters( 'zmb_item_icon', $zmb_item_icon );
			}

			// Item image icon
			if ( trim( $item->icon_img_url ) != '' ) {
				//$item_output .= '<span class="item-icon item-img-icon" style="background-image: url(' . esc_url( $item->icon_img_url ) . ');"></span>';
				$zmb_item_img_icon = '<img class="item-icon item-img-icon" src="' . esc_url( $item->icon_img_url ) . '" alt="' . esc_attr( $item->title ) . '" />';
				$item_output .= apply_filters( 'zmb_item_img_icon', $zmb_item_img_icon );
			}

			$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
			$item_output .= ( $args->has_children && 0 === $depth || $item->object == 'zanmenu' && 0 === $depth || $item->enable_mega == 'yes' && 0 === $depth ) ? ' <span class="caret"></span></a>' : '</a>';
			$item_output .= $args->after;

			//$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
			/**
			 * Check if menu item object is a mega menu object.
			 * If it is, display the mega menu content.
			 * Otherwise render elements as normal
			 */

			if ( $item->object == 'zanmenu' || $item->enable_mega == 'yes' ) {
				global $zanmb;

				$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
				$sub_menu_ul_attrs = isset( $item->mega_width ) ? 'data-width="' . esc_attr( $item->mega_width ) . '"' : 'data-width="0"';
				$mega_item_custom_css_no_wrap = zmb_get_shortcode_custom_css_no_wrap( $item->object_id );
				$item_bg_color = get_post_meta( $item->object_id, '_zmb_bg_color', true );
				$item_style = trim( $item_bg_color ) != '' ? 'style="background-color: ' . esc_attr( $item_bg_color ) . ';"' : '';

				$item_custom_class = esc_attr( get_post_meta( $item->object_id, 'zmb_custom_class', true ) );

				$drop_direction = isset( $item->mega_item_drop_direction ) ? esc_attr( $item->mega_item_drop_direction ) : '';
				if ( $drop_direction != '' ) {
					$item_custom_class .= ' zmb-dop-direction-' . $drop_direction;
				}

				// Mega menu item is has parent
				if ( 0 != $depth ) {
					$item_custom_class .= ' zanmenu-has-parent';
				}

				$menu_content = '';

				$use_apply_filter_the_content = isset( $zanmb['apply_filter_the_content'] ) ? $zanmb['apply_filter_the_content'] == 1 : true;
				if ( $use_apply_filter_the_content ) {
					$menu_content = apply_filters( 'the_content', get_post_field( 'post_content', $item->object_id ) );
				}
				else {
					$menu_content = do_shortcode( get_post_field( 'post_content', $item->object_id ) );
				}

				$sub_menu_ul_attrs .= ' data-style="' . $mega_item_custom_css_no_wrap . '" data-mega-obj-id="' . esc_attr( $item->object_id ) . '"';
				$output .= '<div class="sub-menu zanmenu ' . $item_custom_class . '" ' . $sub_menu_ul_attrs . ' ' . $item_style . '><div class="zanmenu-inner"><div class="zanmenu-content ">' . $menu_content . '</div></div></div>';
			}
			else {

				$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );

			}
		}
	}

	/**
	 * Traverse elements to create list from elements.
	 *
	 * Display one element if the element doesn't have any children otherwise,
	 * display the element and its children. Will only traverse up to the max
	 * depth and no ignore elements under that depth.
	 *
	 * This method shouldn't be called directly, use the walk() method instead.
	 *
	 * @see   Walker::start_el()
	 * @since 2.5.0
	 *
	 * @param object $element           Data object
	 * @param array  $children_elements List of elements to continue traversing.
	 * @param int    $max_depth         Max depth to traverse.
	 * @param int    $depth             Depth of current element.
	 * @param array  $args
	 * @param string $output            Passed by reference. Used to append additional content.
	 *
	 * @return null Null on failure with no changes to parameters.
	 */
	public function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {
		if ( !$element )
			return;

		$id_field = $this->db_fields['id'];

		// Display this element.
		if ( is_object( $args[0] ) )
			$args[0]->has_children = !empty( $children_elements[$element->$id_field] );

		parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
	}

	/**
	 * Menu Fallback
	 * =============
	 * If this function is assigned to the wp_nav_menu's fallback_cb variable
	 * and a manu has not been assigned to the theme location in the WordPress
	 * menu manager the function with display nothing to a non-logged in user,
	 * and will add a link to the WordPress menu manager if logged in as an admin.
	 *
	 * @param array $args passed from the wp_nav_menu function.
	 *
	 */
	public static function fallback( $args ) {
		if ( current_user_can( 'manage_options' ) ) {

			extract( $args );

			$fb_output = null;

			if ( $container ) {
				$fb_output = '<' . $container;

				if ( $container_id )
					$fb_output .= ' id="' . $container_id . '"';

				if ( $container_class )
					$fb_output .= ' class="' . $container_class . '"';

				$fb_output .= '>';
			}

			$fb_output .= '<ul';

			if ( $menu_id )
				$fb_output .= ' id="' . $menu_id . '"';

			if ( $menu_class )
				$fb_output .= ' class="' . $menu_class . '"';

			$fb_output .= '>';
			$fb_output .= '<li><a href="' . esc_url( admin_url( 'nav-menus.php' ) ) . '">' . esc_html__( 'Add a menu', 'zanmb' ) . '</a></li>';
			$fb_output .= '</ul>';

			if ( $container )
				$fb_output .= '</' . $container . '>';

			echo $fb_output;
		}
	}
}
