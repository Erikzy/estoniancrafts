<?php if ( ! defined('BASEL_THEME_DIR')) exit('No direct script access allowed');

/**
 * ----------------------------------------------------------------------------------------
 * Include the generated CSS and JS in the page header.
 * ----------------------------------------------------------------------------------------
 */
if ( ! function_exists( 'basel_load_wp_head' ) ) {
	function basel_load_wp_head() {

        $logo_container_width = basel_get_opt( 'logo_width' );
        $logo_img_width = basel_get_opt( 'logo_img_width' );
		$right_column_width   = basel_get_opt( 'right_column_width' );

        $header = basel_get_opt( 'header' );
        $header_height = basel_get_opt( 'header_height' );
        $sticky_header_height = basel_get_opt( 'sticky_header_height' );
        $mobile_header_height = basel_get_opt( 'mobile_header_height' );

        $right_column_width_percents = $menu_width = (int) (100 - $logo_container_width) / 2;

        $widgets_scroll = basel_get_opt( 'widgets_scroll' );
        $widgets_height = basel_get_opt( 'widget_heights' );

        $custom_css 		= basel_get_opt( 'custom_css' );
        $css_desktop 		= basel_get_opt( 'css_desktop' );
        $css_tablet 		= basel_get_opt( 'css_tablet' );
        $css_wide_mobile 	= basel_get_opt( 'css_wide_mobile' );
        $css_mobile         = basel_get_opt( 'css_mobile' );
        $custom_js          = basel_get_opt( 'custom_js' );
        $js_ready 		    = basel_get_opt( 'js_ready' );

		?>
		
		<!-- Logo CSS -->
		<style type="text/css">
            <?php if( $header != 'split'  ): ?>

            <?php endif; ?>

            .site-logo {
                width: <?php echo esc_html( $logo_container_width ); ?>%;
            }    

            .site-logo img {
                max-width: <?php echo esc_html( $logo_img_width ); ?>px;
                max-height: <?php echo esc_html( $header_height ); ?>px;
            }    

            <?php if( $header == 'shop'  ): ?>
                .widgetarea-head,
                .main-nav {
                    width: <?php echo esc_html( $menu_width ); ?>%;
                }  

                .right-column {
                    width: <?php echo esc_html( $right_column_width_percents ); ?>%;
                }  

            <?php elseif( $header == 'logo-center' ): ?>
                .widgetarea-head {
                    width: <?php echo esc_html( $menu_width ); ?>%;
                }  

                .right-column {
                    width: <?php echo esc_html( $right_column_width_percents ); ?>%;
                }  

                .sticky-header .right-column {
                    width: <?php echo esc_html( $right_column_width ); ?>px;
                }  

            <?php elseif( $header == 'split' ): ?>
                .left-column,
                .right-column {
                    width: <?php echo esc_html( $right_column_width ); ?>px;
                }  
            <?php else: ?>
                .right-column {
                    width: <?php echo esc_html( $right_column_width ); ?>px;
                }  
            <?php endif; ?>

            <?php if( $widgets_scroll ): ?>
                .basel-woocommerce-layered-nav .basel-scroll {
                    max-height: <?php echo ($widgets_height); ?>px;
                }
            <?php endif; ?>


            /* Header height configs */

       
            /* And for sticky header logo also */
            .act-scroll .site-logo img,
            .header-clone .site-logo img {
                max-height: <?php echo esc_html( $sticky_header_height ); ?>px;
            }   

            /* Set sticky headers height for cloned headers based on menu links line height */
            .header-clone .main-nav .menu > li > a {
                height: <?php echo esc_html( $sticky_header_height ); ?>px;
                line-height: <?php echo esc_html( $sticky_header_height ); ?>px;
            } 

            <?php if( $header == 'base' || $header == 'logo-center' || $header == 'split' ): ?>
                /* Header height for layouts that don't have line height for menu links */
                <?php elseif( $header != 'vertical' ): ?>
                /* Header height for these layouts based on it's menu links line height */
                .main-nav .menu > li > a {
                    height: <?php echo esc_html( $header_height ); ?>px;
                    line-height: <?php echo esc_html( $header_height ); ?>px;
                }  
                /* The same for sticky header */
                .act-scroll .main-nav .menu > li > a {
                    height: <?php echo esc_html( $sticky_header_height ); ?>px;
                    line-height: <?php echo esc_html( $sticky_header_height ); ?>px;
                }  
            <?php endif; ?>

            <?php if( $header == 'split'  ): ?>
                /* Sticky header height for split header layout */
                .act-scroll .wrapp-header {
                    min-height: <?php echo esc_html( $sticky_header_height ); ?>px;
                }   
            <?php endif; ?>

            <?php if( $header == 'shop'  ): ?>
                /* Set line height for header links for shop header layout. Based in the header height option */
                .header-shop .right-column .header-links {
                    height: <?php echo esc_html( $header_height ); ?>px;
                    line-height: <?php echo esc_html( $header_height ); ?>px;
                }  

                /* The same for sticky header */
                .header-shop.act-scroll .right-column .header-links {
                    height: <?php echo esc_html( $sticky_header_height ); ?>px;
                    line-height: <?php echo esc_html( $sticky_header_height ); ?>px;
                }  
            <?php endif; ?>

            /* Page headings settings for heading overlap. Calculate on the header height base */

            .basel-header-overlap .title-size-default,
            .basel-header-overlap .title-size-small,
            .basel-header-overlap .title-shop.without-title.title-size-default,
            .basel-header-overlap .title-shop.without-title.title-size-small {
                padding-top: <?php echo ($header_height + 40);  ?>px;
            }


            .basel-header-overlap .title-shop.without-title.title-size-large,
            .basel-header-overlap .title-size-large {
                padding-top: <?php echo ($header_height + 120);  ?>px;
            }

            @media (max-width: 991px) {
                /* Set header height for mobile devices */
                .main-header .wrapp-header {
                    min-height: <?php echo esc_html( $mobile_header_height ); ?>px;
                } 

                /* Limit logo image height for mobile according to mobile header height */
                .site-logo img {
                    max-height: <?php echo esc_html( $mobile_header_height ); ?>px;
                }   

                /* Limit logo on sticky header. Both header real and header cloned */
                .act-scroll .site-logo img,
                .header-clone .site-logo img {
                    max-height: <?php echo esc_html( $mobile_header_height ); ?>px;
                }   

                /* Page headings settings for heading overlap. Calculate on the MOBILE header height base */
                .basel-header-overlap .title-size-default,
                .basel-header-overlap .title-size-small,
                .basel-header-overlap .title-shop.without-title.title-size-default,
                .basel-header-overlap .title-shop.without-title.title-size-small {
                    padding-top: <?php echo ($mobile_header_height + 20);  ?>px;
                }

                .basel-header-overlap .title-shop.without-title.title-size-large,
                .basel-header-overlap .title-size-large {
                    padding-top: <?php echo ($mobile_header_height + 60);  ?>px;
                }
 
             }
     
            <?php 
                if( $custom_css != '' ) {
                    echo ($custom_css);
                }
                if( $css_desktop != '' ) {
                    echo '@media (min-width: 992px) { ' . ($css_desktop) . ' }'; 
                }
                if( $css_tablet != '' ) {
                    echo '@media (min-width: 768px) and (max-width: 991px) {' . ($css_tablet) . ' }'; 
                }
                if( $css_wide_mobile != '' ) {
                    echo '@media (min-width: 481px) and (max-width: 767px) { ' . ($css_wide_mobile) . ' }'; 
                }
                if( $css_mobile != '' ) {
                    echo '@media (max-width: 480px) { ' . ($css_mobile) . ' }'; 
                }
             ?>

		</style>
        <?php if( ! empty( $custom_js ) || ! empty( $js_ready ) ): ?>
            <script type="text/javascript">
                <?php if( ! empty( $custom_js ) ): ?>
                    <?php echo ($custom_js); ?>
                <?php endif; ?>
                <?php if( ! empty( $js_ready ) ): ?>
                    jQuery(document).ready(function() {
                        <?php echo ($js_ready); ?>
                    });
                <?php endif; ?>
            </script>
        <?php endif; ?>

		<?php
	}

	add_action( 'wp_head', 'basel_load_wp_head' );
}
