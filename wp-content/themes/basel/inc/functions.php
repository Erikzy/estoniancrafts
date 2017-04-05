<?php if ( ! defined('BASEL_THEME_DIR')) exit('No direct script access allowed');

// **********************************************************************// 
// ! Body classes
// **********************************************************************// 

if( ! function_exists( 'basel_body_class' ) ) {
	function basel_body_class( $classes ) {

		$page_id = basel_page_ID();

		$site_width = basel_get_opt( 'site_width' );
		$cart_design = basel_get_opt( 'shopping_cart' );
		$wishlist = basel_get_opt( 'header_wishlist' );
		$header = basel_get_opt( 'header' );
		$header_overlap_opt = basel_get_opt( 'header-overlap' );
		$product_design = basel_product_design();
		$top_bar = basel_get_opt( 'top-bar' );
		$ajax_shop = basel_get_opt( 'ajax_shop' );
		$header_search = basel_get_opt( 'header_search' );
		$ajax_search = basel_get_opt( 'search_ajax' );
		$catalog_mode = basel_get_opt( 'catalog_mode' );
		$categories_toggle = basel_get_opt( 'categories_toggle' );
		$header = basel_get_opt( 'header' );
		$sticky_footer = basel_get_opt( 'sticky_footer' );

		$header_overlap = $header_sticky = $disable_sticky = false;

		$disable = get_post_meta( $page_id, '_basel_title_off', true );

		$classes[] = 'wrapper-' . $site_width;
		$classes[] = 'global-cart-design-' . $cart_design;
		$classes[] = 'global-search-' . $header_search;
		$classes[] = 'global-header-' . $header;

		if( is_singular( 'product') ) 
			$classes[] = 'basel-product-design-' . $product_design;
		
		$classes[] = ( $sticky_footer ) ? 'sticky-footer-on' : 'no-sticky-footer';

		if( $catalog_mode ) {
			$classes[] = 'catalog-mode-on';
		} else {
			$classes[] = 'catalog-mode-off';
		}

		if( $categories_toggle ) {
			$classes[] = 'categories-accordion-on';
		} else {
			$classes[] = 'categories-accordion-off';
		}

		if( $wishlist ) {
			$classes[] = 'global-wishlist-enable';
		} else {
			$classes[] = 'global-wishlist-disable';
		}

		if( $top_bar ) {
			$classes[] = 'basel-top-bar-on';
		} else {
			$classes[] = 'basel-top-bar-off';
		}

		if( $ajax_shop ) {
			$classes[] = 'basel-ajax-shop-on';
		} else {
			$classes[] = 'basel-ajax-shop-off';
		}

		if( $ajax_search ) {
			$classes[] = 'basel-ajax-search-on';
		} else {
			$classes[] = 'basel-ajax-search-off';
		}

		// Sticky header settings
		if( basel_get_opt('sticky_header') ) {
			$classes[] = 'enable-sticky-header';
			$header_sticky = true;
		} else {
			$disable_sticky = true;
			$classes[] = 'disable-sticky-header';
		}

		// Force header full width class
		if(  is_singular( 'product') && basel_get_opt('force_header_full_width') && basel_product_design() == 'sticky' ) {
			$classes[] = 'header-full-width';
		}

		if( basel_get_opt('header_full_width') ) {
			$classes[] = 'header-full-width';
		}

		if( in_array( $header, array('menu-top') ) ) {
			$header_sticky = 'real';
			$classes[] = 'sticky-navigation-only';
		} else if( in_array( $header, array('base', 'simple', 'logo-center', 'categories') ) ) {
			$header_sticky = 'clone';
		}

		// Header overlaps content in the following cases:
		// 1. Header type is overlap
		// 2. Not on the single product page
		// 3. Not shop page and not disabled page title
		/*if( $header == 'overlap' 
			&& ! is_singular( 'product' )
			&& ! ( basel_woocommerce_installed() 
					&& ( is_shop() || is_product_category() || is_product_tag() || is_singular( "product" ) ) 
					&& $disable
				)
		) {
			$header_overlap = true;
			$header_sticky = 'real';
		} */

		// If header type is SHOP and overlap option is enabled
		if( $header == 'shop' || $header == 'split' ) {
			$header_sticky = 'real';
			if( $header_overlap_opt ) {
				$header_overlap = true;
			}
		}

		if( $header == 'simple' && $header_overlap_opt ) {
			$header_overlap = true;
			$header_sticky = 'real';
		}

		/*if( $header == 'simple' && $header_sticky == 'real' && ! $header_overlap ) {
			$classes[] = 'basel-header-smooth';
		}*/

		if( $header_overlap ) {
			$classes[] = 'basel-header-overlap';
		}

		if( $header_sticky == 'clone' && ! $disable_sticky ) {
			$classes[] = 'sticky-header-clone';
		} elseif( $header_sticky && ! $disable_sticky ) {
			$classes[] = 'sticky-header-real';
		}

		return $classes;
	}

	add_filter('body_class', 'basel_body_class');
}

/**
 * ------------------------------------------------------------------------------------------------
 * Filter wp_title
 * ------------------------------------------------------------------------------------------------
 */

if( ! function_exists( 'basel_wp_title' ) ) {
	function basel_wp_title( $title, $sep ) {
		global $paged, $page;

		if ( is_feed() )
			return $title;

		// Add the site name.
		$title .= get_bloginfo( 'name' );

		// Add the site description for the home/front page.
		$site_description = get_bloginfo( 'description', 'display' );
		if ( $site_description && ( is_home() || is_front_page() ) )
			$title = "$title $sep $site_description";

		// Add a page number if necessary.
		if ( $paged >= 2 || $page >= 2 )
			$title = "$title $sep " . sprintf( esc_html__( 'Page %s', 'twentytwelve' ), max( $paged, $page ) );

		return $title;
	}
	add_filter( 'wp_title', 'basel_wp_title', 10, 2 );

}

/**
 * ------------------------------------------------------------------------------------------------
 * Remove admin bar
 * ------------------------------------------------------------------------------------------------
 */
if( ! function_exists( 'basel_remove_admin_bar' )) {
    function basel_remove_admin_bar() {	
		if( basel_get_opt( 'admin_bar' ) )
			add_filter('show_admin_bar', '__return_false');
    } 

    add_action( 'wp', 'basel_remove_admin_bar', 10 );
}



/**
 * ------------------------------------------------------------------------------------------------
 * Get predefined footer configuration by index
 * ------------------------------------------------------------------------------------------------
 */

if( ! function_exists( 'basel_get_footer_config' ) ) {
	function basel_get_footer_config( $index ) {

		if( $index > 20 || $index < 1) {
			$index = 1;
		}

		$configs = apply_filters( 'basel_footer_configs_array', array(
			1 => array(
				'cols' => array(
					'col-sm-12'
				),
				
			),
			2 => array(
				'cols' => array(
					'col-sm-6',
					'col-sm-6',
				),
			),
			3 => array(
				'cols' => array(
					'col-sm-4',
					'col-sm-4',
					'col-sm-4',
				),
			),
			4 => array(
				'cols' => array(
					'col-md-3 col-sm-6',
					'col-md-3 col-sm-6',
					'col-md-3 col-sm-6',
					'col-md-3 col-sm-6',
				),
				'clears' => array(
					2 => 'sm'
				)
			),
			5 => array(
				'cols' => array(
					'col-md-2 col-sm-4',
					'col-md-2 col-sm-4',
					'col-md-2 col-sm-4',
					'col-md-2 col-sm-4',
					'col-md-2 col-sm-4',
					'col-md-2 col-sm-4',
				),
				'clears' => array(
					3 => 'sm'
				)
			),
			6 => array(
				'cols' => array(
					'col-md-3 col-sm-4',
					'col-md-6 col-sm-4',
					'col-md-3 col-sm-4',
				),
			),
			7 => array(
				'cols' => array(
					'col-md-6 col-sm-4',
					'col-md-3 col-sm-4',
					'col-md-3 col-sm-4',
				),
			),
			8 => array(
				'cols' => array(
					'col-md-3 col-sm-4',
					'col-md-3 col-sm-4',
					'col-md-6 col-sm-4',
				),
			),
			9 => array(
				'cols' => array(
					'col-md-12 col-sm-12',
					'col-md-3 col-sm-6',
					'col-md-3 col-sm-6',
					'col-md-3 col-sm-6',
					'col-md-3 col-sm-6',
				),
				'clears' => array(
					1 => 'md',
					1 => 'lg',
					3 => 'sm',
				),
			),
			10 => array(
				'cols' => array(
					'col-md-6 col-sm-12',
					'col-md-6 col-sm-12',
					'col-md-3 col-sm-6',
					'col-md-3 col-sm-6',
					'col-md-3 col-sm-6',
					'col-md-3 col-sm-6',
				),
				'clears' => array(
					2 => 'md',
					2 => 'lg',
					4 => 'sm',
				),
			),
			11 => array(
				'cols' => array(
					'col-md-6 col-sm-12',
					'col-md-6 col-sm-12',
					'col-md-2 col-sm-6',
					'col-md-2 col-sm-6',
					'col-md-2 col-sm-6',
					'col-md-2 col-sm-6',
					'col-md-4 col-sm-12',
				),
				'clears' => array(
					2 => 'md',
					2 => 'lg',
					4 => 'sm',
				),
			),
			12 => array(
				'cols' => array(
					'col-md-12 col-sm-12',
					'col-md-2 col-sm-6',
					'col-md-2 col-sm-6',
					'col-md-2 col-sm-6',
					'col-md-2 col-sm-6',
					'col-md-4 col-sm-12',
				),
				'clears' => array(
					1 => 'md',
					1 => 'lg',
					3 => 'sm',
				),
			),
		) );

		return (isset( $configs[$index] )) ? $configs[$index] : array();
	}
}


// **********************************************************************// 
// ! Theme 3d plugins
// **********************************************************************// 


if(!defined('YITH_REFER_ID')) {
    define('YITH_REFER_ID', '1040314');
}


if( ! function_exists( 'basel_3d_plugins' )) {
    function basel_3d_plugins() {
        if( function_exists( 'set_revslider_as_theme' ) ){
            set_revslider_as_theme();
        }
    } 

    add_action( 'init', 'basel_3d_plugins' );
}

if( ! function_exists( 'basel_vcSetAsTheme' ) ) {

    function basel_vcSetAsTheme() {
        if( function_exists( 'vc_set_as_theme' ) ){
            vc_set_as_theme();
        }
    } 

    add_action( 'vc_before_init', 'basel_vcSetAsTheme' );
}


// **********************************************************************// 
// ! Function to get taxonomy meta data
// **********************************************************************// 

if( ! function_exists( 'basel_tax_data' ) ) {
	function basel_tax_data($taxonomy, $term_id, $meta_key) {
		$data = '';
		
		if( class_exists('Taxonomy_MetaData') )
			$data = Taxonomy_MetaData::get( $taxonomy, $term_id, $meta_key );

		return $data;
	}
}

// **********************************************************************// 
// ! Obtain real page ID (shop page, blog, portfolio or simple page)
// **********************************************************************// 

/**
 * This function is called once when initializing BASEL_Layout object
 * then you can use function basel_page_ID to get current page id
 */
if( ! function_exists( 'basel_get_the_ID' ) ) {
	function basel_get_the_ID( $settings = array() ) {
		global $post;

		$page_id = 0;

		$page_for_posts    = get_option( 'page_for_posts' );
		$page_for_shop     = get_option( 'woocommerce_shop_page_id' );
		$page_for_projects = basel_tpl2id( 'portfolio.php' );
		
		if(isset($post->ID)) $page_id = $post->ID;

		if( isset($post->ID) && ( is_singular( 'page' ) || is_singular( 'post' ) ) ) { 
			$page_id = $post->ID;
		} else if( is_home() || is_singular( 'post' ) || is_search() || is_tag() || is_category() || is_date() || is_author() ) {
			$page_id = $page_for_posts;
		} else if( is_archive('portfolio') && get_post_type() == 'portfolio' ) {
			$page_id = $page_for_projects;
		}

		if( basel_woocommerce_installed() && function_exists( 'is_shop' )  ) {
			if( isset( $settings['singulars'] ) && in_array( 'product', $settings['singulars']) && is_singular( "product" ) ) {
				// keep post id
			} else if( is_shop() || is_product_category() || is_product_tag() || is_singular( "product" ) )
				$page_id = $page_for_shop;
		}

		return $page_id;
	}
}


// **********************************************************************// 
// ! Function to get HTML block content
// **********************************************************************// 

if( ! function_exists( 'basel_get_html_block' ) ) {
	function basel_get_html_block($id) {
		$content = get_post_field('post_content', $id);

		$content = do_shortcode($content);


		$shortcodes_custom_css = get_post_meta( $id, '_wpb_shortcodes_custom_css', true );
		if ( ! empty( $shortcodes_custom_css ) ) {
			$content .= '<style type="text/css" data-type="vc_shortcodes-custom-css">';
			$content .= $shortcodes_custom_css;
			$content .= '</style>';
		}

		return $content;
	}

}

if( ! function_exists( 'basel_get_static_blocks_array' ) ) {
	function basel_get_static_blocks_array() {
		$args = array( 'posts_per_page' => 50, 'post_type' => 'cms_block' );
		$blocks_posts = get_posts( $args );
		$array = array();
		foreach ( $blocks_posts as $post ) : 
			setup_postdata( $post ); 
			$array[$post->post_title] = $post->ID; 
		endforeach;
		wp_reset_postdata();
		return $array;
	}
}


// **********************************************************************// 
// ! Support shortcodes in text widget
// **********************************************************************// 

add_filter('widget_text', 'do_shortcode');



// **********************************************************************// 
// ! Set excerpt length and more btn
// **********************************************************************// 

add_filter( 'excerpt_length', 'basel_excerpt_length', 999 );

if( ! function_exists( 'basel_excerpt_length' ) ) {
	function basel_excerpt_length( $length ) {
		return 20;
	}
}

add_filter('excerpt_more', 'basel_new_excerpt_more');

if( ! function_exists( 'basel_new_excerpt_more' ) ) {
	function basel_new_excerpt_more( $more ) {
		return '';
	}
}

// **********************************************************************// 
// ! Add scroll to top buttom 
// **********************************************************************// 

add_action( 'wp_footer', 'basel_scroll_top_btn' );

if( ! function_exists( 'basel_scroll_top_btn' ) ) {
	function basel_scroll_top_btn( $more ) {
		?>
			<a href="#" class="scrollToTop basel-tooltip"><?php esc_attr_e( 'Scroll To Top', 'basel' ); ?></a>
		<?php
	}
}


// **********************************************************************// 
// ! Return related posts args array
// **********************************************************************// 

if( ! function_exists( 'basel_get_related_posts_args' ) ) {
	function basel_get_related_posts_args( $post_id ) {
	    $taxs = wp_get_post_tags( $post_id );
	    $args = array();
	    if ( $taxs ) {
	        $tax_ids = array();
	        foreach( $taxs as $individual_tax ) $tax_ids[] = $individual_tax->term_id;
	         
	        $args = array(
	            'tag__in'               => $tax_ids,
	            'post__not_in'          => array( $post_id ),
	            'showposts'             => 12,
	            'ignore_sticky_posts'   => 1
	        );  
	        
	    }

	    return $args;
	}
}





// **********************************************************************// 
// ! Navigation walker
// **********************************************************************// 

if( ! class_exists( 'BASEL_Mega_Menu_Walker' )) {
	class BASEL_Mega_Menu_Walker extends Walker_Nav_Menu {

		private $color_scheme = 'dark';

		/**
		 * Starts the list before the elements are added.
		 *
		 * @see Walker::start_lvl()
		 *
		 * @since 3.0.0
		 *
		 * @param string $output Passed by reference. Used to append additional content.
		 * @param int    $depth  Depth of menu item. Used for padding.
		 * @param array  $args   An array of arguments. @see wp_nav_menu()
		 */
		public function start_lvl( &$output, $depth = 0, $args = array() ) {
			$indent = str_repeat("\t", $depth);

			if( $depth == 0) {
				$output .= "\n$indent<div class=\"sub-menu-dropdown color-scheme-" . $this->color_scheme . "\">\n";
				$output .= "\n$indent<div class=\"container\">\n";

			}
			if( $depth < 1 ) {
				$sub_menu_class = "sub-menu";
			} else {
				$sub_menu_class = "sub-sub-menu";
			}
			
			$output .= "\n$indent<ul class=\"$sub_menu_class color-scheme-" . $this->color_scheme . "\">\n";

			if( $this->color_scheme == 'light') $this->color_scheme = 'dark';
		}

		/**
		 * Ends the list of after the elements are added.
		 *
		 * @see Walker::end_lvl()
		 *
		 * @since 3.0.0
		 *
		 * @param string $output Passed by reference. Used to append additional content.
		 * @param int    $depth  Depth of menu item. Used for padding.
		 * @param array  $args   An array of arguments. @see wp_nav_menu()
		 */
		public function end_lvl( &$output, $depth = 0, $args = array() ) {
			$indent = str_repeat("\t", $depth);
			$output .= "$indent</ul>\n";
			if( $depth == 0) {
				$output .= "$indent</div>\n";
				$output .= "$indent</div>\n";
			}
		}

		/**
		 * Start the element output.
		 *
		 * @see Walker::start_el()
		 *
		 * @since 3.0.0
		 *
		 * @param string $output Passed by reference. Used to append additional content.
		 * @param object $item   Menu item data object.
		 * @param int    $depth  Depth of menu item. Used for padding.
		 * @param array  $args   An array of arguments. @see wp_nav_menu()
		 * @param int    $id     Current item ID.
		 */
		public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
			$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

			$classes = empty( $item->classes ) ? array() : (array) $item->classes;
			$classes[] = 'menu-item-' . $item->ID;

			$design   = $width = $height = $icon = $label = $label_out = '';
			$design   = get_post_meta( $item->ID, '_menu_item_design',   true );
			$width    = get_post_meta( $item->ID, '_menu_item_width',    true );
			$height   = get_post_meta( $item->ID, '_menu_item_height',   true );
			$icon     = get_post_meta( $item->ID, '_menu_item_icon',     true );
			$event    = get_post_meta( $item->ID, '_menu_item_event',    true );
			$label    = get_post_meta( $item->ID, '_menu_item_label',    true );
			$opanchor = get_post_meta( $item->ID, '_menu_item_opanchor', true );
			$color_scheme = get_post_meta( $item->ID, '_menu_item_colorscheme', true );

			if( $color_scheme == 'light' ) $this->color_scheme = 'light';

			if( empty($design) ) $design = 'default';
			$classes[] = 'menu-item-design-' . $design;

			$event = (empty($event)) ? 'hover' : $event;
			$classes[] = 'item-event-' . $event;

			if( $opanchor == 'enable' ) {
				 $classes[] = 'onepage-link';
				if(($key = array_search('current-menu-item', $classes)) !== false) {
					unset($classes[$key]);
				}
			}

			if( !empty( $label ) ) {
				$classes[] = 'item-with-label';
				$classes[] = 'item-label-' . $label;
				$label_text = '';
				switch ( $label ) {
					case 'hot':
						$label_text = esc_html__('Hot', 'basel');
					break;
					case 'sale':
						$label_text = esc_html__('Sale', 'basel');
					break;
					case 'new':
						$label_text = esc_html__('New', 'basel');
					break;
				}
				$label_out = '<span class="menu-label menu-label-' . $label . '">' . esc_attr( $label_text ) . '</span>';
			}


			/**
			 * Filter the CSS class(es) applied to a menu item's list item element.
			 *
			 * @since 3.0.0
			 * @since 4.1.0 The `$depth` parameter was added.
			 *
			 * @param array  $classes The CSS classes that are applied to the menu item's `<li>` element.
			 * @param object $item    The current menu item.
			 * @param array  $args    An array of {@see wp_nav_menu()} arguments.
			 * @param int    $depth   Depth of menu item. Used for padding.
			 */
			$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
			$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

			/**
			 * Filter the ID applied to a menu item's list item element.
			 *
			 * @since 3.0.1
			 * @since 4.1.0 The `$depth` parameter was added.
			 *
			 * @param string $menu_id The ID that is applied to the menu item's `<li>` element.
			 * @param object $item    The current menu item.
			 * @param array  $args    An array of {@see wp_nav_menu()} arguments.
			 * @param int    $depth   Depth of menu item. Used for padding.
			 */
			$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args, $depth );
			$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

			$output .= $indent . '<li' . $id . $class_names .'>';

			$atts = array();
			$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
			$atts['target'] = ! empty( $item->target )     ? $item->target     : '';
			$atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
			$atts['href']   = ! empty( $item->url )        ? $item->url        : '';

			/**
			 * Filter the HTML attributes applied to a menu item's anchor element.
			 *
			 * @since 3.6.0
			 * @since 4.1.0 The `$depth` parameter was added.
			 *
			 * @param array $atts {
			 *     The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
			 *
			 *     @type string $title  Title attribute.
			 *     @type string $target Target attribute.
			 *     @type string $rel    The rel attribute.
			 *     @type string $href   The href attribute.
			 * }
			 * @param object $item  The current menu item.
			 * @param array  $args  An array of {@see wp_nav_menu()} arguments.
			 * @param int    $depth Depth of menu item. Used for padding.
			 */
			$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

			$attributes = '';
			foreach ( $atts as $attr => $value ) {
				if ( ! empty( $value ) ) {
					$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
					$attributes .= ' ' . $attr . '="' . $value . '"';
				}
			}

			$item_output = $args->before;
			$item_output .= '<a'. $attributes .'>';
			if($icon != '') {
				$item_output .= '<i class="fa fa-' . $icon . '"></i>';
			}
			/** This filter is documented in wp-includes/post-template.php */
			$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
			$item_output .= $label_out;
			$item_output .= '</a>';
			$item_output .= $args->after;

			$styles = '';

			if( $depth == 0) {
				/**
				 * Add background image to dropdown
				 **/


				if( has_post_thumbnail( $item->ID ) ) {
					$post_thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $item->ID ), 'full' );

					//ar($post_thumbnail);

					$styles .= '.menu-item-' . $item->ID . ' > .sub-menu-dropdown {';
						$styles .= 'background-image: url(' . $post_thumbnail[0] .'); ';
					$styles .= '}';
				}

				if( ! empty( $item->description ) && !in_array("menu-item-has-children", $item->classes) ) {
					$item_output .= "\n$indent<div class=\"sub-menu-dropdown color-scheme-" . $this->color_scheme . "\">\n";
					$item_output .= "\n$indent<div class=\"container\">\n";
						$item_output .= do_shortcode( $item->description );
					$item_output .= "\n$indent</div>\n";
					$item_output .= "\n$indent</div>\n";
				}
			}

			if($design == 'sized' && !empty($height) && !empty($width)) {
				$styles .= '.menu-item-' . $item->ID . ' > .sub-menu-dropdown {';
					$styles .= 'min-height: ' . $height .'px; ';
					$styles .= 'width: ' . $width .'px; ';
				$styles .= '}';
			}


			if( $styles != '' ) {
				$item_output .= '<style type="text/css">';
				$item_output .= $styles;
				$item_output .= '</style>';
			}

			/**
			 * Filter a menu item's starting output.
			 *
			 * The menu item's starting output only includes `$args->before`, the opening `<a>`,
			 * the menu item's title, the closing `</a>`, and `$args->after`. Currently, there is
			 * no filter for modifying the opening and closing `<li>` for a menu item.
			 *
			 * @since 3.0.0
			 *
			 * @param string $item_output The menu item's starting HTML output.
			 * @param object $item        Menu item data object.
			 * @param int    $depth       Depth of menu item. Used for padding.
			 * @param array  $args        An array of {@see wp_nav_menu()} arguments.
			 */
			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		}
	}
}



// **********************************************************************// 
// ! // Deletes first gallery shortcode and returns content (http://stackoverflow.com/questions/17224100/wordpress-remove-shortcode-and-save-for-use-elsewhere)
// **********************************************************************// 

if( ! function_exists( 'basel_strip_shortcode_gallery' ) ) {
	function  basel_strip_shortcode_gallery( $content ) {
	    preg_match_all( '/'. get_shortcode_regex() .'/s', $content, $matches, PREG_SET_ORDER );
	    if ( ! empty( $matches ) ) {
	        foreach ( $matches as $shortcode ) {
	            if ( 'gallery' === $shortcode[2] ) {
	                $pos = strpos( $content, $shortcode[0] );
	                if ($pos !== false)
	                    return substr_replace( $content, '', $pos, strlen($shortcode[0]) );
	            }
	        }
	    }
	    return $content;
	}
}


// **********************************************************************// 
// ! Get exceprt from post content
// **********************************************************************// 

if( ! function_exists( 'basel_excerpt_from_content' ) ) {
	function basel_excerpt_from_content($post_content, $limit, $shortcodes = '') {
        // Strip shortcodes and HTML tags
        if ( empty( $shortcodes )) {
	        $post_content = preg_replace("/\[caption(.*)\[\/caption\]/i", '', $post_content);
            $post_content = preg_replace('`\[[^\]]*\]`','',$post_content);
        }

        $post_content = stripslashes(wp_filter_nohtml_kses($post_content));

        $excerpt = explode(' ', $post_content, $limit);

        if ( count( $excerpt) >= $limit ) {
            array_pop( $excerpt );
            $excerpt = implode( " ", $excerpt ) . '...';
        } else {
            $excerpt = implode( " ", $excerpt );
        }

        $excerpt = strip_tags( $excerpt );

        if (trim($excerpt) == '...') {
            return '';
        }

        return $excerpt;
    }
}

// **********************************************************************// 
// ! Get portfolio taxonomies dropdown
// **********************************************************************// 

if( ! function_exists( 'basel_get_projects_cats_array') ) {
	function basel_get_projects_cats_array() {
		$return = array('All' => '');

		if( ! post_type_exists( 'portfolio' ) ) return array();

		$cats = get_terms( 'project-cat' );

		foreach ($cats as $key => $cat) {
			$return[$cat->name] = $cat->term_id;
		}

		return $return;
	}
}

// **********************************************************************// 
// ! Get menus dropdown
// **********************************************************************// 

if( ! function_exists( 'basel_get_menus_array') ) {
	function basel_get_menus_array() {
		$basel_menus = wp_get_nav_menus();
		$basel_menu_dropdown = array();
		
		foreach ( $basel_menus as $menu ) {

			$basel_menu_dropdown[$menu->term_id] = $menu->name;
			
		}

		return $basel_menu_dropdown;
	}
}


// **********************************************************************// 
// ! Get registered sidebars dropdown
// **********************************************************************// 

if(!function_exists('basel_get_sidebars_array')) {
    function basel_get_sidebars_array() {
        global $wp_registered_sidebars;
        $sidebars['none'] = 'none';
        foreach( $wp_registered_sidebars as $id=>$sidebar ) {
            $sidebars[ $id ] = $sidebar[ 'name' ];
        }
        return $sidebars;
    }
}


// **********************************************************************// 
// ! If page needs header
// **********************************************************************// 

if( ! function_exists( 'basel_needs_header' ) ) {
	function basel_needs_header() {
		return ( ! basel_maintenance_page() );
	}
}

// **********************************************************************// 
// ! If page needs footer
// **********************************************************************// 

if( ! function_exists( 'basel_needs_footer' ) ) {
	function basel_needs_footer() {
		return ( ! basel_maintenance_page() );
	}
}

// **********************************************************************// 
// ! Is maintenance page
// **********************************************************************// 

if( ! function_exists( 'basel_maintenance_page' ) ) {
	function basel_maintenance_page() {
		
        $pages_ids = basel_pages_ids_from_template( 'maintenance' );

        if( ! empty( $pages_ids ) && is_page( $pages_ids ) ) {
        	return true;
        }

		return false;
	}
}


// **********************************************************************// 
// ! Get page id by template name
// **********************************************************************// 

if( ! function_exists( 'basel_pages_ids_from_template' ) ) {
	function basel_pages_ids_from_template( $name ) {
		$pages = get_pages(array(
		    'meta_key' => '_wp_page_template',
		    'meta_value' => $name . '.php'
		));

		$return = array();

		foreach($pages as $page){
		    $return[] = $page->ID;
		}

		return $return;
	}
}


// **********************************************************************// 
// ! Get config file
// **********************************************************************// 

if( ! function_exists( 'basel_get_config' ) ) {
	function basel_get_config( $name ) {
		// $allowed = array('selectors', 'versions', 'base-options', 'widgets-import', 'specific-options', 'product-hovers');
		$path = BASEL_CONFIGS . '/' . $name . '.php';
		if( file_exists( $path ) ) { // && in_array($name, $allowed) 
			return include $path;
		} else {
			return array();
		}
	}
}


// **********************************************************************// 
// ! Text to one-line string
// **********************************************************************// 

if( ! function_exists( 'basel_text2line')) {
	function basel_text2line( $str ) {
        return trim(preg_replace("/('|\"|\r?\n)/", '', $str)); 
	}
}


// **********************************************************************// 
// ! Get page ID by it's template name
// **********************************************************************// 
if( ! function_exists( 'basel_tpl2id' ) ) {
	function basel_tpl2id( $tpl = '' ) {
		$pages = get_pages(array(
		    'meta_key' => '_wp_page_template',
		    'meta_value' => $tpl
		));
		foreach($pages as $page){
		    return $page->ID;
		}
	}
}

// **********************************************************************// 
// ! Function print array within a pre tags
// **********************************************************************// 
if( ! function_exists( 'ar' ) ) {
	function ar($array) {

		echo '<pre>';
			print_r($array);
		echo '</pre>';

	}
}


// **********************************************************************// 
// ! Get protocol (http or https)
// **********************************************************************// 
if( ! function_exists( 'basel_http' )) {
	function basel_http() {
		if( ! is_ssl() ) {
			return 'http';
		} else {
			return 'https';
		}
	}
}


// **********************************************************************// 
// ! It could be useful if you using nginx instead of apache 
// **********************************************************************// 

if (!function_exists('getallheaders')) { 
    function getallheaders() { 
		$headers = ''; 
		foreach ($_SERVER as $name => $value) { 
			if (substr($name, 0, 5) == 'HTTP_') { 
				$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value; 
			} 
		} 
		return $headers; 
    } 
} 