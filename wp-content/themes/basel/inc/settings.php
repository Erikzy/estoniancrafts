<?php if ( ! defined('BASEL_THEME_DIR')) exit('No direct script access allowed');

/**
 * ------------------------------------------------------------------------------------------------
 * Init Theme Settings and Options with Redux plugin
 * ------------------------------------------------------------------------------------------------
 */

    if ( ! class_exists( 'Redux' ) ) {
        return;
    }

    $opt_name = "basel_options";

    $basel_selectors = basel_get_config( 'selectors' );

    $theme = wp_get_theme(); // For use with some settings. Not necessary.

    $args = array(
        'opt_name'             => $opt_name,
        'display_name'         => $theme->get( 'Name' ),
        'display_version'      => $theme->get( 'Version' ),
        'menu_type'            => 'menu',
        'allow_sub_menu'       => true,
        'menu_title'           => __( 'Theme Settings', 'redux-framework-demo' ),
        'page_title'           => __( 'Theme Settings', 'redux-framework-demo' ),
        'google_api_key'       => '',
        'google_update_weekly' => false,
        'async_typography'     => false,
        'admin_bar'            => true,
        'admin_bar_icon'       => 'dashicons-portfolio',
        'admin_bar_priority'   => 50,
        'global_variable'      => '',
        'dev_mode'             => false,
        'update_notice'        => true,
        'customizer'           => true,
        'page_priority'        => 61,
        'page_parent'          => 'themes.php',
        'page_permissions'     => 'manage_options',
        'menu_icon'            => BASEL_ASSETS . '/images/theme-admin-icon.png', 
        'last_tab'             => '',
        'page_icon'            => 'icon-themes',
        'page_slug'            => '_options',
        'save_defaults'        => true,
        'default_show'         => false,
        'default_mark'         => '',
        'show_import_export'   => true,
        'transient_time'       => 60 * MINUTE_IN_SECONDS,
        'output'               => true,
        'output_tag'           => true,
        'footer_credit'     =>  '1.0',                  
        'database'             => '',
        'system_info'          => false,
        'hints'                => array(
            'icon'          => 'el el-question-sign',
            'icon_position' => 'right',
            'icon_color'    => 'lightgray',
            'icon_size'     => 'normal',
            'tip_style'     => array(
                'color'   => 'light',
                'shadow'  => true,
                'rounded' => false,
                'style'   => '',
            ),
            'tip_position'  => array(
                'my' => 'top left',
                'at' => 'bottom right',
            ),
            'tip_effect'    => array(
                'show' => array(
                    'effect'   => 'slide',
                    'duration' => '500',
                    'event'    => 'mouseover',
                ),
                'hide' => array(
                    'effect'   => 'slide',
                    'duration' => '500',
                    'event'    => 'click mouseleave',
                ),
            ),
        )
    );

    Redux::setArgs( $opt_name, $args );


    Redux::setSection( $opt_name, array(
        'title' => 'General',
        'id' => 'general',
        'icon' => 'el-icon-home',
        'fields' => array (
            array (
                'id' => 'favicon',
                'type' => 'media',
                'desc' => 'Upload image: png, ico',
                'operator' => 'and',
                'title' => 'Favicon image',
            ),
            array (
                'id' => 'favicon_retina',
                'type' => 'media',
                'desc' => 'Upload image: png, ico',
                'operator' => 'and',
                'title' => 'Favicon retina image',
            ),
            array (
                'id'       => 'admin_bar',
                'type'     => 'switch',
                'title'    => __('Disable admin bar', 'basel'), 
                'subtitle' => __('You can disable admin bar on front end', 'basel'),
                'default' => false
            ),
            array (
                'id'       => 'page_comments',
                'type'     => 'switch',
                'title'    => __('Show comments on page', 'basel'), 
                'default' => true
            ),
            array (
                'id'       => 'google_map_api_key',
                'type'     => 'text',
                'title'    => __('Google map API key', 'basel'), 
                'subtitle' => __('Obrain API key <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">here</a> to use our Google Map VC element.', 'basel')
            ),
            array (
                'id'       => 'search_post_type',
                'type'     => 'select',
                'title'    => __('Search post type', 'basel'), 
                'subtitle' => __('You can set up site search for posts or for products (woocommerce)', 'basel'),
                'options'  => array(
                    'product' => 'Product',
                    'post' => 'Post',
                ),
                'default' => 'product'
            ),
        ),
    ) );

    Redux::setSection( $opt_name, array(
        'title' => 'General Layout',
        'id' => 'layout',
        'icon' => 'el-icon-website',
        'fields' => array (
            array (
                'id'       => 'site_width',
                'type'     => 'select',
                'title'    => __('Site width', 'basel'), 
                'subtitle' => __('You can make your content wrapper boxed or full width', 'basel'),
                'options'  => array(
                    'full-width' => 'Full width',
                    'boxed' => 'Boxed',
                    'full-width-content' => 'Content full width',
                    'wide' => 'Wide (1600 px)',
                ),
                'default' => 'full-width'
            ),
            array (
                'id'       => 'main_layout',
                'type'     => 'image_select',
                'title'    => __('Main Layout', 'basel'), 
                'subtitle' => __('Select main content and sidebar alignment.', 'basel'),
                'options'  => array(
                    'full-width'      => array(
                        'alt'   => '1 Column', 
                        'img'   => ReduxFramework::$_url.'assets/img/1col.png'
                    ),
                    'sidebar-left'      => array(
                        'alt'   => '2 Column Left', 
                        'img'   => ReduxFramework::$_url.'assets/img/2cl.png'
                    ),
                    'sidebar-right'      => array(
                        'alt'   => '2 Column Right', 
                        'img'  => ReduxFramework::$_url.'assets/img/2cr.png'
                    ),
                ),
                'default' => 'sidebar-right'
            ),
            array (
                'id'       => 'sidebar_width',
                'type'     => 'button_set',
                'title'    => __('Sidebar size', 'basel'), 
                'subtitle' => __('You can set different sizes for your pages sidebar', 'basel'),
                'options'  => array(
                    2 => 'small', 
                    3 => 'medium', 
                    4 => 'large'
                ),
                'default' => 3
            ),
            array (         
                'id'       => 'body-background',
                'type'     => 'background',
                'title'    => __('Site background', 'basel'),
                'subtitle' => __('Set background image or color for body. Only for boxed layout', 'basel'),
                'output'   => array('body')
            ),
        ),
    ) );

    Redux::setSection( $opt_name, array(
        'title' => 'Page heading',
        'id' => 'page_titles',
        'icon' => 'el-icon-check',
        'fields' => array (
            array (
                'id'       => 'page-title-design',
                'type'     => 'button_set',
                'title'    => __('Page title design', 'basel'), 
                'options'  => array(
                    'default' => 'default', 
                    'centered' => 'centered', 
                    'disable' => 'disable'
                ),
                'default' => 'centered'
            ),
            array (
                'id'       => 'breadcrumbs',
                'type'     => 'switch',
                'title'    => __('Show breadcrumbs', 'basel'), 
                'subtitle' => __('Displays a full chain of links to the current page.', 'basel'),
                'default' => true
            ),
            array (         
                'id'       => 'title-background',
                'type'     => 'background',
                'title'    => __('Pages heading background', 'basel'),
                'subtitle' => __('Set background image or color, that will be used as a default for all page titles, shop page and blog.', 'basel'),
                'desc'     => __('You can also specify other image for particular page', 'basel'),
                'output'   => array('.page-title-default'),
                'default'  => array(
                    'background-color' => '#212121'
                )
            ),
            array (
                'id'       => 'page-title-size',
                'type'     => 'button_set',
                'title'    => __('Page title size', 'basel'), 
                'subtitle' => __('You can set different sizes for your pages titles', 'basel'),
                'options'  => array(
                    'default' => 'default', 
                    'small' => 'small', 
                    'large' => 'large'
                ),
                'default' => 'small'
            ),
            array (
                'id'       => 'page-title-color',
                'type'     => 'button_set',
                'title'    => __('Text color for page title', 'basel'), 
                'subtitle' => __('You can set different colors depending on it\'s background. May be light or dark', 'basel'),
                'options'  => array(
                    'default' => 'default', 
                    'light' => 'light', 
                    'dark' => 'dark'
                ),
                'default' => 'light'
            ),
        ),
    ) );

    Redux::setSection( $opt_name, array(
        'title' => 'Header',
        'id' => 'header',
        'icon' => 'el-icon-wrench'
    ) );

    Redux::setSection( $opt_name, array(
        'title' => 'Logo',
        'id' => 'header-logo',
        'subsection' => true,
        'fields' => array (
            array (
                'id' => 'logo',
                'type' => 'media',
                'desc' => 'Upload image: png, jpg or gif file',
                'operator' => 'and',
                'title' => 'Logo image',
            ),
            array (
                'id' => 'logo-white',
                'type' => 'media',
                'desc' => 'Upload image: png, jpg or gif file',
                'operator' => 'and',
                'title' => 'Logo image - white',
            ),
            array(
                'id'        => 'logo_width',
                'type'      => 'slider',
                'title'     => __('Logo container width', 'basel'),
                'desc'      => __('Set width for logo area in the header. In percentages', 'basel'),
                "default"   => 20,
                "min"       => 1,
                "step"      => 1,
                "max"       => 50,
                'display_value' => 'label'
            ),
            array(
                'id'        => 'logo_img_width',
                'type'      => 'slider',
                'title'     => __('Logo image maximum width', 'basel'),
                'desc'      => __('Set maximum width for logo image in the header. In pixels', 'basel'),
                "default"   => 250,
                "min"       => 50,
                "step"      => 1,
                "max"       => 600,
                'display_value' => 'label'
            ),
        )
    ) );


    Redux::setSection( $opt_name, array(
        'title' => 'Top bar',
        'id' => 'header-topbar',
        'subsection' => true,
        'fields' => array (
            array(
                'id'       => 'top-bar',
                'type'     => 'switch',
                'title'    => __('Top bar', 'basel'), 
                'subtitle' => __('Information about the header', 'basel'),
                'default'  => true,
            ),
            array (
                'id'       => 'top-bar-color',
                'type'     => 'select',
                'title'    => __('Top bar text color', 'basel'), 
                'options'  => array(
                    'dark' => 'Dark', 
                    'light' => 'Light', 
                ),
                'default' => 'light'
            ),
            array(
                'id'       => 'top-bar-bg',
                'type'     => 'background',
                'title'    => __('Top bar background', 'basel'), 
                'output'   => '.topbar-wrapp',
                'default'  => array(
                    'background-color' => '#1aada3'
                )
            ),
            array (
                'id'       => 'header_text',
                'type'     => 'text',
                'title'    => __('Text in the header top bar', 'basel'), 
                'subtitle' => __('Place here text you want to see in the header top bar. You can use shortocdes.<br> Ex.: [social_buttons]', 'basel'),
                'default' => 'FREE STANDARD SHIPPING ON ALL ORDERS ABOVE £40'
            ),
        ),
    ) );

    Redux::setSection( $opt_name, array(
        'title' => 'Header Layout',
        'id' => 'header-header',
        'subsection' => true,
        'fields' => array (
            array(
                'id'       => 'header_full_width',
                'type'     => 'switch',
                'title'    => __('Full Width', 'basel'), 
                'subtitle' => __('Make header full width', 'basel'),
                'default'  => true,
                'required' => array(
                    array('header','!=', array('vertical')),
                )
            ),
            array(
                'id'       => 'sticky_header',
                'type'     => 'switch',
                'title'    => __('Sticky Header', 'basel'), 
                'subtitle' => __('Enable/disable sticky header option', 'basel'),
                'default'  => true
            ),
            array (
                'id'       => 'header',
                'type'     => 'image_select',
                'title'    => __('Header', 'basel'), 
                'subtitle' => __('Set your header design', 'basel'),
                'options'  => array(
                    'shop' => array(
                        'title' => 'E-Commerce',
                        'img' => BASEL_ASSETS_IMAGES . '/settings/header-shop.png',
                    ), 
                    'base' => array(
                        'title' => 'Base header',
                        'img' => BASEL_ASSETS_IMAGES . '/settings/header-base.png',
                    ), 
                    'simple' => array(
                        'title' => 'Simplified',
                        'img' => BASEL_ASSETS_IMAGES . '/settings/header-simple.png',
                    ), 
                    'split' => array(
                        'title' => 'Double menu',
                        'img' => BASEL_ASSETS_IMAGES . '/settings/header-split.png',
                    ), 
                    'logo-center' => array(
                        'title' => 'Logo center',
                        'img' => BASEL_ASSETS_IMAGES . '/settings/header-logo-center.png',
                    ), 
                    'categories' => array(
                        'title' => 'With categories menu',
                        'img' => BASEL_ASSETS_IMAGES . '/settings/header-categories.png',
                    ), 
                    'menu-top' => array(
                        'title' => 'Menu in top bar',
                        'img' => BASEL_ASSETS_IMAGES . '/settings/header-menu-top.png',
                    ), 
                    'vertical' => array(
                        'title' => 'Vertical',
                        'img' => BASEL_ASSETS_IMAGES . '/settings/header-vertical.png',
                    ), 
                ),
                'default' => 'shop'
            ),
            array(
                'id'       => 'header-overlap',
                'type'     => 'switch',
                'title'    => __('Header above the content', 'basel'), 
                'subtitle' => __('Overlap page content with this header (header is transparent).', 'basel'),
                'default'  => false,
                'required' => array(
                     array('header','equals', array('simple','shop','split')),
                )
            ),
            array(
                'id'        => 'right_column_width',
                'type'      => 'slider',
                'title'     => __('Right column width', 'basel'),
                'desc'      => __('Set width for icons and links area in the header (shopping cart, wishlist, search). In pixels', 'basel'),
                "default"   => 100,
                "min"       => 30,
                "step"      => 1,
                "max"       => 450,
                'display_value' => 'label',
                'required' => array(
                    array('header','!=', array('vertical')),
                )
            ),
            array(
                'id'        => 'header_height',
                'type'      => 'slider',
                'title'     => __('Header height', 'basel'),
                "default"   => 105,
                "min"       => 40,
                "step"      => 1,
                "max"       => 220,
                'display_value' => 'label',
                'required' => array(
                    array('header','!=', array('vertical')),
                )
            ),
            array(
                'id'        => 'sticky_header_height',
                'type'      => 'slider',
                'title'     => __('Sticky header height', 'basel'),
                "default"   => 75,
                "min"       => 40,
                "step"      => 1,
                "max"       => 180,
                'display_value' => 'label',
                'required' => array(
                    array('header','!=', array('vertical')),
                )
            ),
            array(
                'id'        => 'mobile_header_height',
                'type'      => 'slider',
                'title'     => __('Mobile header height', 'basel'),
                "default"   => 60,
                "min"       => 40,
                "step"      => 1,
                "max"       => 120,
                'display_value' => 'label'
            ),
        ),
    ) );

    Redux::setSection( $opt_name, array(
        'title' => 'Shopping cart widget',
        'id' => 'header-cart',
        'subsection' => true,
        'fields' => array (
            array (
                'id'       => 'cart_position',
                'type'     => 'select',
                'title'    => __('Shopping cart position', 'basel'), 
                'subtitle' => __('Shopping cart widget may be placed in the header or as a sidebar.', 'basel'),
                'options'  => array(
                    'side' => 'Hidden sidebar', 
                    'dropdown' => 'Dropdown widget in header', 
                ),
                'default' => 'side'
            ),
            array (
                'id'       => 'shopping_cart',
                'type'     => 'select',
                'title'    => __('Shopping cart', 'basel'), 
                'subtitle' => __('Set your shopping cart widget design in the header', 'basel'),
                'options'  => array(
                    1 => 'Design 1', 
                    2 => 'Design 2', 
                    3 => 'Design 3', 
                    'disable' => 'Disable'
                ),
                'default' => 1
            ),
            array (
                'id'       => 'shopping_icon_alt',
                'type'     => 'switch',
                'title'    => __('Alternative shopping cart icon', 'basel'), 
                'subtitle' => __('Use alternative cart icon in header icons links', 'basel'),
                'default' => 0
            ),
        ),
    ) );

    Redux::setSection( $opt_name, array(
        'title' => 'Appearance',
        'id' => 'header-style',
        'subsection' => true,
        'fields' => array (
            array (
                'id'       => 'header_color_scheme',
                'type'     => 'select',
                'title'    => __('Header text color', 'basel'), 
                'subtitle' => __('You can change colors of links and icons for the header', 'basel'),
                'options'  => array(
                    'dark' => 'Dark', 
                    'light' => 'Light', 
                ),
                'default' => 'dark'
            ),
            array(
                'id'       => 'header_background',
                'type'     => 'background',
                'title'    => __('Header background', 'basel'), 
                'output'    => '.main-header, .sticky-header.header-clone, .header-spacing'
            ),
            array( 
                'id'       => 'header-border',
                'type'     => 'border',
                'title'    => __('Header Border', 'basel'),
                'output'   => array('.main-header'),
                'subtitle'     => __('Border bottom for the header.', 'basel'),
                'top'      => false,
                'left'     => false,
                'right'    => false,
            ),
            array (
                'id'       => 'icons_design',
                'type'     => 'select',
                'title'    => __('Icons font for header icons', 'basel'), 
                'subtitle' => __('Choose between two icon fonts: Font Awesome and Line Icons', 'basel'),
                'options'  => array(
                    'line' => 'Line Icons', 
                    'fontawesome' => 'Font Awesome', 
                ),
                'default' => 'line'
            ),
        ),
    ) );


    Redux::setSection( $opt_name, array(
        'title' => 'Main menu',
        'id' => 'header-menu',
        'subsection' => true,
        'fields' => array (
            array (
                'id'       => 'menu_align',
                'type'     => 'button_set',
                'title'    => __('Main menu align', 'basel'), 
                'subtitle' => __('Set menu text position on some headers', 'basel'),
                'options'  => array(
                    'left' => 'Left', 
                    'center' => 'Center', 
                    'right' => 'Right', 
                ),
                'default' => 'left'
            ),
        ),
    ) );

    Redux::setSection( $opt_name, array(
        'title' => 'Other',
        'id' => 'header-other',
        'subsection' => true,
        'fields' => array (
            array(
                'id'       => 'categories-menu',
                'type'     => 'select',
                'title'    => __('Categories menu', 'basel'), 
                'subtitle' => __('Use your custom menu as a categories navigation for particular headers.', 'basel'),
                'data'     => 'menus'
            ),
            array (
                'id'       => 'header_area',
                'type'     => 'textarea',
                'title'    => __('Text in the header', 'basel'), 
                'subtitle' => __('You can place here some advertisement or phone numbers. You can use shortcode to place here HTML block [html_block id=""]', 'basel'),
                'default' => ''
            ),
            array (
                'id'       => 'header_search',
                'type'     => 'button_set',
                'title'    => __('Search widget', 'basel'), 
                'subtitle'    => __('Display search icon in the header in different views', 'basel'), 
                'options'  => array(
                    'dropdown' => 'dropdown', 
                    'full-screen' => 'full screen', 
                    'disable' => 'disable'
                ),
                'default' => 'full-screen'
            ),
            array (
                'id'       => 'search_ajax',
                'type'     => 'switch',
                'title'    => __('AJAX Search', 'basel'), 
                'default' => 1
            ),
            array (
                'id'       => 'header_wishlist',
                'type'     => 'switch',
                'title'    => __('Display wishlist icon', 'basel'), 
                'default' => 1
            ),
        ),
    ) );

    Redux::setSection( $opt_name, array(
        'title' => 'Footer',
        'id' => 'footer',
        'icon' => 'el-icon-photo',
        'fields' => array (
            array(
                'id'       => 'footer-layout',
                'type'     => 'image_select',
                'title'    => __('Footer layout', 'basel'), 
                'subtitle' => __('Choose your footer layout. Depending on columns number you will have different number of widget areas for footer in Appearance->Widgets', 'basel'),
                'options'  => array(
                    1 => array(
                        'title' => 'Single Column',
                        'img' => BASEL_ASSETS_IMAGES . '/settings/footer-1.png'
                    ), 
                    2 => array(
                        'title' => 'Two Columns',
                        'img' => BASEL_ASSETS_IMAGES . '/settings/footer-2.png'
                    ), 
                    3 => array(
                        'title' => 'Three Columns',
                        'img' => BASEL_ASSETS_IMAGES . '/settings/footer-3.png'
                    ), 
                    4 => array(
                        'title' => 'Four Columns',
                        'img' => BASEL_ASSETS_IMAGES . '/settings/footer-4.png'
                    ), 
                    5 => array(
                        'title' => 'Six Columns',
                        'img' => BASEL_ASSETS_IMAGES . '/settings/footer-5.png'
                    ), 
                    6 => array(
                        'title' => '1/4 + 1/2 + 1/4',
                        'img' => BASEL_ASSETS_IMAGES . '/settings/footer-6.png'
                    ), 
                    7 => array(
                        'title' => '1/2 + 1/4 + 1/4',
                        'img' => BASEL_ASSETS_IMAGES . '/settings/footer-7.png'
                    ), 
                    8 => array(
                        'title' => '1/4 + 1/4 + 1/2', 
                        'img' => BASEL_ASSETS_IMAGES . '/settings/footer-8.png'
                    ),
                    9 => array(
                        'title' => 'Two rows', 
                        'img' => BASEL_ASSETS_IMAGES . '/settings/footer-9.png'
                    ),
                    10 => array(
                        'title' => 'Two rows', 
                        'img' => BASEL_ASSETS_IMAGES . '/settings/footer-10.png'
                    ), 
                    11 => array(
                        'title' => 'Two rows', 
                        'img' => BASEL_ASSETS_IMAGES . '/settings/footer-11.png'
                    ),  
                    12 => array(
                        'title' => 'Two rows', 
                        'img' => BASEL_ASSETS_IMAGES . '/settings/footer-12.png'
                    ), 
                ),
                'default' => 12
            ),
            array(
                'id'       => 'footer-style',
                'type'     => 'select',
                'title'    => __('Footer text color', 'basel'), 
                'subtitle' => __('Choose your footer color scheme', 'basel'),
                'options'  => array(
                    'dark' => 'Dark', 
                    'light' => 'Light', 
                ),
                'default' => 'light'
            ),
            array(
                'id'       => 'footer-bar-bg',
                'type'     => 'background',
                'title'    => __('Footer background', 'basel'), 
                'output'    => '.footer-container',
                'default'  => array(
                    'background-color' => '#000000'
                )
            ),
            array(
                'id'       => 'copyrights-layout',
                'type'     => 'select',
                'title'    => __('Copyrights layout', 'basel'), 
                'options'  => array(
                    'two-columns' => 'Two columns', 
                    'centered' => 'Centered', 
                ),
                'default' => 'centered'
            ),
            array (
                'id'       => 'copyrights',
                'type'     => 'text',
                'title'    => __('Copyrights', 'basel'), 
                'subtitle' => __('Place here text you want to see in the copyrights area. You can use shortocdes.<br> Ex.: [social_buttons]', 'basel'),
                'default' => ''
            ),
            array (
                'id'       => 'copyrights2',
                'type'     => 'text',
                'title'    => __('Text next to coyprights', 'basel'), 
                'subtitle' => __('You can use shortocdes.<br> Ex.: [social_buttons]', 'basel'),
                'default' => '' //'[social_buttons align="right" style="colored" size="small"]'
            ),
            array(
                'id'=>'prefooter_area',
                'type' => 'textarea',
                'title' => __('HTML before footer', 'basel'), 
                'subtitle' => __('Custom HTML Allowed (wp_kses)', 'basel'),
                'desc' => __('This is the text before footer field, again good for additional info. You can place here any shortcode, for ex.: [html_block id=""]', 'basel'),
                'validate' => 'html_custom',
                'allowed_html' => array(
                    'a' => array(
                        'href' => array(),
                        'title' => array()
                    ),
                    'br' => array(),
                    'em' => array(),
                    'p' => array(),
                    'div' => array(),
                    'strong' => array()
                )
            ),
            array (
                'id'       => 'sticky_footer',
                'type'     => 'switch',
                'title'    => __('Sticky footer', 'basel'), 
                'default' => false
            ),
        ),
    ) );

    Redux::setSection( $opt_name, array(
        'title' => 'Typography',
        'id' => 'typography',
        'icon' => 'el-icon-fontsize',
        'fields' => array (
            array(
                'id'          => 'text-font',
                'type'        => 'typography', 
                'title'       => __('Text font', 'basel'),
                'all_styles'  => true,
                'google'      => true, 
                'font-backup' => true,
                'text-align'  => false,
                'font-weight' => false,
                'font-style' => false,
                'output'      => $basel_selectors['text-font'],
                'units'       =>'px',
                'subtitle'    => __('Set you typography options for body, paragraphs.', 'basel'),
                'default'     => array(
                    'font-family' => 'Karla',
                    'google'      => true,
                    'font-backup' => 'Arial, Helvetica, sans-serif'
                )
            ),
            array(
                'id'          => 'primary-font',
                'type'        => 'typography', 
                'title'       => __('Primary font', 'basel'),
                'all_styles'  => true,
                'google'      => true, 
                'font-backup' => true,
                'font-size'   => false,
                'line-height' => false,
                'text-align'  => false,
                'output'      => $basel_selectors['primary-font'],
                'units'       =>'px',
                'subtitle'    => __('Set you typography options for titles, post names.', 'basel'),
                'default'     => array(
                    'font-family' => 'Karla',
                    'google'      => true,
                    'font-backup' => "'MS Sans Serif', Geneva, sans-serif"
                )
            ),
            array(
                'id'          => 'post-titles-font',
                'type'        => 'typography', 
                'title'       => __('Entities names', 'basel'),
                'all_styles'  => true,
                'google'      => true, 
                'font-backup' => true,
                'font-size'   => false,
                'line-height' => false,
                'text-align'  => false,
                'output'      => $basel_selectors['titles-font'],
                'units'       =>'px',
                'subtitle'    => __('Titles for posts, products, categories and pages', 'basel'),
                'default'     => array(
                    'font-family' => 'Lora',
                    'google'      => true,
                    'font-backup' => "'MS Sans Serif', Geneva, sans-serif"
                )
            ),
            array(
                'id'          => 'secondary-font',
                'type'        => 'typography', 
                'title'       => __('Secondary font', 'basel'),
                'all_styles'  => true,
                'google'      => true, 
                'font-backup' => true,
                'font-size'   => false,
                'line-height' => false,
                'text-align'  => false,
                'output'      => $basel_selectors['secondary-font'],
                'units'       =>'px',
                'subtitle'    => __('Use for secondary titles (use CSS class "font-alt" or "title-alt")', 'basel'),
                'default'     => array(
                    'font-family' => 'Lato',
                    'font-style' => 'italic',
                    'google'      => true,
                    'font-backup' => "'Comic Sans MS', cursive"
                )
            ),
            array(
                'id'          => 'widget-titles-font',
                'type'        => 'typography', 
                'title'       => __('Widget titles font', 'basel'),
                'all_styles'  => true,
                'google'      => true, 
                'font-backup' => true,
                'font-size'   => true,
                'line-height' => true,
                'text-align'  => true,
                'output'      => $basel_selectors['widget-titles-font'],
                'units'       =>'px',
            ),
            array(
                'id'          => 'navigation-font',
                'type'        => 'typography', 
                'title'       => __('Navigation font', 'basel'),
                'all_styles'  => true,
                'google'      => true, 
                'font-backup' => true,
                'font-size'   => true,
                'line-height' => false,
                'text-align'  => false,
                'output'      => $basel_selectors['navigation-font'],
                'units'       =>'px',
            ),
        ),
    ) );


    Redux::setSection( $opt_name, array(
        'title' => 'Styles and colors',
        'id' => 'colors',
        'icon' => 'el-icon-brush',
        'fields' => array (
            array(
                'id'       => 'primary-color',
                'type'     => 'color',
                'title'    => __('Primary Color', 'basel'), 
                'subtitle' => __('Pick a background color for the theme buttons and other colored elements.', 'basel'),
                'validate' => 'color',
                'output'   => $basel_selectors['primary-color']
            ),
            array(
                'id'       => 'secondary-color',
                'type'     => 'color',
                'title'    => __('Secondary Color', 'basel'), 
                'validate' => 'color',
                'output'   => $basel_selectors['secondary-color']
            )
        ),
    ) );


    Redux::setSection( $opt_name, array(
        'title' => 'Blog',
        'id' => 'blog',
        'icon' => 'el-icon-pencil',
        'fields' => array (
            array (
                'id'       => 'blog_layout',
                'type'     => 'image_select',
                'title'    => __('Blog Layout', 'basel'), 
                'subtitle' => __('Select main content and sidebar alignment for blog pages.', 'basel'),
                'options'  => array(
                    'full-width'      => array(
                        'alt'   => '1 Column', 
                        'img'   => ReduxFramework::$_url.'assets/img/1col.png'
                    ),
                    'sidebar-left'      => array(
                        'alt'   => '2 Column Left', 
                        'img'   => ReduxFramework::$_url.'assets/img/2cl.png'
                    ),
                    'sidebar-right'      => array(
                        'alt'   => '2 Column Right', 
                        'img'  => ReduxFramework::$_url.'assets/img/2cr.png'
                    ),
                ),
                'default' => 'sidebar-right'
            ),
            array (
                'id'       => 'blog_sidebar_width',
                'type'     => 'button_set',
                'title'    => __('Blog Sidebar size', 'basel'), 
                'subtitle' => __('You can set different sizes for your blog pages sidebar', 'basel'),
                'options'  => array(
                    2 => 'small', 
                    3 => 'medium', 
                    4 => 'large'
                ),
                'default' => 3
            ),
            array (
                'id'       => 'blog_design',
                'type'     => 'select',
                'title'    => __('Blog Design', 'basel'), 
                'subtitle' => __('You can use different design for your blog styled for the theme.', 'basel'),
                'options'  => array(
                    'default' => 'Default', 
                    'default-alt' => 'Default alternative', 
                    'small-images' => 'Small images', 
                    'masonry' => 'Masonry grid', 
                    'mask' => 'Mask on image'
                ),
                'default' => 'default'
            ),
            array (
                'id'       => 'blog_columns',
                'type'     => 'button_set',
                'title'    => __('Blog items columns', 'basel'), 
                'subtitle' => __('For masonry grid design', 'basel'),
                'options'  => array(
                    2 => '2', 
                    3 => '3', 
                    4 => '4', 
                    6 => '6'
                ),
                'default' => 3
            ),
            array (
                'id'       => 'blog_excerpt',
                'type'     => 'select',
                'title'    => __('Posts excerpt', 'basel'), 
                'subtitle' => __('If you will set this option to "Excerpt" then you are able to set custom excerpt for each post or it will be cutted from the post content. If you choose "Full content" then all content will be shown, or you can also add "Read more button" while editing the post and by doing this cut your excerpt length as you need.', 'basel'),
                'options'  => array(
                    'excerpt' => 'Excerpt', 
                    'full' => 'Full content'
                ),
                'default' => 'full'
            ),
            array (
                'id'       => 'blog_excerpt_length',
                'type'     => 'text',
                'title'    => __('Excerpt length', 'basel'), 
                'subtitle' => __('Number of words that will be displayed for each post if you use "Excerpt" mode and don\'t set custom excerpt for each post.', 'basel'),
                'default' => 35,
                'required' => array(
                     array('blog_excerpt','equals', 'excerpt'),
                )
            ),
            array (
                'id'       => 'blog_share',
                'type'     => 'switch',
                'title'    => __('Share buttons', 'basel'), 
                'subtitle' => __('Display share icons on single post page', 'basel'),
                'default' => true
            ),
            array (
                'id'       => 'blog_navigation',
                'type'     => 'switch',
                'title'    => __('Posts navigation', 'basel'), 
                'subtitle' => __('Next and previous posts links on single post page', 'basel'),
                'default' => true
            ),
            array (
                'id'       => 'blog_author_bio',
                'type'     => 'switch',
                'title'    => __('Author bio', 'basel'), 
                'subtitle' => __('Display information about the post author', 'basel'),
                'default' => true
            ),
            array (
                'id'       => 'blog_related_posts',
                'type'     => 'switch',
                'title'    => __('Related posts', 'basel'), 
                'subtitle' => __('Show related posts on single post page', 'basel'),
                'default' => true
            ),
        ),
    ) );


    Redux::setSection( $opt_name, array(
        'title' => 'Portfolio',
        'id' => 'portfolio',
        'icon' => 'el-icon-th',
        'fields' => array (
            array (
                'id'       => 'portoflio_style',
                'type'     => 'select',
                'title'    => __('Portfolio Style', 'basel'), 
                'subtitle' => __('You can use different styles for your projects.', 'basel'),
                'options'  => array(
                    'hover' => 'Show text on mouse over', 
                    'hover-inverse' => 'Hide text on mouse over', 
                    'bordered' => 'Bordered style',
                    'bordered-inverse' => 'Bordered inverse',
                    'text-shown' => 'Text under image', 
                    'with-bg' => 'Text with background', 
                    'with-bg-alt' => 'Text with background alternative', 
                ),
                'default' => 'hover'
            ),
            array (
                'id'       => 'portfolio_full_width',
                'type'     => 'switch',
                'title'    => __('Full Width portfolio', 'basel'), 
                'subtitle' => __('Makes container 100% width of the page', 'basel'),
                'default' => false
            ),
            array (
                'id'       => 'projects_columns',
                'type'     => 'button_set',
                'title'    => __('Projects columns', 'basel'), 
                'subtitle' => __('How many projects you want to show per row', 'basel'),
                'options'  => array(
                    2 => '2', 
                    3 => '3', 
                    4 => '4', 
                    6 => '6'
                ),
                'default' => 3
            ),
            array (
                'id'       => 'portfolio_spacing',
                'type'     => 'button_set',
                'title'    => __('Space between projects', 'basel'), 
                'subtitle' => __('You can set different spacing between blocks on portfolio page', 'basel'),
                'options'  => array(
                    0 => '0', 
                    2 => '2', 
                    6 => '5', 
                    10 => '10', 
                    20 => '20', 
                    30 => '30'
                ),
                'default' => 30
            ),
            // array (
            //     'id'       => 'portfolio_pagination',
            //     'type'     => 'button_set',
            //     'title'    => __('Portfolio pagination', 'basel'), 
            //     'options'  => array(
            //         'pagination' => 'Pagination links', 
            //         'load_more' => '"Load more" button',
            //         'infinit' => 'Infinit scrolling', 
            //     ),
            //     'default' => 'pagination'
            // ),
            array (
                'id'       => 'portoflio_per_page',
                'type'     => 'text',
                'title'    => __('Items per page', 'basel'), 
                'default' => 12
            ),
            array(         
                'id'       => 'portfolio_nav_background',
                'type'     => 'background',
                'title'    => __('Filter background', 'basel'),
                'output'   => array('.portfolio-filter')
            ),
            array(         
                'id'       => 'portoflio_filters',
                'type'     => 'switch',
                'title'    => __('Show categories filters', 'basel'),
                'default'  => true
            ),
            array (
                'id'       => 'portfolio_nav_color_scheme',
                'type'     => 'select',
                'title'    => __('Color scheme for filters', 'basel'), 
                'subtitle' => __('You can change colors of links in portfolio filters', 'basel'),
                'options'  => array(
                    'dark' => 'Dark', 
                    'light' => 'Light', 
                ),
                'default' => 'dark'
            ),
        ),
    ) );

    Redux::setSection( $opt_name, array(
        'title' => 'Shop',
        'id' => 'shop',
        'icon' => 'el-icon-shopping-cart',
        'fields' => array (
            array (
                'id'       => 'shop_per_page',
                'type'     => 'text',
                'title'    => __('Products per page', 'basel'), 
                'subtitle' => __('Number of products per page', 'basel'),
                'default' => 12
            ),
            array (
                'id'       => 'products_columns',
                'type'     => 'button_set',
                'title'    => __('Products columns', 'basel'), 
                'subtitle' => __('How many products you want to show per row', 'basel'),
                'options'  => array(
                    2 => '2', 
                    3 => '3', 
                    4 => '4', 
                    6 => '6'
                ),
                'default' => 4
            ),
            array (
                'id'       => 'shop_filters',
                'type'     => 'switch',
                'title'    => __('Shop filters', 'basel'), 
                'subtitle' => __('Enable shop filters widget\'s area above the products.', 'basel'),
                'default' => true
            ),
            array (
                'id'       => 'products_masonry',
                'type'     => 'switch',
                'title'    => __('Masonry grid', 'basel'), 
                'subtitle' => __('Products may have different sizes', 'basel'),
                'default' => false
            ),
            array (
                'id'       => 'products_different_sizes',
                'type'     => 'switch',
                'title'    => __('Products grid with different sizes', 'basel'), 
                'default' => false
            ),
            array (
                'id'       => 'products_hover',
                'type'     => 'select',
                'title'    => __('Hover on product', 'basel'), 
                'subtitle' => __('Choose one of those hover effects for products', 'basel'),
                'options'  => basel_get_config( 'product-hovers' ),
                'default' => 'alt'
            ),
            array (
                'id'       => 'ajax_shop',
                'type'     => 'switch',
                'title'    => __('AJAX shop', 'basel'), 
                'subtitle' => __('Enable AJAX functionality for filters widgets on shop.', 'basel'),
                'default' => true
            ),
            array (
                'id'       => 'shop_countdown',
                'type'     => 'switch',
                'title'    => __('Countdown timer', 'basel'), 
                'subtitle' => __('Show timer for products that have scheduled date for the sale price', 'basel'),
                'default' => false
            ),
            array (
                'id'       => 'added_to_cart_popup',
                'type'     => 'switch',
                'title'    => __('Show popup after adding to cart', 'basel'), 
                'subtitle' => __('Enable to display information window about successful adding to cart. .', 'basel'),
                'default' => true
            ),
            array (
                'id'       => 'quick_view',
                'type'     => 'switch',
                'title'    => __('Quick View', 'basel'), 
                'subtitle' => __('Enable Quick view option. Ability to see the product information with AJAX.', 'basel'),
                'default' => true
            ),
            array (
                'id'       => 'quick_view_variable',
                'type'     => 'switch',
                'title'    => __('Show variations on quick view', 'basel'), 
                'subtitle' => __('Enable Quick view option for variable products. Will allow your users to purchase variable products directly from the quick view.', 'basel'),
                'default' => true,
                'required' => array(
                     array('quick_view','equals',true),
                )
            ),
            array (
                'id'       => 'search_categories',
                'type'     => 'switch',
                'title'    => __('Categories dropdown in WOO search form', 'basel'), 
                'subtitle' => __('Display categories select that allows users search products by category', 'basel'),
                'default' => true
            ),
            array (
                'id'       => 'categories_design',
                'type'     => 'select',
                'title'    => __('Categories design', 'basel'), 
                'subtitle' => __('Choose one of those designs for categories', 'basel'),
                'options'  => basel_get_config( 'categories-designs' ),
                'default' => 'default'
            ),
            array (
                'id'       => 'empty_cart_text',
                'type'     => 'textarea',
                'title'    => __('Empty cart text', 'basel'), 
                'subtitle' => __('Text will be displayed if user don\'t add any products to cart', 'basel'),
                'default'  => 'Before proceed to checkout you must add some products to your shopping cart.<br> You will find a lot of interesting products on our "Shop" page.',
            ),


        ),
    ) );

    Redux::setSection( $opt_name, array(
        'title' => 'Shop page layout',
        'id' => 'shop-layout',
        'subsection' => true,
        'fields' => array (
            array (
                'id'       => 'shop_layout',
                'type'     => 'image_select',
                'title'    => __('Shop Layout', 'basel'), 
                'subtitle' => __('Select main content and sidebar alignment for shop pages.', 'basel'),
                'options'  => array(
                    'full-width'      => array(
                        'alt'   => '1 Column', 
                        'img'   => ReduxFramework::$_url.'assets/img/1col.png'
                    ),
                    'sidebar-left'      => array(
                        'alt'   => '2 Column Left', 
                        'img'   => ReduxFramework::$_url.'assets/img/2cl.png'
                    ),
                    'sidebar-right'      => array(
                        'alt'   => '2 Column Right', 
                        'img'  => ReduxFramework::$_url.'assets/img/2cr.png'
                    ),
                ),
                'default' => 'full-width'
            ),
            array (
                'id'       => 'shop_sidebar_width',
                'type'     => 'button_set',
                'title'    => __('Sidebar size', 'basel'), 
                'subtitle' => __('You can set different sizes for your shop pages sidebar', 'basel'),
                'options'  => array(
                    2 => 'small', 
                    3 => 'medium', 
                    4 => 'large'
                ),
                'default' => 3
            ),
            array (
                'id'       => 'shop_title',
                'type'     => 'switch',
                'title'    => __('Shop title', 'basel'), 
                'subtitle' => __('Show title for shop page, product categories or tags.', 'basel'),
                'default' => false
            ),
            array (
                'id'       => 'shop_categories',
                'type'     => 'switch',
                'title'    => __('Categories menu in page heading', 'basel'), 
                'subtitle' => __('Show categories menu below page title', 'basel'),
                'default' => 1
            ),
            array (
                'id'       => 'shop_categories_ancestors',
                'type'     => 'switch',
                'title'    => __('Show current category ancestors', 'basel'), 
                'default' => 0,
                'required' => array(
                     array('shop_categories','equals',true),
                )
            )
        ),
    ) );

    Redux::setSection( $opt_name, array(
        'title' => 'Attribute swatches',
        'id' => 'shop-swatches',
        'subsection' => true,
        'fields' => array (
            array (
                'id'       => 'grid_swatches_attribute',
                'type'     => 'select',
                'title'    => __('Grid swatch attribute to display', 'basel'), 
                'subtitle' => __('Choose attribute that will be shown on products grid', 'basel'),
                'data'  => 'taxonomy',
            ),
            array (
                'id'       => 'swatches_use_variation_images',
                'type'     => 'switch',
                'title'    => __('Use images from product variations', 'basel'), 
                'subtitle' => __('If enabled swatches buttons will be filled with images choosed for product variations and not with images uploaded to attibute terms.', 'basel'),
                'default' => false
            ),
            array (
                'id'       => 'swatches_2_0',
                'type'     => 'switch',
                'title'    => __('Swatches on products 2.0', 'basel'), 
                'subtitle' => __('In our update 2.0 we have changed the way attributes selection works. You can disable this option and continue use the old one.', 'basel'),
                'default' => true
            ),
        ),
    ) );

    Redux::setSection( $opt_name, array(
        'title' => 'My Account',
        'id' => 'shop-account',
        'subsection' => true,
        'fields' => array (
            array (
                'id'       => 'login_tabs',
                'type'     => 'switch',
                'title'    => __('Login page tabs', 'basel'), 
                'subtitle' => __('Enable tabs for login and register forms', 'basel'),
                'default' => 1
            ),
            array (
                'id'       => 'reg_text',
                'type'     => 'editor',
                'title'    => __('Registration text', 'basel'), 
                'subtitle' => __('Show some information about registration on your web-site', 'basel'),
                'default' => 'Registering for this site allows you to access your order status and history. Just fill in the fields below, and we’ll get a new account set up for you in no time. We will only ask you for information necessary to make the purchase process faster and easier.'
            ),
            array (
                'id'       => 'login_links',
                'type'     => 'switch',
                'title'    => __('My Account link in header', 'basel'), 
                'subtitle' => __('Show links to login/register or my accout page in the header', 'basel'),
                'default' => 1
            ),
        ),
    ) );


    Redux::setSection( $opt_name, array(
        'title' => 'Catalog mode',
        'id' => 'shop-catalog',
        'subsection' => true,
        'fields' => array (
            array (
                'id'       => 'catalog_mode',
                'type'     => 'switch',
                'title'    => __('Enable catalog mode', 'basel'), 
                'subtitle' => __('You can hide all "Add to cart" buttons, cart widget, cart and checkout pages. This will allow you to showcase your products as an online catalog without ability to make a purchase.', 'basel'),
                'default' => false
            ),
        ),
    ) );

    Redux::setSection( $opt_name, array(
        'title' => 'Cookie Law Info',
        'id' => 'shop-cookie',
        'subsection' => true,
        'fields' => array (
            array (
                'id'       => 'cookies_info',
                'type'     => 'switch',
                'title'    => __('Show cookies info', 'basel'), 
                'subtitle' => __('Under EU privacy regulations, websites must make it clear to visitors what information about them is being stored. This specifically includes cookies. Turn on this option and user will see info box at the bottom of the page that your web-site is using cookies.', 'basel'),
                'default' => true
            ),
            array (
                'id'       => 'cookies_text',
                'type'     => 'editor',
                'title'    => __('Popup text', 'basel'), 
                'subtitle' => __('Place here some information about cookies usage that will be shown in the popup.', 'basel'),
                'default' => __('We use cookies to improve your experience on our website. By browsing this website, you agree to our use of cookies.', 'basel'),
            ),
            array (
                'id'       => 'cookies_policy_page',
                'type'     => 'select',
                'title'    => __('Page with details', 'basel'), 
                'subtitle' => __('Choose page that will contain detailed information about your Privacy Policy', 'basel'),
                'data'     => 'pages'
            ),
        ),
    ) );


    Redux::setSection( $opt_name, array(
        'title' => 'Promo popup',
        'id' => 'shop-popup',
        'subsection' => true,
        'fields' => array (
            array (
                'id'       => 'promo_popup',
                'type'     => 'switch',
                'title'    => __('Enable promo popup', 'basel'), 
                'subtitle' => __('Show promo popup to users when they enter the site.', 'basel'),
                'default' => 1
            ),
            array (
                'id'       => 'popup_text',
                'type'     => 'editor',
                'title'    => __('Promo popup text', 'basel'), 
                'subtitle' => __('Place here some promo text or use HTML block and place here it\'s shortcode', 'basel'),
                'default' => '
<div class="vc_row">
    <div class="vc_column_container vc_col-sm-6">
        <div class="vc_column-inner ">
            <figure style="margin: -20px;">
                <img src="http://placehold.it/760x800" alt="">
            </figure>
        </div>
    </div>
    <div class="vc_column_container vc_col-sm-6">
        <div style="padding: 70px 25px 70px 40px;">
            <h1 style="margin-bottom: 0px; text-align: center;"><strong>HELLO USER, JOIN OUR</strong></h1>
            <h1 style="text-align: center;"><strong>NEWSLETTER<span style="color: #0f8a7e;"> BASEL &amp; CO.</span></strong></h1>
            <p style="text-align: center; font-size: 16px;">Be the first to learn about our latest trends and get exclusive offers.</p>
            [mc4wp_form]
        </div>
    </div>
</div>
                '
            ),
            array (
                'id'       => 'popup_event',
                'type'     => 'button_set',
                'title'    => __('Show popup after', 'basel'), 
                'options'  => array(
                    'time' => 'some time', 
                    'scroll' => 'user scroll',
                ),
                'default' => 'time'
            ),
            array (
                'id'       => 'promo_timeout',
                'type'     => 'text',
                'title'    => __('Popup delay', 'basel'), 
                'subtitle' => __('Show popup after some time (in milliseconds)', 'basel'),
                'default' => '2000',
                'required' => array(
                     array('popup_event','equals', 'time'),
                )
            ),
            array(
                'id'        => 'popup_scroll',
                'type'      => 'slider',
                'title'     => __('Show after user scroll down the page', 'basel'),
                'subtitle' => __('Set the number of pixels users have to scroll down before popup opens', 'basel'),
                "default"   => 1000,
                "min"       => 100,
                "step"      => 50,
                "max"       => 5000,
                'display_value' => 'label',
                'required' => array(
                     array('popup_event','equals', 'scroll'),
                )
            ),
            array(
                'id'        => 'popup_pages',
                'type'      => 'slider',
                'title'     => __('Show after number of pages visited', 'basel'),
                'subtitle' => __('You can choose how much pages user should change before popup will be shown.', 'basel'),
                "default"   => 0,
                "min"       => 0,
                "step"      => 1,
                "max"       => 10,
                'display_value' => 'label'
            ),
            array (         
                'id'       => 'popup-background',
                'type'     => 'background',
                'title'    => __('Popup background', 'basel'),
                'subtitle' => __('Set background image or color for promo popup', 'basel'),
                'output'   => array('.basel-promo-popup'),
                // 'default'  => array(
                //     'background-image' => 'http://placehold.it/760x800',
                //     'background-repeat' => 'no-repeat',
                //     'background-size' => 'contain',
                //     'background-position' => 'left center',
                // )
            ),
            array (
                'id'       => 'promo_popup_hide_mobile',
                'type'     => 'switch',
                'title'    => __('Hide for mobile devices', 'basel'), 
                'default' => 1
            ),
        ),
    ) );

    Redux::setSection( $opt_name, array(
        'title' => 'Widgets',
        'id' => 'shop-widgets',
        'subsection' => true,
        'fields' => array (
            array (
                'id'       => 'categories_toggle',
                'type'     => 'switch',
                'title'    => __('Toggle function for categories widget', 'basel'), 
                'subtitle' => __('Turn it on to enable accordion JS for the WooCommerce Product Categories widget. Useful if you have a lot of categories and subcategories.', 'basel'),
                'default' => true
            ),
            array (
                'id'       => 'widgets_scroll',
                'type'     => 'switch',
                'title'    => __('Scroll for filters widgets', 'basel'), 
                'subtitle' => __('You can limit your Layered Navigation widgets by height and enable nice scroll for them. Useful if you have a lot of product colors/sizes or other attributes for filters.', 'basel'),
                'default' => true
            ),
            array(
                'id'        => 'widget_heights',
                'type'      => 'slider',
                'title'     => __('Height for filters widgets', 'basel'),
                "default"   => 280,
                "min"       => 100,
                "step"      => 1,
                "max"       => 800,
                'display_value' => 'label',
                'required' => array(
                     array('widgets_scroll','equals', true),
                )
            ),
        ),
    ) );

    Redux::setSection( $opt_name, array(
        'title' => 'Product Page',
        'id' => 'product_page',
        'icon' => 'el-icon-tags',
        'fields' => array (
            array (
                'id'       => 'single_product_layout',
                'type'     => 'image_select',
                'title'    => __('Single Product Sidebar', 'basel'), 
                'subtitle' => __('Select main content and sidebar alignment for single product pages.', 'basel'),
                'options'  => array(
                    'full-width'      => array(
                        'alt'   => '1 Column', 
                        'img'   => ReduxFramework::$_url.'assets/img/1col.png'
                    ),
                    'sidebar-left'      => array(
                        'alt'   => '2 Column Left', 
                        'img'   => ReduxFramework::$_url.'assets/img/2cl.png'
                    ),
                    'sidebar-right'      => array(
                        'alt'   => '2 Column Right', 
                        'img'  => ReduxFramework::$_url.'assets/img/2cr.png'
                    ),
                ),
                'default' => 'full-width'
            ),
            array (
                'id'       => 'single_sidebar_width',
                'type'     => 'button_set',
                'title'    => __('Sidebar size', 'basel'), 
                'subtitle' => __('You can set different sizes for your single product pages sidebar', 'basel'),
                'options'  => array(
                    2 => 'small', 
                    3 => 'medium', 
                    4 => 'large'
                ),
                'default' => 3
            ),
            array (
                'id'       => 'product_design',
                'type'     => 'select',
                'title'    => __('Product page design', 'basel'), 
                'subtitle' => __('Choose between different predefined designs', 'basel'),
                'options'  => array(
                    'default' => 'Default', 
                    'alt' => 'Alternative', 
                    'sticky' => 'Images scroll', 
                    'compact' => 'Compact', 
                ),
                'default' => 'alt'
            ),
            array (
                'id'       => 'force_header_full_width',
                'type'     => 'switch',
                'title'    => __('Force full width header for product page', 'basel'), 
                'default' => false,
                'required' => array(
                     array('product_design','equals','sticky'),
                )
            ),
            array (
                'id'       => 'single_product_style',
                'type'     => 'select',
                'title'    => __('Product image size', 'basel'), 
                'subtitle' => __('You can choose different page layout depending on the product image size you need', 'basel'),
                'options'  => array(
                    1 => 'Small image', 
                    2 => 'Medium', 
                    3 => 'Large'
                ),
                'default' => 2,
                'required' => array(
                     array('product_design','not','sticky'),
                )
            ),
            array (
                'id'       => 'thums_position',
                'type'     => 'select',
                'title'    => __('Thumbnails position', 'basel'), 
                'subtitle' => __('Use vertical or horizontal position for thumbnails', 'basel'),
                'options'  => array(
                    'left' => 'Left (vertical position)', 
                    'bottom' => 'Bottom (horizontal position)', 
                ),
                'default' => 'bottom',
                'required' => array(
                     array('product_design','not','sticky'),
                )
            ),
            array (         
                'id'       => 'product-background',
                'type'     => 'background',
                'title'    => __('Product background', 'basel'),
                'subtitle' => __('Set background for your products page. You can also specify different background for particular products while editing it.', 'basel'),
                'output'   => array('.single-product .site-content')
            ),
            array (
                'id'       => 'product_share',
                'type'     => 'switch',
                'title'    => __('Show share buttons', 'basel'), 
                'default' => true
            ),
            array (
                'id'       => 'product_share_type',
                'type'     => 'button_set',
                'title'    => __('Share buttons type', 'basel'), 
                'options'  => array(
                    'share' => 'Share', 
                    'follow' => 'Follow', 
                ),
                'default' => 'share',
                'required' => array(
                     array('product_share','equals', true),
                )
            ),
            array (
                'id'       => 'product_countdown',
                'type'     => 'switch',
                'title'    => __('Countdown timer', 'basel'), 
                'subtitle' => __('Show timer for products that have scheduled date for the sale price', 'basel'),
                'default' => false
            ),
            array (
                'id'       => 'related_products',
                'type'     => 'switch',
                'title'    => __('Show related products', 'basel'), 
                'default' => true
            ),
            array (
                'id'       => 'hide_tabs_titles',
                'title'    => __('Hide tabs headings', 'basel'), 
                'type'     => 'switch',
                'default'  => false
            ),
            array (
                'id'       => 'additional_tab_title',
                'type'     => 'text',
                'title'    => __('Additional tab title', 'basel'), 
                'subtitle' => __('Leave empty to disable custom tab', 'basel'),
                'default'  => 'Shipping & Delivery'
            ),
            array (
                'id'       => 'additional_tab_text',
                'type'     => 'textarea',
                'title'    => __('Additional tab content', 'basel'), 
                'default'  => '
<img src="http://placehold.it/250x200" class="alignleft" /> <p>Vestibulum curae torquent diam diam commodo parturient penatibus nunc dui adipiscing convallis bulum parturient suspendisse parturient a.Parturient in parturient scelerisque nibh lectus quam a natoque adipiscing a vestibulum hendrerit et pharetra fames.Consequat net</p>

<p>Vestibulum parturient suspendisse parturient a.Parturient in parturient scelerisque  nibh lectus quam a natoque adipiscing a vestibulum hendrerit et pharetra fames.Consequat netus.</p>

<p>Scelerisque adipiscing bibendum sem vestibulum et in a a a purus lectus faucibus lobortis tincidunt purus lectus nisl class eros.Condimentum a et ullamcorper dictumst mus et tristique elementum nam inceptos hac vestibulum amet elit</p>

<div class="clearfix"></div>
                '
            ),
        ),
    ) );

    Redux::setSection( $opt_name, array(
        'title' => 'Custom CSS',
        'id' => 'custom_css',
        'icon' => 'el-icon-css',
        'fields' => array (
            array (
                'id' => 'custom_css',
                'type' => 'ace_editor',
                'mode' => 'css',
                'title' => 'Global Custom CSS',
            ),
            array (
                'id' => 'css_desktop',
                'type' => 'ace_editor',
                'mode' => 'css',
                'title' => 'Custom CSS for desktop',
            ),
            array (
                'id' => 'css_tablet',
                'type' => 'ace_editor',
                'mode' => 'css',
                'title' => 'Custom CSS for tablet',
            ),
            array (
                'id' => 'css_wide_mobile',
                'type' => 'ace_editor',
                'mode' => 'css',
                'title' => 'Custom CSS for mobile landscape',
            ),
            array (
                'id' => 'css_mobile',
                'type' => 'ace_editor',
                'mode' => 'css',
                'title' => 'Custom CSS for mobile',
            ),
        ),
    ) );

    Redux::setSection( $opt_name, array(
        'title' => 'Custom JS',
        'id' => 'custom_js',
        'icon' => 'el-icon-magic',
        'fields' => array (
            array (
                'id' => 'custom_js',
                'type' => 'ace_editor',
                'mode' => 'javascript',
                'title' => 'Global Custom JS',
            ),
            array (
                'id' => 'js_ready',
                'type' => 'ace_editor',
                'mode' => 'javascript',
                'title' => 'On document ready',
                'desc' => __('Will be executed on $(document).ready()', 'basel')
            ),
        ),
    ) );

    Redux::setSection( $opt_name, array(
        'title' => 'Social',
        'id' => 'social',
        'icon' => 'el-icon-group',
        'fields' => array (
            array (
                'id'   => 'info_normal',
                'type' => 'info',
                'desc' => __('Configurate your [social_buttons] shortcode. You can leave field empty to remove particular link. ', 'basel')
            ),
            array (
                'id'       => 'fb_link',
                'type'     => 'text',
                'title'    => __('Facebook link', 'basel'), 
                'default' => '#'
            ),
            array (
                'id'       => 'twitter_link',
                'type'     => 'text',
                'title'    => __('Twitter link', 'basel'), 
                'default' => '#'
            ),
            array (
                'id'       => 'google_link',
                'type'     => 'text',
                'title'    => __('Google+', 'basel'), 
                'default' => '#'
            ),
            array (
                'id'       => 'social_email',
                'type'     => 'text',
                'title'    => __('Email for social links', 'basel'), 
                'default' => 'user@gmail.com'
            ),
            array (
                'id'       => 'isntagram_link',
                'type'     => 'text',
                'title'    => __('Instagram', 'basel'), 
                'default' => '#'
            ),
            array (
                'id'       => 'pinterest_link',
                'type'     => 'text',
                'title'    => __('Pinterest link', 'basel'), 
                'default' => '#'
            ),
            array (
                'id'       => 'youtube_link',
                'type'     => 'text',
                'title'    => __('Youtube link', 'basel'), 
                'default' => '#'
            ),
            array (
                'id'       => 'tumblr_link',
                'type'     => 'text',
                'title'    => __('Tumblr link', 'basel'), 
                'default' => ''
            ),
            array (
                'id'       => 'linkedin_link',
                'type'     => 'text',
                'title'    => __('LinkedIn link', 'basel'), 
                'default' => ''
            ),
            array (
                'id'       => 'vimeo_link',
                'type'     => 'text',
                'title'    => __('Vimeo link', 'basel'), 
                'default' => ''
            ),
            array (
                'id'       => 'flickr_link',
                'type'     => 'text',
                'title'    => __('Flickr link', 'basel'), 
                'default' => ''
            ),
            array (
                'id'       => 'github_link',
                'type'     => 'text',
                'title'    => __('Github link', 'basel'), 
                'default' => ''
            ),
            array (
                'id'       => 'dribbble_link',
                'type'     => 'text',
                'title'    => __('Dribbble link', 'basel'), 
                'default' => ''
            ),
            array (
                'id'       => 'behance_link',
                'type'     => 'text',
                'title'    => __('Behance link', 'basel'), 
                'default' => ''
            ),
            array (
                'id'       => 'soundcloud_link',
                'type'     => 'text',
                'title'    => __('SoundCloud link', 'basel'), 
                'default' => ''
            ),
            array (
                'id'       => 'spotify_link',
                'type'     => 'text',
                'title'    => __('Spotify link', 'basel'), 
                'default' => ''
            ),
        ),
    ) );

    Redux::setSection( $opt_name, array(
        'title' => 'Performance',
        'id' => 'performance',
        'icon' => 'el-icon-cog',
        'fields' => array (
            array (
                'id'       => 'minified_css',
                'type'     => 'switch',
                'title'    => __('Include minified CSS', 'basel'), 
                'subtitle' => __('Minified version of style.css file will be loaded (style.min.css)', 'basel'),
                'default' => true
            ),
            array (
                'id'       => 'minified_js',
                'type'     => 'switch',
                'title'    => __('Include minified JS', 'basel'), 
                'subtitle' => __('Minified version of functions.js and libraries.js file will be loaded (theme.min.js)', 'basel'),
                'default' => true
            ),
        ),
    ) );

    Redux::setSection( $opt_name, array(
        'title' => 'Other',
        'id' => 'other',
        'icon' => 'el-icon-cog',
        'fields' => array (
            array (
                'id'       => 'dummy_import',
                'type'     => 'switch',
                'title'    => __('Show Dummy Content link in admin menu', 'basel'), 
                'default' => true
            ),
        ),
    ) );


    Redux::setSection( $opt_name, array(
        'title' => 'Maintenance',
        'id' => 'maintenance',
        'icon' => 'el-icon-cog',
        'fields' => array (
            array (
                'id'       => 'maintenance_mode',
                'type'     => 'switch',
                'title'    => __('Enable maintenance mode', 'basel'), 
                'subtitle' => __('This will block non-logged users access to the site.', 'basel'),
                'description' => __('If enabled you need to create maintenance page in Dashbard - Pages - Add new. Choose "Template" to be "Maintenance" in "Page attributes". Or you can import the page from our demo in Dashbard - Dummy Content', 'basel'),
                'default' => false
            ),
        ),
    ) );

    // Load extensions
    //Redux::setExtensions( $opt_name, BASEL_3D . '/options/ext/' );

    //add_filter('redux/options/' . $opt_name . '/compiler', array( $this, 'basel_typography_compiler' ), 10, 3);

    if( ! function_exists( 'basel_typography_compiler' ) ) {
        function basel_typography_compiler($options, $css, $changed_values) {
            echo '<h1>The compiler hook has run!</h1>';
             
            print_r ($options);
            print_r ($css);
            print_r ($changed_values);
        }
    }

    function basel_removeDemoModeLink() { // Be sure to rename this function to something more unique
        if ( class_exists('ReduxFrameworkPlugin') ) {
            remove_filter( 'plugin_row_meta', array( ReduxFrameworkPlugin::get_instance(), 'plugin_metalinks'), null, 2 );
        }
        if ( class_exists('ReduxFrameworkPlugin') ) {
            remove_action('admin_notices', array( ReduxFrameworkPlugin::get_instance(), 'admin_notices' ) );    
        }
    }
    add_action('init', 'basel_removeDemoModeLink', 1520);

