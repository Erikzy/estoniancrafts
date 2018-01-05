<?php if ( ! defined('ABSPATH')) exit('No direct script access allowed');

/**
* ------------------------------------------------------------------------------------------------
* Section title shortcode
* ------------------------------------------------------------------------------------------------
*/

if( ! function_exists( 'basel_shortcode_title' ) ) {
	function basel_shortcode_title( $atts ) {
		extract( shortcode_atts( array(
			'title' 	 => 'Title',
			'subtitle' 	 => '',
			'after_title'=> '',
			'link' 	 	 => '',
			'color' 	 => 'default',
			'style'   	 => 'default',
			'size' 		 => 'default',
			'subtitle_font' => 'default',
			'align' 	 => 'center',
			'el_class' 	 => '',
			'css'		 => ''
		), $atts) );

		$output = $attrs = '';

		$title_class = '';

		$title_class .= ' basel-title-color-' . $color;
		$title_class .= ' basel-title-style-' . $style;
		$title_class .= ' basel-title-size-' . $size;
		$title_class .= ' text-' . $align;

		$separator = '<span class="title-separator"><span></span></span>';

		if( function_exists( 'vc_shortcode_custom_css_class' ) ) {
			$title_class .= ' ' . vc_shortcode_custom_css_class( $css );
		}

		if( $el_class != '' ) {
			$title_class .= ' ' . $el_class;
		}

		$output .= '<div class="title-wrapper ' . $title_class . '">';

			if( $subtitle != '' ) {
				$output .= '<span class="title-subtitle font-'. esc_attr( $subtitle_font ) .'">' . $subtitle . '</span>';
			}

			$output .= '<div class="liner-continer"> <span class="left-line"></span> <span class="title">' . $title . $separator . '</span> <span class="right-line"></span> </div>';

			if( $after_title != '' ) {
				$output .= '<span class="title-after_title">' . $after_title . '</span>';
			}

		$output .= '</div>';

		return $output;

	}

	add_shortcode( 'basel_title', 'basel_shortcode_title' );
}


/**
* ------------------------------------------------------------------------------------------------
* Buttons shortcode
* ------------------------------------------------------------------------------------------------
*/

if( ! function_exists( 'basel_shortcode_button' ) ) {
	function basel_shortcode_button( $atts ) {
		extract( shortcode_atts( array(
			'title' 	 => 'GO',
			'link' 	 	 => '',
			'color' 	 => 'default',
			'style'   	 => 'default',
			'size' 		 => 'default',
			'align' 	 => 'center',
			'el_class' 	 => '',
		), $atts) );

		$output = $attrs = '';

		$btn_class = 'btn';

		$btn_class .= ' btn-color-' . $color;
		$btn_class .= ' btn-style-' . $style;
		$btn_class .= ' btn-size-' . $size;

		if( $el_class != '' ) {
			$btn_class .= ' ' . $el_class;
		}

		if( $link != '' ) {
			$attrs .= 'href="' . esc_attr( $link ) . '"';
		}

		$attrs .= ' class="' . $btn_class . '"';

		$output .= '<div class="basel-button-wrapper text-' . esc_attr( $align ) . '"><a ' . $attrs . '>' . $title . '</a></div>';

		return $output;

	}

	add_shortcode( 'basel_button', 'basel_shortcode_button' );
}

/**
* ------------------------------------------------------------------------------------------------
* instagram shortcode
* ------------------------------------------------------------------------------------------------
*/

if( ! function_exists( 'basel_shortcode_instagram' ) ) {
	function basel_shortcode_instagram( $atts, $content = '' ) {
		$output = '';
		extract(shortcode_atts( array(
			'title' => '',
			'username' => 'flickr',
			'number' => 9,
			'size' => 'thumbnail',
			'target' => '_self',
			'link' => '',
			'design' => '',
			'spacing' => 0,
			'rounded' => 0,
			'per_row' => 3
		), $atts ));	

		$carousel_id = rand(100,999);

		ob_start();

		$class = 'instagram-widget slider-' . $carousel_id;

		if( $design != '' ) {
			$class .= ' instagram-' . $design;
		}

		if( $spacing == 1 ) {
			$class .= ' instagram-with-spaces';
		}

		if( $rounded == 1 ) {
			$class .= ' instagram-rounded';
		}

		$class .= ' instagram-per-row-' . $per_row;

		echo '<div class="' . $class . '">';

		if(!empty($title)) { echo '<h3 class="title">' . $title . '</h3>'; };

		if ($username != '') {

			if ( ! empty( $content ) ): ?>
				<div class="instagram-content">
					<div class="instagram-content-inner">
						<?php echo do_shortcode( $content ); ?>
					</div>
				</div>
			<?php endif;

			$media_array = basel_scrape_instagram($username, $number);

			if ( is_wp_error($media_array) ) {

			   echo esc_html( $media_array->get_error_message() );

			} else {

				// filter for images only?
				//if ( $images_only = apply_filters( 'wpiw_images_only', FALSE ) )
				//	$media_array = array_filter( $media_array, 'basel_instagram_images_only' );

				?><ul class="instagram-pics"><?php
				foreach ($media_array as $item) {
					$image = (! empty( $item[$size] )) ? $item[$size] : $item['thumbnail'];
					echo '<li>
						<a href="'. esc_url( $item['link'] ) .'" target="'. esc_attr( $target ) .'"></a>
						<div class="wrapp-pics">
							<img src="'. esc_url( $image ) .'" />
							<div class="hover-mask"></div>
						</div>
					</li>';
				}
				?></ul><?php
			}
		}

		if ($link != '') {
			?><p class="clear"><a href="//instagram.com/<?php echo trim($username); ?>" rel="me" target="<?php echo esc_attr( $target ); ?>"><?php echo esc_html($link); ?></a></p><?php
		}

		if( $design == 'slider' ) {
			$func_name = 'carousel_' . $carousel_id;
			$autoplay = false;
			$hide_pagination_control = true;
			$hide_prev_next_buttons = false;
			$scroll_per_page = true;
			$wrap = false;
			$func_name = function() use( $carousel_id, $per_row, $autoplay, $hide_pagination_control, $hide_prev_next_buttons, $scroll_per_page, $wrap ) {
				
				$items = array();
				$items['desktop'] = ($per_row > 0) ? $per_row : 1;
				$items['desktop_small'] = ($items['desktop'] > 1) ? $items['desktop'] - 1 : 1;
				$items['tablet'] = ($items['desktop_small'] > 1) ? $items['desktop_small'] - 1 : 1;
				$items['mobile'] = ($items['tablet'] > 1) ? $items['tablet'] - 1 : 1;

				if($items['mobile'] > 2) {
					$items['mobile'] = 2;
				}

				?>

				<script type="text/javascript">
					jQuery( document ).ready(function( $ ) {
						$(".slider-<?php echo esc_js( $carousel_id ); ?> ul").owlCarousel({
				            items: <?php echo esc_js( $items['desktop'] ); ?>, 
				            itemsDesktop: [1199,<?php echo esc_js( $items['desktop'] ); ?>],
				            itemsDesktopSmall: [979,<?php echo esc_js( $items['desktop_small'] ); ?>],
				            itemsTablet: [768,<?php echo esc_js( $items['tablet'] ); ?>],
				            itemsMobile: [479,<?php echo esc_js( $items['mobile'] ); ?>],
				            lazyLoad : false,
				            autoPlay: <?php echo ($autoplay == 'yes') ? $speed : 'false'; ?>,
				            pagination: <?php echo ($hide_pagination_control == 'yes') ? 'false' : 'true'; ?>,
				            navigation: <?php echo ($hide_prev_next_buttons == 'yes') ? 'false' : 'true'; ?>,
				            scrollPerPage:  <?php echo ($scroll_per_page == 'yes') ? 'true' : 'false'; ?>,
				            navigationText:false,
				            rewindNav: <?php echo ($wrap == 'yes') ? 'true' : 'false'; ?>,
				            afterUpdate: function() {
				            	$(window).resize();
				            }
						});
		            	$(window).resize();
					});
				</script>

				<?php
			};

			add_action( 'basel_after_footer', $func_name );
		}

		echo '</div>';

		$output = ob_get_contents();
		ob_end_clean();

		return $output;

	}

	add_shortcode( 'basel_instagram', 'basel_shortcode_instagram' );
}

if( ! function_exists( 'basel_scrape_instagram' ) ) {
	function basel_scrape_instagram($username, $slice = 9) {
		$username = strtolower( $username );
		$by_hashtag = ( substr( $username, 0, 1) == '#' );
		if ( false === ( $instagram = get_transient( 'instagram-media-new-'.sanitize_title_with_dashes( $username ) ) ) ) {
			$request_param = ( $by_hashtag ) ? 'explore/tags/' . substr( $username, 1) : trim( $username );
			$remote = wp_remote_get( 'http://instagram.com/'. $request_param );
			if ( is_wp_error( $remote ) )
				return new WP_Error( 'site_down', __( 'Unable to communicate with Instagram.', 'wpiw' ) );
			if ( 200 != wp_remote_retrieve_response_code( $remote ) )
				return new WP_Error( 'invalid_response', __( 'Instagram did not return a 200.', 'wpiw' ) );
			$shards = explode( 'window._sharedData = ', $remote['body'] );
			$insta_json = explode( ';</script>', $shards[1] );
			$insta_array = json_decode( $insta_json[0], TRUE );
			if ( !$insta_array )
				return new WP_Error( 'bad_json', __( 'Instagram has returned invalid data.', 'wpiw' ) );
			// old style
			if ( isset( $insta_array['entry_data']['UserProfile'][0]['userMedia'] ) ) {
				$images = $insta_array['entry_data']['UserProfile'][0]['userMedia'];
				$type = 'old';
			// new style
			} else if ( isset( $insta_array['entry_data']['ProfilePage'][0]['user']['media']['nodes'] ) ) {
				$images = $insta_array['entry_data']['ProfilePage'][0]['user']['media']['nodes'];
				$type = 'new';
			} elseif( $by_hashtag && isset( $insta_array['entry_data']['TagPage'][0]['tag']['media']['nodes'] ) ) {
				$images = $insta_array['entry_data']['TagPage'][0]['tag']['media']['nodes'];
				$type = 'new';
			} else {
				return new WP_Error( 'bad_json_2', __( 'Instagram has returned invalid data.', 'wpiw' ) );
			}

			//ar($images);
			if ( !is_array( $images ) )
				return new WP_Error( 'bad_array', __( 'Instagram has returned invalid data.', 'wpiw' ) );
			$instagram = array();
			switch ( $type ) {
				case 'old':
					foreach ( $images as $image ) {
						if ( $image['user']['username'] == $username ) {
							$image['link']						  = $image['link'];
							$image['images']['thumbnail']		   = preg_replace( "/^http:/i", "", $image['images']['thumbnail'] );
							$image['images']['standard_resolution'] = preg_replace( "/^http:/i", "", $image['images']['standard_resolution'] );
							$image['images']['low_resolution']	  = preg_replace( "/^http:/i", "", $image['images']['low_resolution'] );
							$instagram[] = array(
								'description'   => $image['caption']['text'],
								'link'		  	=> $image['link'],
								'time'		  	=> $image['created_time'],
								'comments'	  	=> $image['comments']['count'],
								'likes'		 	=> $image['likes']['count'],
								'thumbnail'	 	=> $image['images']['thumbnail'],
								'large'		 	=> $image['images']['standard_resolution'],
								'small'		 	=> $image['images']['low_resolution'],
								'type'		  	=> $image['type']
							);
						}
					}
				break;
				default:
					foreach ( $images as $image ) {
						$image['thumbnail_src'] = preg_replace( "/^https:/i", "", $image['thumbnail_src'] );
						$image['thumbnail'] = str_replace( 's640x640', 's160x160', $image['thumbnail_src'] );
						$image['medium'] = str_replace( 's640x640', 's320x320', $image['thumbnail_src'] );
						$image['large'] = $image['thumbnail_src'];
						$image['display_src'] = preg_replace( "/^https:/i", "", $image['display_src'] );
						if ( $image['is_video'] == true ) {
							$type = 'video';
						} else {
							$type = 'image';
						}
						$caption = esc_html__( 'Instagram Image', 'xstore' );
						if ( ! empty( $image['caption'] ) ) {
							$caption = $image['caption'];
						}
						$instagram[] = array(
							'description'   => $caption,
							'link'		  	=> '//instagram.com/p/' . $image['code'],
							'time'		  	=> $image['date'],
							'comments'	  	=> $image['comments']['count'],
							'likes'		 	=> $image['likes']['count'],
							'thumbnail'	 	=> $image['thumbnail'],
							'medium'		=> $image['medium'],
							'large'			=> $image['large'],
							'original'		=> $image['display_src'],
							'type'		  	=> $type
						);
					}
				break;
			}
			// do not set an empty transient - should help catch private or empty accounts
			if ( ! empty( $instagram ) ) {
				$instagram = base64_encode( maybe_serialize( $instagram ) );
				set_transient( 'instagram-media-new-'.sanitize_title_with_dashes( $username ), $instagram, apply_filters( 'null_instagram_cache_time', HOUR_IN_SECONDS*2 ) );
			}
		}
		if ( ! empty( $instagram ) ) {
			$instagram = maybe_unserialize( base64_decode( $instagram ) );
			return array_slice( $instagram, 0, $slice );
		} else {
			return new WP_Error( 'no_images', __( 'Instagram did not return any images.', 'wpiw' ) );
		}
	}
}

if( !function_exists( 'basel_instagram_images_only' ) ) {
	function basel_instagram_images_only($media_item) {
		if ($media_item['type'] == 'image')
			return true;

		return false;
	}
}


/**
* ------------------------------------------------------------------------------------------------
* Google Map shortcode
* ------------------------------------------------------------------------------------------------
*/

if( ! function_exists( 'basel_shortcode_google_map' ) ) {
	function basel_shortcode_google_map( $atts, $content ) {
		$output = '';
		extract(shortcode_atts( array(
			'title' => '',
			'lat' => 45.9,
			'lon' => 10.9,
			'style_json' => '',
			'zoom' => 15,
			'height' => 400,
			'scroll' => 'no',
			'mask' => '',
			'google_key' => basel_get_opt( 'google_map_api_key' ),
			'el_class' => ''
		), $atts ));

		wp_enqueue_script( 'maplace' );
		wp_enqueue_script( 'google.map.api', 'https://maps.google.com/maps/api/js?key=' . $google_key . '', array(), '', false );

		if( $mask != '' ) {
			$el_class .= ' map-mask-' . $mask;
		}

		$id = rand(100,999);

		ob_start();

		?>		

			<?php if ( ! empty( $content ) ): ?>
				<div class="google-map-container <?php echo esc_attr( $el_class ); ?> google-map-container-with-content">
				
					<div class="basel-google-map-wrapper">
						<div class="basel-google-map with-content google-map-<?php echo esc_attr( $id ); ?>" style="height:<?php echo esc_attr( $height ); ?>px;"></div>
					</div>

					<?php echo do_shortcode( $content ); ?>

				</div>
			<?php else: ?>

				<div class="google-map-container <?php echo esc_attr( $el_class );?> ">
				
					<div class="basel-google-map-wrapper">
						<div class="basel-google-map without-content google-map-<?php echo esc_attr( $id ); ?>" style="height:<?php echo esc_attr( $height ); ?>px;"></div>
					</div>

				</div>

			<?php endif ?>

			<script type="text/javascript">
			
				jQuery(document).ready(function() {

					new Maplace({
						locations: [
						    {
								lat: <?php echo esc_js( $lat ); ?>,
								lon: <?php echo esc_js( $lon ); ?>,
								title: '<?php echo esc_js( $title ); ?>',
						        <?php if( $title != '' && empty($content) ): ?>html: '<h3 style="min-width:300px; text-align:center; margin:15px;"><?php echo esc_html( $title ); ?></h3>', <?php endif; ?>
						        animation: google.maps.Animation.DROP
						    }
						],
						controls_on_map: false,
						title: '<?php echo esc_js( $title ); ?>',
					    map_div: '.google-map-<?php echo esc_js( $id ); ?>',
					    start: 1,
					    map_options: {
					        zoom: <?php echo esc_js( $zoom ); ?>,
					        scrollwheel: <?php echo ($scroll == 'yes') ? 'true' : 'false'; ?>
					    },
					    <?php if($style_json != ''): ?>
					    styles: {
					        '<?php _e('Custom style', 'basel') ?>': <?php echo rawurldecode( base64_decode($style_json, true) ); ?>
					    }
					    <?php endif; ?>
					}).Load(); 

				});
			</script>
		<?php
		$output = ob_get_contents();
		ob_end_clean();

		return $output;


	}

	add_shortcode( 'basel_google_map', 'basel_shortcode_google_map' );
}
/**
* ------------------------------------------------------------------------------------------------
* Portfolio shortcode
* ------------------------------------------------------------------------------------------------
*/

if( ! function_exists( 'basel_shortcode_portfolio' ) ) {
	function basel_shortcode_portfolio( $atts ) {
		global $basel_portfolio_loop;
		$output = $title = $el_class = '';

	    $parsed_atts = shortcode_atts( array(
			'posts_per_page' => basel_get_opt( 'portoflio_per_page' ),
			'filters' => basel_get_opt( 'portoflio_filters' ),
			'categories' => '',
			'style' => basel_get_opt( 'portoflio_style' ),
			'columns' => basel_get_opt( 'projects_columns' ),
			'spacing' => basel_get_opt( 'portfolio_spacing' ),
			'full_width' => basel_get_opt( 'portfolio_full_width' ),
			'filters_bg' => '',
			'ajax_page' => '',
			'pagination' => basel_get_opt( 'portfolio_pagination' ),
			'basel_color_scheme' => basel_get_opt('portfolio_nav_color_scheme'),
			'el_class' => ''
		), $atts );

		extract( $parsed_atts );

		$encoded_atts = json_encode( $parsed_atts );

		// Load masonry script JS
		wp_enqueue_script( 'images-loaded' );
		//wp_enqueue_script( 'masonry' );
		wp_enqueue_script( 'isotope' );

		$is_ajax = (defined( 'DOING_AJAX' ) && DOING_AJAX);
		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
		if( $ajax_page > 1 ) $paged = $ajax_page;

		$args = array(
			'post_type' => 'portfolio',
			'posts_per_page' => $posts_per_page,
			'paged' => $paged
		);

		if( get_query_var('project-cat') != '' ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'project-cat',
					'field'    => 'slug',
					'terms'    => get_query_var('project-cat')
				),
			);
		}

		if( $categories != '' ) {

			$args['tax_query'] = array(
				array(
					'taxonomy' => 'project-cat',
					'field'    => 'term_id',
					'operator' => 'IN',
					'terms'    => $categories
				),
			);
		}

		$basel_portfolio_loop['columns'] = $columns;

		$query = new WP_Query( $args );

		ob_start();

		?>
			
			<?php if ( ! $is_ajax ): ?>
			<div class="site-content page-portfolio portfolio-layout-<?php echo ($full_width) ? 'full-width' : 'boxed'; ?> portfolio-<?php echo esc_attr( $style ); ?> col-sm-12" role="main">
			<?php endif ?>

				<?php if ( $query->have_posts() ) : ?>
					<?php if ( ! $is_ajax ): ?>
						<div class="row row-spacing-<?php echo esc_attr( $spacing ); ?> <?php if( $full_width ) echo 'vc_row vc_row-fluid vc_row-no-padding" data-vc-full-width="true" data-vc-full-width-init="true" data-vc-stretch-content="true'; ?>">

							<?php if ( ! is_tax() && $filters ): ?>
								<?php 
									$cats = get_terms( 'project-cat', array( 'parent' => $categories ) );
									if( ! is_wp_error( $cats ) && ! empty( $cats ) ) {
										?>
										<div class="col-sm-12 portfolio-filter color-scheme-<?php echo esc_attr( $basel_color_scheme ) ?> ">
											<ul class="masonry-filter list-inline text-center">
												<li><a href="#" data-filter="*" class="filter-active"><?php _e('All', 'basel'); ?></a></li>
											<?php
											foreach ($cats as $key => $cat) {
												?>
													<li><a href="#" data-filter=".proj-cat-<?php echo esc_attr( $cat->slug ); ?>"><?php echo esc_html( $cat->name ); ?></a></li>
												<?php
											}
											?>
											</ul>
										</div>
										<?php
									}
								 ?>

								 <?php if ( $filters_bg != '' ): ?>
								 	<style type="text/css">
										.portfolio-filter {
											background-color: <?php echo esc_html( $filters_bg ); ?>
										}
								 	</style>
								 <?php endif ?>

							<?php endif ?>

							<div class="clear"></div>

							<div class="masonry-container basel-portfolio-holder" data-atts="<?php echo esc_attr( $encoded_atts ); ?>" data-source="shortcode" data-paged="1">
					<?php endif ?>

							<?php /* The loop */ ?>
							<?php while ( $query->have_posts() ) : $query->the_post(); ?>
								<?php get_template_part( 'content', 'portfolio' ); ?>
							<?php endwhile; ?>

					<?php if ( ! $is_ajax ): ?>
							</div>
						</div>

						<div class="vc_row-full-width"></div>

						<?php 
							if ( $query->max_num_pages > 1 && !$is_ajax ) {
								?>
							    	<div class="portfolio-footer">
							    		<?php if ( $pagination == 'infinit' || $pagination == 'load_more'): ?>
							    			<a href="#" class="btn basel-portfolio-load-more load-on-<?php echo ($pagination == 'load_more') ? 'click' : 'scroll'; ?>"><?php _e('Load more posts', 'basel'); ?></a>
						    			<?php else: ?>
							    			<?php query_pagination( $query->max_num_pages ); ?>
							    		<?php endif ?>
							    	</div>
							    <?php 
							}
						?>
					<?php endif ?>

				<?php elseif ( ! $is_ajax ) : ?>
					<?php get_template_part( 'content', 'none' ); ?>
				<?php endif; ?>

			<?php if ( ! $is_ajax ): ?>
			</div><!-- .site-content -->
			<?php endif ?>
		<?php

		$output .= ob_get_clean();

		wp_reset_postdata();

	    if( $is_ajax ) {
	    	$output =  array(
	    		'items' => $output,
	    		'status' => ( $query->max_num_pages > $paged ) ? 'have-posts' : 'no-more-posts'
	    	);
	    }

		return $output;
	}

	add_shortcode( 'basel_portfolio', 'basel_shortcode_portfolio' );
}

if( ! function_exists( 'basel_get_portfolio_shortcode_ajax' ) ) {
	add_action( 'wp_ajax_basel_get_portfolio_shortcode', 'basel_get_portfolio_shortcode_ajax' );
	add_action( 'wp_ajax_nopriv_basel_get_portfolio_shortcode', 'basel_get_portfolio_shortcode_ajax' );
	function basel_get_portfolio_shortcode_ajax() {
		if( ! empty( $_POST['atts'] ) ) {
			$atts = $_POST['atts'];
			$paged = (empty($_POST['paged'])) ? 2 : (int) $_POST['paged'] + 1;
			$atts['ajax_page'] = $paged;

			$data = basel_shortcode_portfolio($atts);

			echo json_encode( $data );

			die();
		}
	}
}


/**
* ------------------------------------------------------------------------------------------------
* Blog shortcode
* ------------------------------------------------------------------------------------------------
*/

if( ! function_exists( 'basel_shortcode_blog' ) ) {
	function basel_shortcode_blog( $atts ) {
		global $basel_loop;
	    $parsed_atts = shortcode_atts( array(
	        'post_type'  => 'post',
	        'include'  => '',
	        'custom_query'  => '',
	        'taxonomies'  => '',
	        'pagination'  => '',
	        'parts_title'  => true,
	        'parts_meta'  => true,
	        'parts_text'  => true,
	        'parts_btn'  => true,
	        'items_per_page'  => 12,
	        'offset'  => '',
	        'orderby'  => 'date',
	        'order'  => 'DESC',
	        'meta_key'  => '',
	        'exclude'  => '',
	        'class'  => '',
	        'ajax_page' => '',
	        'img_size' => 'medium',
	        'blog_design'  => basel_get_opt( 'blog_design' ),
	        'blog_columns'  => basel_get_opt( 'blog_columns' ),
	    ), $atts );

	    extract( $parsed_atts );

	    $encoded_atts = json_encode( $parsed_atts );

	    $is_ajax = (defined( 'DOING_AJAX' ) && DOING_AJAX);

	    $output = '';

		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

		if( $ajax_page > 1 ) $paged = $ajax_page;

	    $args = array(
	    	'post_type' => 'post',
	    	'status' => 'published',
	    	'paged' => $paged,	
	    	'posts_per_page' => $items_per_page
		);

		if( $post_type == 'ids' && $include != '' ) {
			$args['post__in'] = explode(',', $include);
		}

		if( ! empty( $exclude ) ) {
			$args['post__not_in'] = explode(',', $exclude);
		}

		if( ! empty( $taxonomies ) ) {
			$taxonomy_names = get_object_taxonomies( 'post' );
			$terms = get_terms( $taxonomy_names, array(
				'orderby' => 'name',
				'include' => $taxonomies
			));

			if( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
				$args['tax_query'] = array('relation' => 'OR');
				foreach ($terms as $key => $term) {
					$args['tax_query'][] = array(
				        'taxonomy' => $term->taxonomy,     
				        'field' => 'slug',                  
				        'terms' => array( $term->slug ),   
				        'include_children' => true,        
				        'operator' => 'IN'  
					);
				}
			}
		}

		if( ! empty( $order ) ) {
			$args['order'] = $order;
		}

		if( ! empty( $offset ) ) {
			$args['offset'] = $offset;
		}

		if( ! empty( $meta_key ) ) {
			$args['meta_key'] = $meta_key;
		}

		if( ! empty( $orderby ) ) {
			$args['orderby'] = $orderby;
		}

	    $blog_query = new WP_Query($args);

	    ob_start();

	    $basel_loop['blog_design'] = $blog_design;
	    $basel_loop['img_size'] = $img_size;

	    $basel_loop['columns'] = $blog_columns;

	    $basel_loop['loop'] = 0;

	    $basel_loop['parts']['title'] = $parts_title;
	    $basel_loop['parts']['meta'] = $parts_meta;
	    $basel_loop['parts']['text'] = $parts_text;
	    if( ! $parts_btn )
	    	$basel_loop['parts']['btn'] = false;


	    if ( in_array( $blog_design, array( 'masonry', 'mask' ) ) ) {
	    	$class .= ' masonry-container';
	    }

	    if(!$is_ajax) echo '<div class="basel-blog-holder row ' . esc_attr( $class) . '" data-paged="1" data-atts="' . esc_attr( $encoded_atts ) . '">';

		while ( $blog_query->have_posts() ) {
			$blog_query->the_post();

			get_template_part( 'content' );
		}

    	if(!$is_ajax) echo '</div>';

		if ( $blog_query->max_num_pages > 1 && !$is_ajax && ! empty( $pagination ) ) {
			?>
		    	<div class="blog-footer">
		    		<?php if ($pagination == 'more-btn'): ?>
		    			<a href="#" class="btn basel-blog-load-more"><?php _e('Load more posts', 'basel'); ?></a>
	    			<?php elseif( $pagination == 'pagination' ): ?>
		    			<?php query_pagination( $blog_query->max_num_pages ); ?>
		    		<?php endif ?>
		    	</div>
		    <?php 
		}

	    unset( $basel_loop );
	    
	    wp_reset_postdata();

	    $output .= ob_get_clean();

	    ob_flush();

	    if( $is_ajax ) {
	    	$output =  array(
	    		'items' => $output,
	    		'status' => ( $blog_query->max_num_pages > $paged ) ? 'have-posts' : 'no-more-posts'
	    	);
	    }
	    
	    return $output;

	}

	add_shortcode( 'basel_blog', 'basel_shortcode_blog' );
}
if( ! function_exists( 'basel_get_blog_shortcode_ajax' ) ) {
	add_action( 'wp_ajax_basel_get_blog_shortcode', 'basel_get_blog_shortcode_ajax' );
	add_action( 'wp_ajax_nopriv_basel_get_blog_shortcode', 'basel_get_blog_shortcode_ajax' );
	function basel_get_blog_shortcode_ajax() {
		if( ! empty( $_POST['atts'] ) ) {
			$atts = $_POST['atts'];
			$paged = (empty($_POST['paged'])) ? 2 : (int) $_POST['paged'] + 1;
			$atts['ajax_page'] = $paged;

			$data = basel_shortcode_blog($atts);

			echo json_encode( $data );

			die();
		}
	}
}


/**
* ------------------------------------------------------------------------------------------------
* Override WP default gallery
* ------------------------------------------------------------------------------------------------
*/


if( ! function_exists( 'basel_gallery_shortcode' ) ) {

	function basel_gallery_shortcode( $attr ) {
		$post = get_post();

		static $instance = 0;
		$instance++;

		if ( ! empty( $attr['ids'] ) ) {
			// 'ids' is explicitly ordered, unless you specify otherwise.
			if ( empty( $attr['orderby'] ) ) {
				$attr['orderby'] = 'post__in';
			}
			$attr['include'] = $attr['ids'];
		}

		/**
		 * Filter the default gallery shortcode output.
		 *
		 * If the filtered output isn't empty, it will be used instead of generating
		 * the default gallery template.
		 *
		 * @since 2.5.0
		 *
		 * @see gallery_shortcode()
		 *
		 * @param string $output The gallery output. Default empty.
		 * @param array  $attr   Attributes of the gallery shortcode.
		 */
		$output = apply_filters( 'post_gallery', '', $attr );
		if ( $output != '' ) {
			return $output;
		}

		$html5 = current_theme_supports( 'html5', 'gallery' );
		$atts = shortcode_atts( array(
			'order'      => 'ASC',
			'orderby'    => 'menu_order ID',
			'id'         => $post ? $post->ID : 0,
			'itemtag'    => $html5 ? 'figure'     : 'dl',
			'icontag'    => $html5 ? 'div'        : 'dt',
			'captiontag' => $html5 ? 'figcaption' : 'dd',
			'columns'    => 3,
			'size'       => 'thumbnail',
			'include'    => '',
			'exclude'    => '',
			'link'       => ''
		), $attr, 'gallery' );

		$atts['link'] = 'file';

		$id = intval( $atts['id'] );

		if ( ! empty( $atts['include'] ) ) {
			$_attachments = get_posts( array( 'include' => $atts['include'], 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby'] ) );

			$attachments = array();
			foreach ( $_attachments as $key => $val ) {
				$attachments[$val->ID] = $_attachments[$key];
			}
		} elseif ( ! empty( $atts['exclude'] ) ) {
			$attachments = get_children( array( 'post_parent' => $id, 'exclude' => $atts['exclude'], 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby'] ) );
		} else {
			$attachments = get_children( array( 'post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby'] ) );
		}

		if ( empty( $attachments ) ) {
			return '';
		}

		if ( is_feed() ) {
			$output = "\n";
			foreach ( $attachments as $att_id => $attachment ) {
				$output .= wp_get_attachment_link( $att_id, $atts['size'], true ) . "\n";
			}
			return $output;
		}

		$itemtag = tag_escape( $atts['itemtag'] );
		$captiontag = tag_escape( $atts['captiontag'] );
		$icontag = tag_escape( $atts['icontag'] );
		$valid_tags = wp_kses_allowed_html( 'post' );
		if ( ! isset( $valid_tags[ $itemtag ] ) ) {
			$itemtag = 'dl';
		}
		if ( ! isset( $valid_tags[ $captiontag ] ) ) {
			$captiontag = 'dd';
		}
		if ( ! isset( $valid_tags[ $icontag ] ) ) {
			$icontag = 'dt';
		}

		$columns = intval( $atts['columns'] );
		$itemwidth = $columns > 0 ? floor(100/$columns) : 100;
		$float = is_rtl() ? 'right' : 'left';

		$selector = "gallery-{$instance}";

		$gallery_style = '';

		/**
		 * Filter whether to print default gallery styles.
		 *
		 * @since 3.1.0
		 *
		 * @param bool $print Whether to print default gallery styles.
		 *                    Defaults to false if the theme supports HTML5 galleries.
		 *                    Otherwise, defaults to true.
		 */
		if ( apply_filters( 'use_default_gallery_style', ! $html5 ) ) {
			$gallery_style = "
			<style type='text/css'>
				#{$selector} {
					margin: auto;
				}
				#{$selector} .gallery-item {
					float: {$float};
					margin-top: 10px;
					text-align: center;
					width: {$itemwidth}%;
				}
				#{$selector} img {
					max-width:100%;
				}
				#{$selector} .gallery-caption {
					margin-left: 0;
				}
			</style>\n\t\t";
		}

		$size_class = sanitize_html_class( $atts['size'] );
		$gallery_div = "<div id='$selector' class='gallery galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class}'>";

		/**
		 * Filter the default gallery shortcode CSS styles.
		 *
		 * @since 2.5.0
		 *
		 * @param string $gallery_style Default CSS styles and opening HTML div container
		 *                              for the gallery shortcode output.
		 */
		$output = apply_filters( 'gallery_style', $gallery_style . $gallery_div );

		$rows_width = $thumbs_heights = array();
		$row_i = 0;

		$i = 0;
		foreach ( $attachments as $id => $attachment ) {

			$attr = ( trim( $attachment->post_excerpt ) ) ? array( 'aria-describedby' => "$selector-$id" ) : '';
			if ( ! empty( $atts['link'] ) && 'file' === $atts['link'] ) {
				$image_output = wp_get_attachment_link( $id, $atts['size'], false, false, false, $attr );
			} elseif ( ! empty( $atts['link'] ) && 'none' === $atts['link'] ) {
				$image_output = wp_get_attachment_image( $id, $atts['size'], false, $attr );
			} else {
				$image_output = wp_get_attachment_link( $id, $atts['size'], true, false, false, $attr );
			}
			$image_meta  = wp_get_attachment_metadata( $id );

			$orientation = '';
			if ( isset( $image_meta['height'], $image_meta['width'] ) ) {
				$orientation = ( $image_meta['height'] > $image_meta['width'] ) ? 'portrait' : 'landscape';
			}
			//$output .= "<{$itemtag} class='gallery-item'>";
			$output .= "
					$image_output";
			if ( false && $captiontag && trim($attachment->post_excerpt) ) {
				$output .= "
					<{$captiontag} class='wp-caption-text gallery-caption' id='$selector-$id'>
					" . wptexturize($attachment->post_excerpt) . "
					</{$captiontag}>";
			}
			//$output .= "</{$itemtag}>";
			if ( ! $html5 && $columns > 0 && ++$i % $columns == 0 ) {
				//$output .= '<br style="clear: both" />';
			}

			if($i % $columns == 0) {
				$row_i++;
			}

			$thumb = wp_get_attachment_image_src($id, $atts['size']);

			$thumbs_heights[] = $thumb[2];

			//echo $thumb[1] . '<br>';
		}


		ob_start();


		$rowHeight = 250;
		$maxRowHeight = min($thumbs_heights);

		if( $maxRowHeight < $rowHeight) {
			$rowHeight = $maxRowHeight;
		}


		?>
			<script type="text/javascript">
				jQuery( document ).ready(function() {
					jQuery("#<?php echo esc_js( $selector ); ?>").justifiedGallery({
						rowHeight: <?php echo esc_js( $rowHeight ); ?>,
						maxRowHeight: <?php echo esc_js( $maxRowHeight ); ?>,
						margins: 1
					});
				});
			</script>
		<?php
		$output .= ob_get_clean();


		if ( ! $html5 && $columns > 0 && $i % $columns !== 0 ) {
			//$output .= "<br style='clear: both' />";
		}

		$output .= "
			</div>\n";

		return $output;
	}
		
	remove_shortcode('gallery');
	add_shortcode('gallery', 'basel_gallery_shortcode');

}

/**
* ------------------------------------------------------------------------------------------------
* Categories grid shortcode
* ------------------------------------------------------------------------------------------------
*/

if( ! function_exists( 'basel_shortcode_categories' )) {
	function basel_shortcode_categories($atts, $content) {
		global $woocommerce_loop;
		$extra_class = '';
		extract( shortcode_atts( array(
			'title' => __( 'Categories', 'basel' ),
			'number'     => null,
			'orderby'    => 'name',
			'order'      => 'ASC',
			'columns'    => '4',
			'hide_empty' => 1,
			'parent'     => '',
			'ids'        => '',
			'style'      => 'default',
			'slides_per_view' => '1',
			'wrap' => '',
			'autoplay' => 'no',
			'hide_pagination_control' => '',
			'hide_prev_next_buttons' => '',
			'spacing' => 30,
			'categories_design' => basel_get_opt( 'categories_design' ),
			'el_class' => ''
		), $atts ) );

		if ( isset( $ids ) ) {
			$ids = explode( ',', $ids );
			$ids = array_map( 'trim', $ids );
		} else {
			$ids = array();
		}

		$hide_empty = ( $hide_empty == true || $hide_empty == 1 ) ? 1 : 0;

		// get terms and workaround WP bug with parents/pad counts
		$args = array(
			'orderby'    => $orderby,
			'order'      => $order,
			'hide_empty' => $hide_empty,
			'include'    => $ids,
			'pad_counts' => true,
			'child_of'   => $parent
		);

		$product_categories = get_terms( 'product_cat', $args );

		if ( '' !== $parent ) {
			$product_categories = wp_list_filter( $product_categories, array( 'parent' => $parent ) );
		}

		if ( $hide_empty ) {
			foreach ( $product_categories as $key => $category ) {
				if ( $category->count == 0 ) {
					unset( $product_categories[ $key ] );
				}
			}
		}

		if ( $number ) {
			$product_categories = array_slice( $product_categories, 0, $number );
		}


		$columns = absint( $columns );

		if( $style == 'masonry' ) {
			$extra_class = 'categories-masonry';
		}
		
		if( $style == 'masonry-first' ) {
			$woocommerce_loop['different_sizes'] = array(1);
			$extra_class = 'categories-masonry';
			$columns = 4;
		}

		if( $categories_design != 'inherit' ) {
			$woocommerce_loop['categories_design'] = $categories_design;
		}

		$extra_class .= ' categories-space-' . $spacing;

		$woocommerce_loop['columns'] = $columns;
		$woocommerce_loop['style'] = $style;

		$carousel_id = 'cats-' . rand(100,999);
		
		ob_start();

		// Reset loop/columns globals when starting a new loop
		$woocommerce_loop['loop'] = '';

		if ( $product_categories ) {
			//woocommerce_product_loop_start();

			if( $style == 'carousel' ) {
				?>

				<div id="<?php echo esc_attr( $carousel_id ); ?>" class="vc_carousel_container">
					<div class="owl-carousel carousel-items">
							
						<?php foreach ( $product_categories as $category ): ?>
							<div class="category-item owl-carousel-item">
								<div class="owl-carousel-item-inner">	
									<?php 
										wc_get_template( 'content-product_cat.php', array(
											'category' => $category
										) );
									?>
								</div>
							</div>

						<?php endforeach; ?>
					</div>
				</div> <!-- end #<?php echo esc_html( $carousel_id ); ?> -->

				<?php 
					$func_name = 'carousel_' . $carousel_id;
					$func_name = function() use( $carousel_id, $slides_per_view, $autoplay, $hide_pagination_control, $hide_prev_next_buttons, $scroll_per_page, $wrap ) {
						
						$items = array();
						$items['desktop'] = ($slides_per_view > 0) ? $slides_per_view : 1;
						$items['desktop_small'] = ($items['desktop'] > 1) ? $items['desktop'] - 1 : 1;
						$items['tablet'] = ($items['desktop_small'] > 1) ? $items['desktop_small'] - 1 : 1;
						$items['mobile'] = ($items['tablet'] > 1) ? $items['tablet'] - 1 : 1;

						if($items['mobile'] > 2) {
							$items['mobile'] = 2;
						}

						?>

						<script type="text/javascript">
							jQuery( document ).ready(function( $ ) {
								$("#<?php echo esc_js( $carousel_id ); ?> .owl-carousel").owlCarousel({
						            items: <?php echo esc_js( $items['desktop'] ); ?>, 
						            itemsDesktop: [1199,<?php echo esc_js( $items['desktop'] ); ?>],
						            itemsDesktopSmall: [979,<?php echo esc_js( $items['desktop_small'] ); ?>],
						            itemsTablet: [768,<?php echo esc_js( $items['tablet'] ); ?>],
						            itemsMobile: [479,<?php echo esc_js( $items['mobile'] ); ?>],
						            lazyLoad : false,
						            autoPlay: <?php echo ($autoplay == 'yes') ? $speed : 'false'; ?>,
						            pagination: <?php echo ($hide_pagination_control == 'yes') ? 'false' : 'true'; ?>,
						            navigation: <?php echo ($hide_prev_next_buttons == 'yes') ? 'false' : 'true'; ?>,
						            scrollPerPage:  <?php echo ($scroll_per_page == 'yes') ? 'true' : 'false'; ?>,
						            navigationText:false,
						            rewindNav: <?php echo ($wrap == 'yes') ? 'true' : 'false'; ?>,
						            afterUpdate: function() {
						            	$(window).resize();
						            }
								});
				            	$(window).resize();
							});
						</script>

						<?php
					};

					add_action( 'basel_after_footer', $func_name );
			} else {

				foreach ( $product_categories as $category ) {
					wc_get_template( 'content-product_cat.php', array(
						'category' => $category
					) );
				}
			}


			//woocommerce_product_loop_end();
		}

		unset($woocommerce_loop['different_sizes']);

		woocommerce_reset_loop();

		if( $style == 'carousel' ) {
			return '<div class="woocommerce categories-style-'. esc_attr( $style ) . ' ' . esc_attr( $extra_class ) . '">' . ob_get_clean() . '</div>';
		} else {
			return '<div class="woocommerce row categories-style-'. esc_attr( $style ) . ' ' . esc_attr( $extra_class ) . ' columns-' . $columns . '">' . ob_get_clean() . '</div>';
		}

	}

	add_shortcode( 'basel_categories', 'basel_shortcode_categories' );

}

/**
* ------------------------------------------------------------------------------------------------
* Products widget shortcode
* ------------------------------------------------------------------------------------------------
*/

if( ! function_exists( 'basel_shortcode_products_widget' )) {
	function basel_shortcode_products_widget($atts, $content) {
		$output = $title = $el_class = '';
		extract( shortcode_atts( array(
			'title' => __( 'Products', 'basel' ),
			'el_class' => ''
		), $atts ) );

		$output = '<div class="widget_products' . $el_class . '">';
		$type = 'WC_Widget_Products';

		$args = array('widget_id' => rand(10,99));

		ob_start();
		the_widget( $type, $atts, $args );
		$output .= ob_get_clean();

		$output .= '</div>';

		return $output;

	}

	add_shortcode( 'basel_shortcode_products_widget', 'basel_shortcode_products_widget' );

}

/**
* ------------------------------------------------------------------------------------------------
* Counter shortcode
* ------------------------------------------------------------------------------------------------
*/

if( ! function_exists( 'basel_shortcode_animated_counter' )) {
	function basel_shortcode_animated_counter($atts) {
		$output = $label = $el_class = '';
		extract( shortcode_atts( array(
			'label' => '',
			'value' => 100,
			'time' => 1000,
			'el_class' => ''
		), $atts ) );


		ob_start();
		?>
			<div class="basel-counter <?php echo esc_attr( $el_class ); ?>">
				<span class="counter-value" data-state="new" data-final="<?php echo esc_attr( $value ); ?>"><?php echo esc_attr( $value ); ?></span>
				<?php if ($label != ''): ?>
					<span class="counter-label"><?php echo esc_html( $label ); ?></span>
				<?php endif ?>
			</div>

		<?php
		$output .= ob_get_clean();


		return $output;

	}

	add_shortcode( 'basel_counter', 'basel_shortcode_animated_counter' );

}

/**
* ------------------------------------------------------------------------------------------------
* Team member shortcode
* ------------------------------------------------------------------------------------------------
*/

if( ! function_exists( 'basel_shortcode_team_member' )) {
	function basel_shortcode_team_member($atts, $content = "") {
		$output = $title = $el_class = '';
		extract( shortcode_atts( array(
	        'name' => '',
	        'position' => '',
	        'email' => '',
	        'twitter' => '',
	        'facebook' => '',
	        'google_plus' => '',
	        'skype' => '',
	        'linkedin' => '',
	        'instagram' => '',
	        'img' => '',
	        'img_size' => '270x170',
			'el_class' => ''
		), $atts ) );

		$img_id = preg_replace( '/[^\d]/', '', $img );

		$img = wpb_getImageBySize( array( 'attach_id' => $img_id, 'thumb_size' => $img_size, 'class' => 'team-member-avatar-image' ) );
	    
	    $output .= '<div class="team-member '.$el_class.'">';

		    if(@$img['thumbnail'] != ''){

	            $output .= '<div class="member-image">';
	                $output .=  $img['thumbnail'];
	            $output .= '</div>';
		    }

	        $output .= '<div class="member-details">';
	            if($name != ''){
	                $output .= '<h4>' . $name . '</h4>';
	            }
			    if($position != ''){
				    $output .= '<h5 class="member-position">' . $position . '</h5>';
			    }
	            if($email != ''){
	                $output .= '<p class="member-email"><span>' . __('Email:', 'basel') . '</span> <a href="' . esc_url( $email ) . '">' . $email . '</a></p>';
	            }
			    $output .= '<div class="member-bio">';
			    $output .= do_shortcode($content);
			    $output .=  '</div>';
	    	$output .= '</div>';

            if ($linkedin != '' || $twitter != '' || $facebook != '' || $skype != '' || $google_plus != '' || $instagram != '') {
                $output .= '<div class="member-social"><ul class="social-icons">';
	                if ($facebook != '') {
	                    $output .= '<li><a href="'.esc_url( $facebook ).'"><i class="fa fa-facebook"></i></a></li>';
	                }
	                if ($twitter != '') {
	                    $output .= '<li><a href="'.esc_url( $twitter ).'"><i class="fa fa-twitter"></i></a></li>';
	                }
	                if ($google_plus != '') {
	                    $output .= '<li><a href="'.esc_url( $google_plus ).'"><i class="fa fa-google-plus"></i></a></li>';
	                }
	                if ($linkedin != '') {
	                    $output .= '<li><a href="'.esc_url( $linkedin ).'"><i class="fa fa-linkedin"></i></a></li>';
	                }
	                if ($skype != '') {
	                    $output .= '<li><a href="'.esc_url( $skype ).'"><i class="fa fa-skype"></i></a></li>';
	                }
	                if ($instagram != '') {
	                    $output .= '<li><a href="'.esc_url( $instagram ).'"><i class="fa fa-instagram"></i></a></li>';
	                }
                $output .= '</ul></div>';
            }


	    $output .= '</div>';
	    
	    
	    return $output;
	}

	add_shortcode( 'team_member', 'basel_shortcode_team_member' );

}

/**
* ------------------------------------------------------------------------------------------------
* Testimonials shortcodes
* ------------------------------------------------------------------------------------------------
*/

if( ! function_exists( 'basel_shortcode_testimonials' ) ) {
	function basel_shortcode_testimonials($atts = array(), $content = null) {
		$output = $class = $autoplay = '';
		extract(shortcode_atts( array(
			'layout' => 'slider', // grid slider
			'style' => 'standard', // standard boxed
			'align' => 'center', // left center
			'columns' => 3,
			'name' => '',
			'title' => '',
			'speed' => '5000',
			'slides_per_view' => '1',
			'wrap' => '',
			'autoplay' => 'no',
			'hide_pagination_control' => '',
			'hide_prev_next_buttons' => '',
			'scroll_per_page' => 'yes',
			'el_class' => ''
		), $atts ));

		$class .= ' testimonials-' . $layout;
		$class .= ' testimon-style-' . $style;
		$class .= ' testimon-columns-' . $columns;
		$class .= ' testimon-align-' . $align;

		$class .= ' ' . $el_class;

		$carousel_id = rand( 1000, 10000);

		ob_start(); ?>
			<div class="testimonials-wrapper">
				<?php if ( $title != '' ): ?>
					<h2 class="title slider-title"><?php echo esc_html( $title ); ?></h2>
				<?php endif ?>
				<div id="testimonials-<?php echo ($carousel_id); ?>" class="testimonials<?php echo esc_attr( $class ); ?>" >
					<?php echo do_shortcode( $content ); ?>
				</div>
			</div>

			<?php 
				if( $layout == 'slider' ) {
					$func_name = 'carousel_' . $carousel_id;
					$func_name = function() use( $carousel_id, $slides_per_view, $autoplay, $speed, $hide_pagination_control, $hide_prev_next_buttons, $scroll_per_page, $wrap ) {
						
						$items = array();
						$items['desktop'] = ($slides_per_view > 0) ? $slides_per_view : 1;
						$items['desktop_small'] = ($items['desktop'] > 1) ? $items['desktop'] - 1 : 1;
						$items['tablet'] = ($items['desktop_small'] > 1) ? $items['desktop_small'] : 1;
						$items['mobile'] = ($items['tablet'] > 2) ? $items['tablet'] - 2 : 1;

						if($items['mobile'] > 2) {
							$items['mobile'] = 2;
						}

						?>

						<script type="text/javascript">
							jQuery( document ).ready(function( $ ) {
								$("#testimonials-<?php echo esc_js( $carousel_id ); ?>").owlCarousel({
						            items: <?php echo esc_js( $items['desktop'] ); ?>, 
						            itemsDesktop: [1199,<?php echo esc_js( $items['desktop'] ); ?>],
						            itemsDesktopSmall: [979,<?php echo esc_js( $items['desktop_small'] ); ?>],
						            itemsTablet: [768,<?php echo esc_js( $items['tablet'] ); ?>],
						            itemsMobile: [479,<?php echo esc_js( $items['mobile'] ); ?>],
						            lazyLoad : false,
						            autoPlay: <?php echo ($autoplay == 'yes') ? $speed : 'false'; ?>,
						            pagination: <?php echo ($hide_pagination_control == 'yes') ? 'false' : 'true'; ?>,
						            navigation: <?php echo ($hide_prev_next_buttons == 'yes') ? 'false' : 'true'; ?>,
						            scrollPerPage:  <?php echo ($scroll_per_page == 'yes') ? 'true' : 'false'; ?>,
						            navigationText:false,
						            rewindNav: <?php echo ($wrap == 'yes') ? 'true' : 'false'; ?>,
						            afterUpdate: function() {
						            	$(window).resize();
						            }
								});
				            	$(window).resize();
							});
						</script>

						<?php
					};

					add_action( 'basel_after_footer', $func_name );
				}

			 ?>

		<?php
		$output = ob_get_contents();
		ob_end_clean();

		return $output; 
	}

	add_shortcode( 'testimonials', 'basel_shortcode_testimonials' );
}


if( ! function_exists( 'basel_shortcode_testimonial' ) ) {
	function basel_shortcode_testimonial($atts, $content) {
		if( ! function_exists( 'wpb_getImageBySize' ) ) return;
		$output = $class = '';
		extract(shortcode_atts( array(
			'image' => '',
			'img_size' => '100x100',
			'name' => '',
			'title' => '',
			'el_class' => ''
		), $atts ));

		$img_id = preg_replace( '/[^\d]/', '', $image );

		$img = wpb_getImageBySize( array( 'attach_id' => $img_id, 'thumb_size' => $img_size, 'class' => 'testimonial-avatar-image' ) );

		$class .= ' ' . $el_class;

		ob_start(); ?>
			
			<div class="testimonial<?php echo esc_attr( $class ); ?>" >
				<div class="testimonial-inner">
					<?php if ( $img['thumbnail'] != ''): ?>
						<div class="testimonial-avatar">
							<?php echo $img['thumbnail']; ?>
						</div>
					<?php endif ?>
					
					<div class="testimonial-content">
						<?php echo do_shortcode( $content ); ?>
						<footer>
							<?php echo esc_html( $name ); ?> 
							<span><?php echo esc_html( $title ); ?></span>
						</footer>
					</div>
				</div>
			</div>

		<?php
		$output = ob_get_contents();
		ob_end_clean();

		return $output; 
	}

	add_shortcode( 'testimonial', 'basel_shortcode_testimonial' );
}


/**
* ------------------------------------------------------------------------------------------------
* Pricing tables shortcodes
* ------------------------------------------------------------------------------------------------
*/

if( ! function_exists( 'basel_shortcode_pricing_tables' ) ) {
	function basel_shortcode_pricing_tables($atts = array(), $content = null) {
		$output = $class = $autoplay = '';
		extract(shortcode_atts( array(
			'el_class' => ''
		), $atts ));

		$class .= ' ' . $el_class;

		ob_start(); ?>
			<div class="pricing-tables-wrapper">
				<div class="pricing-tables<?php echo esc_attr( $class ); ?>" >
					<?php echo do_shortcode( $content ); ?>
				</div>
			</div>
		<?php
		$output = ob_get_contents();
		ob_end_clean();

		return $output; 
	}

	add_shortcode( 'pricing_tables', 'basel_shortcode_pricing_tables' );
}

if( ! function_exists( 'basel_shortcode_pricing_plan' ) ) {
	function basel_shortcode_pricing_plan($atts, $content) {
		global $wpdb, $post;
		if( ! function_exists( 'wpb_getImageBySize' ) ) return;
		$output = $class = '';
		extract(shortcode_atts( array(
			'name' => '',
			'subtitle' => '',
			'price_value' => '',
			'price_suffix' => 'per month',
			'currency' => '',
			'features_list' => '',
			'label' => '',
			'label_color' => 'red',
			'link' => '',
			'button_label' => '',
			'button_type' => 'custom',
			'id' => '',
			'el_class' => ''
		), $atts ));

		$class .= ' ' . $el_class;
		if( ! empty( $label ) ) {
			$class .= ' price-with-label label-color-' . $label_color;
		}

		$features = explode(PHP_EOL, $features_list);

		$product = false;

		if( $button_type == 'product' && ! empty( $id ) ) {
			$product_data = get_post( $id );
			$product = is_object( $product_data ) && in_array( $product_data->post_type, array( 'product', 'product_variation' ) ) ? wc_setup_product_data( $product_data ) : false;
		}

		ob_start(); ?>
			
			<div class="basel-price-table<?php echo esc_attr( $class ); ?>" >
				<div class="basel-plan">
					<div class="basel-plan-name">
						<span><?php echo  $name; ?></span>
						<?php if (! empty( $subtitle ) ): ?>
							<span class="price-subtitle"><?php echo  $subtitle; ?></span>
						<?php endif ?>
					</div>
				</div>
				<div class="basel-plan-inner">
					<?php if ( ! empty( $label ) ): ?>
						<div class="price-label"><span><?php echo  $label; ?></span></div>
					<?php endif ?>
					<div class="basel-plan-price">
						<span class="basel-price-currency">
							<?php echo  $currency; ?>
						</span>
						<span class="basel-price-value">
							<?php echo  $price_value; ?>
						</span>
						<span class="basel-price-suffix">
							<?php echo  $price_suffix; ?>
						</span>
					</div>
					<?php if ( count( $features ) > 0 ): ?>
						<div class="basel-plan-features">
							<?php foreach ($features as $value): ?>
								<div class="basel-plan-feature">
									<?php echo  $value; ?>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif ?>
					<div class="basel-plan-footer">
						<?php if ( $button_type == 'product' && $product ): ?>
							<?php woocommerce_template_loop_add_to_cart(  ); //array( 'quantity' => $atts['quantity'] )?>
						<?php else: ?>
							<a href="<?php echo esc_url( $link ); ?>" class="button price-plan-btn">
								<?php echo  $button_label; ?>
							</a>
						<?php endif ?>
					</div>
				</div>
			</div>

		<?php
		$output = ob_get_contents();
		ob_end_clean();

		if ( $button_type == 'product' ) {
			// Restore Product global in case this is shown inside a product post
			wc_setup_product_data( $post );
		}


		return $output; 
	}

	add_shortcode( 'pricing_plan', 'basel_shortcode_pricing_plan' );
}

/**
* ------------------------------------------------------------------------------------------------
* Mega Menu widget
* ------------------------------------------------------------------------------------------------
*/

if( ! function_exists( 'basel_shortcode_mega_menu' )) {
	function basel_shortcode_mega_menu($atts, $content) {
		$output = $title_html = '';
		extract(shortcode_atts( array(
			'title' => '',
			'nav_menu' => '',
			'style' => '',
			'color' => '',
			'basel_color_scheme' => 'light',
			'el_class' => ''
		), $atts ));

		$class = $el_class;

		if( $title != '' ) {
			$title_html = '<h5 class="widget-title color-scheme-' . $basel_color_scheme . '">' . $title . '</h5>';
		}

		$widget_id = 'widget-' . rand(100,999);


		//if( $nav_menu == '') return;

		ob_start(); ?>
			
			<div id="<?php echo esc_attr( $widget_id ); ?>" class="widget_nav_mega_menu shortcode-mega-menu <?php echo esc_attr( $class ); ?>">
				
				<?php echo $title_html; ?>

				<div class="basel-navigation">
					<?php
						wp_nav_menu( array( 
							'fallback_cb' => '', 
							'menu' => $nav_menu,
							'walker' => new BASEL_Mega_Menu_Walker()
						) );
					?>
				</div>	
			</div>

			<?php if ( $color != '' ): ?>
				<style type="text/css">
					#<?php echo esc_attr( $widget_id ); ?> {
						border-color: <?php echo esc_attr($color); ?>
					}
					#<?php echo esc_attr( $widget_id ); ?> .widget-title {
						background-color: <?php echo esc_attr($color); ?>
					}
				</style>
			<?php endif ?>
			
		<?php
		$output = ob_get_contents();
		ob_end_clean();

		return $output; 

	}

	add_shortcode( 'basel_mega_menu', 'basel_shortcode_mega_menu' );

}


/**
* ------------------------------------------------------------------------------------------------
* Widget user panel
* ------------------------------------------------------------------------------------------------
*/

if( ! function_exists( 'basel_shortcode_user_panel' )) {
	function basel_shortcode_user_panel($atts) {
		if( ! basel_woocommerce_installed() ) return;
		$click = $output = $title_out = $class = '';
		extract(shortcode_atts( array(
			'title' => '',
		), $atts ));

		$class .= ' ';

		$user = wp_get_current_user();

		ob_start(); ?>
				
			<div class="basel-user-panel<?php echo esc_attr( $class ); ?>">

				<?php if ( ! is_user_logged_in() ): ?>
					<?php printf(__('Please, <a href="%s">log in</a>', 'basel'), get_permalink( get_option('woocommerce_myaccount_page_id') )); ?>
				<?php else: ?>


					<div class="user-avatar">
						<?php echo get_avatar( $user->ID, 92 ); ?> 
					</div>

					<div class="user-info">
						<span><?php printf( __('Welcome, <strong>%s</strong>', 'basel'), $user->user_login ) ?></span>
						<a href="<?php echo esc_url( wp_logout_url( home_url('/') ) ); ?>" class="logout-link"><?php _e('Logout', 'basel'); ?></a>
					</div>

				<?php endif ?>
				
	
			</div>


		<?php
		$output = ob_get_contents();
		ob_end_clean();

		return $output; 
	}

	add_shortcode( 'user_panel', 'basel_shortcode_user_panel' );
}



/**
* ------------------------------------------------------------------------------------------------
* Widget with author info
* ------------------------------------------------------------------------------------------------
*/

if( ! function_exists( 'basel_shortcode_author_area' )) {
	function basel_shortcode_author_area($atts, $content) {
		if( ! function_exists( 'wpb_getImageBySize' ) ) return;
		$click = $output = $title_out = $class = '';
		extract(shortcode_atts( array(
			'title' => '',
			'image' => '',
			'img_size' => '800x600',
			'link' => '',
			'link_text' => '',
			'alignment' => 'left',
			'style' => '',
			'basel_color_scheme' => 'dark',
			'el_class' => ''
		), $atts ));

		$img_id = preg_replace( '/[^\d]/', '', $image );

		$img = wpb_getImageBySize( array( 'attach_id' => $img_id, 'thumb_size' => $img_size, 'class' => 'author-area-image' ) );


		$class .= ' text-' . $alignment;
		$class .= ' color-scheme-' . $basel_color_scheme;
		$class .= ' ' . $el_class;

		if( $title != '' ) {
			$title_out = '<h3 class="title author-title">' . esc_html($title) . '</h3>';
		}

		if( $link != '') {
			$link = '<a href="' . esc_url( $link ) . '">' . esc_html($link_text) . '</a>';
		}

		ob_start(); ?>
				
			<div class="author-area<?php echo esc_attr( $class ); ?>">

				<?php echo $title_out; ?>

				<div class="author-avatar">
					<?php echo $img['thumbnail']; ?>
				</div>
				
				<div class="author-info">
					<?php echo do_shortcode( $content ); ?>
				</div>
				
				<?php echo $link; ?>
	
			</div>


		<?php
		$output = ob_get_contents();
		ob_end_clean();

		return $output; 
	}

	add_shortcode( 'author_area', 'basel_shortcode_author_area' );
}

/**
* ------------------------------------------------------------------------------------------------
* Promo banner - image with text and hover effect
* ------------------------------------------------------------------------------------------------
*/

if( ! function_exists( 'basel_shortcode_promo_banner' )) {
	function basel_shortcode_promo_banner($atts, $content) {
		if( ! function_exists( 'wpb_getImageBySize' ) ) return;
		$click = $output = $class = '';
		extract(shortcode_atts( array(
			'image' => '',
			'img_size' => '800x600',
			'link' => '',
			'alignment' => 'left',
			'vertical_alignment' => 'top',
			'style' => '',
			'hover' => '',
			'basel_color_scheme' => 'light',
			'el_class' => ''
		), $atts ));


		//$img_id = preg_replace( '/[^\d]/', '', $image );

		$images = explode(',', $image);

		if( $link != '') {
			$class .= ' cursor-pointer'; 
		}

		$class .= ' text-' . $alignment;
		$class .= ' vertical-alignment-' . $vertical_alignment;
		$class .= ' banner-' . $style;
		$class .= ' hover-' . $hover;
		$class .= ' color-scheme-' . $basel_color_scheme;
		$class .= ' ' . $el_class;

		if ( count($images) > 1 ) {
			$class .= ' multi-banner';
		}

		ob_start(); ?>

			<div class="promo-banner<?php echo esc_attr( $class ); ?>" <?php if( ! empty( $link ) ): ?>onclick="window.location.href='<?php echo esc_js( $link ) ?>'"<?php endif; ?> >

				<div class="main-wrapp-img">
					<div class="banner-image">
						<?php if ( count($images) > 0 ): ?>
							<?php $i=0; foreach ($images as $img_id): $i++; ?>
								<?php $img = wpb_getImageBySize( array( 'attach_id' => $img_id, 'thumb_size' => $img_size, 'class' => 'promo-banner-image image-' . $i ) ); ?>
								<?php echo $img['thumbnail']; ?>
							<?php endforeach ?>
						<?php endif ?>
					</div>
				</div>
				
				<div class="wrapper-content-baner">
					<div class="banner-inner">
						<?php echo do_shortcode( $content ); ?>
					</div>
				</div>
				
			</div>


		<?php
		$output = ob_get_contents();
		ob_end_clean();

		return $output; 
	}

	add_shortcode( 'promo_banner', 'basel_shortcode_promo_banner' );

}


/**
* ------------------------------------------------------------------------------------------------
* Info box
* ------------------------------------------------------------------------------------------------
*/

if( ! function_exists( 'basel_shortcode_info_box' )) {
	function basel_shortcode_info_box($atts, $content) {
		if( ! function_exists( 'wpb_getImageBySize' ) ) return;
		$click = $output = $class = '';
		extract(shortcode_atts( array(
			'image' => '',
			'img_size' => '800x600',
			'link' => '',
			'alignment' => 'left',
			'image_alignment' => 'top',
			'style' => 'base',
			'hover' => '',
			'basel_color_scheme' => 'dark',
			'css' => 'light',
			'btn_text' => '',
			'btn_position' => 'hover',
			'btn_color' 	 => 'default',
			'btn_style'   	 => 'link',
			'btn_size' 		 => 'default',
			'el_class' => ''
		), $atts ));


		$images = explode(',', $image);

		if( $link != '') {
			$class .= ' cursor-pointer'; 
		}

		$class .= ' text-' . $alignment;
		$class .= ' icon-alignment-' . $image_alignment;
		$class .= ' box-style-' . $style;
		// $class .= ' hover-' . $hover;
		$class .= ' color-scheme-' . $basel_color_scheme;
		$class .= ' ' . $el_class;

		if ( count($images) > 1 ) {
			$class .= ' multi-icons';
		}

		if( ! empty( $btn_text ) ) {
			$class .= ' with-btn';
			$class .= ' btn-position-' . $btn_position;
		}

		if( function_exists( 'vc_shortcode_custom_css_class' ) ) {
			$class .= ' ' . vc_shortcode_custom_css_class( $css );
		}

		ob_start(); ?>
			<div class="basel-info-box<?php echo esc_attr( $class ); ?>" <?php if( ! empty( $link ) ): ?>onclick="window.location.href='<?php echo esc_js( $link ) ?>'"<?php endif; ?> >
				<?php if ( count($images) > 0 ): ?>
					<div class="box-icon-wrapper">
						<div class="info-box-icon">
								<?php $i=0; foreach ($images as $img_id): $i++; ?>
									<?php $img = wpb_getImageBySize( array( 'attach_id' => $img_id, 'thumb_size' => $img_size, 'class' => 'info-icon image-' . $i ) ); ?>
									<?php echo $img['thumbnail']; ?>
								<?php endforeach ?>
						</div>
					</div>
				<?php endif ?>
				<div class="info-box-content">
					<div class="info-box-inner">
						<?php 
							echo do_shortcode( $content ); 
							if( ! empty( $btn_text ) ) {
								printf( '<div class="info-btn-wrapper"><a href="%s" class="btn btn-style-link btn-color-primary info-box-btn">%s</a></div>', $link, $btn_text );
							}
						?>
					</div>
				</div>
			</div>
		<?php
		$output = ob_get_contents();
		ob_end_clean();

		return $output; 
	}

	add_shortcode( 'basel_info_box', 'basel_shortcode_info_box' );

}


/**
* ------------------------------------------------------------------------------------------------
* 3D view - images in 360 slider
* ------------------------------------------------------------------------------------------------
*/

if( ! function_exists( 'basel_shortcode_3d_view' )) {
	function basel_shortcode_3d_view($atts, $content) {
		if( ! function_exists( 'wpb_getImageBySize' ) ) return;
		$click = $output = $class = '';
		extract(shortcode_atts( array(
			'images' => '',
			'img_size' => 'full',
			'title' => '',
			'link' => '',
			'style' => '',
			'el_class' => ''
		), $atts ));

		$id = rand(100,999);

		$images = explode(',', $images);

		if( $link != '') {
			$class .= ' cursor-pointer'; 
		}

		$class .= ' ' . $el_class;

		$frames_count = count($images);

		if ( $frames_count < 2 ) return;

		$images_js_string = '';

		$width = $height = 0;

		ob_start(); ?>
			<div class="basel-threed-view<?php echo esc_attr( $class ); ?> threed-id-<?php echo esc_attr( $id ); ?>" <?php if( ! empty( $link ) ): ?>onclick="window.location.href='<?php echo esc_js( $link ) ?>'"<?php endif; ?> >
				<?php if ( ! empty( $title ) ): ?>
					<h3 class="threed-title"><span><?php echo ($title); ?></span></h3>
				<?php endif ?>
				<ul class="threed-view-images">
					<?php if ( count($images) > 0 ): ?>
						<?php $i=0; foreach ($images as $img_id): $i++; ?>
							<?php 
								$img = wpb_getImageBySize( array( 'attach_id' => $img_id, 'thumb_size' => $img_size, 'class' => 'threed-view-image image-' . $i ) );
								$width = $img['p_img_large'][1];
								$height = $img['p_img_large'][2];
								$images_js_string .= "'" . $img['p_img_large'][0] . "'"; 
								if( $i < $frames_count ) {
									$images_js_string .= ","; 
								}
							?>
						<?php endforeach ?>
					<?php endif ?>
				</ul>
			    <div class="spinner">
			        <span>0%</span>
			    </div>
			</div>
			<script type="text/javascript">
				jQuery(document).ready(function( $ ) {
				    $('.threed-id-<?php echo esc_attr( $id ); ?>').ThreeSixty({
				        totalFrames: <?php echo $frames_count; ?>,
				        endFrame: <?php echo $frames_count; ?>, 
				        currentFrame: 1, 
				        imgList: '.threed-view-images', 
				        progress: '.spinner',
				        imgArray: [<?php echo $images_js_string; ?>],
				        height: <?php echo $height ?>,
				        width: <?php echo $width ?>,
				        responsive: true,
				        navigation: true
				    });
				});
			</script>
		<?php
		$output = ob_get_contents();
		ob_end_clean();

		return $output; 
	}

	add_shortcode( 'basel_3d_view', 'basel_shortcode_3d_view' );
}


/**
* ------------------------------------------------------------------------------------------------
* Menu price element
* ------------------------------------------------------------------------------------------------
*/

if( ! function_exists( 'basel_shortcode_menu_price' )) {
	function basel_shortcode_menu_price($atts, $content) {
		if( ! function_exists( 'wpb_getImageBySize' ) ) return;
		$click = $output = $class = '';
		extract(shortcode_atts( array(
			'img_id' => '',
			'img_size' => 'full',
			'title' => '',
			'description' => '',
			'price' => '',
			'link' => '',
			'el_class' => ''
		), $atts ));


		if( $link != '') {
			$class .= ' cursor-pointer'; 
		}

		$class .= ' ' . $el_class;

		ob_start(); ?>
			<div class="basel-menu-price<?php echo esc_attr( $class ); ?>" <?php if( ! empty( $link ) ): ?>onclick="window.location.href='<?php echo esc_js( $link ) ?>'"<?php endif; ?> >
				<div class="menu-price-image">
					<?php 
						$img = wpb_getImageBySize( array( 'attach_id' => $img_id, 'thumb_size' => $img_size, 'class' => '' ) );
						echo $img['thumbnail'];
					?>
				</div>
				<div class="menu-price-description-wrapp">
					<?php if ( ! empty( $title ) ): ?>
						<h3 class="menu-price-title font-title"><span><?php echo ($title); ?></span></h3>
					<?php endif ?>
					<div class="menu-price-description">
						<div class="menu-price-details"><?php echo ($description); ?></div>
						<div class="menu-price-price"><?php echo ($price); ?></div>
					</div>
				</div>
			</div>
		<?php
		$output = ob_get_contents();
		ob_end_clean();

		return $output; 
	}

	add_shortcode( 'basel_menu_price', 'basel_shortcode_menu_price' );
}

/**
* ------------------------------------------------------------------------------------------------
* Countdown timer
* ------------------------------------------------------------------------------------------------
*/

if( ! function_exists( 'basel_shortcode_countdown_timer' )) {
	function basel_shortcode_countdown_timer($atts, $content) {
		if( ! function_exists( 'wpb_getImageBySize' ) ) return;
		$click = $output = $class = '';
		extract(shortcode_atts( array(
			'date' => '2018/12/12',
			'basel_color_scheme' => 'light',
			'size' => 'medium',
			'align' => 'center',
			'style' => 'base',
			'el_class' => ''
		), $atts ));

		$class .= ' ' . $el_class;
		$class .= ' color-scheme-' . $basel_color_scheme;
		$class .= ' timer-align-' . $align;
		$class .= ' timer-size-' . $size;
		$class .= ' timer-style-' . $style;

		ob_start(); ?>
			<div class="basel-countdown-timer<?php echo esc_attr( $class ); ?>">
				<div class="basel-timer" data-end-date="<?php echo esc_attr( $date ) ?>"></div>
			</div>
		<?php
		$output = ob_get_contents();
		ob_end_clean();

		return $output; 
	}

	add_shortcode( 'basel_countdown_timer', 'basel_shortcode_countdown_timer' );
}

/**
* ------------------------------------------------------------------------------------------------
* Share and follow buttons shortcode
* ------------------------------------------------------------------------------------------------
*/

if( ! function_exists( 'basel_shortcode_social' )) {
	function basel_shortcode_social($atts) {
		extract(shortcode_atts( array(
			'type' => 'share',
			'align' => 'center',
			'tooltip' => 'no',
			'style' => 'default', // circle colored 
			'size' => 'default', // circle colored 
			'el_class' => '',
		), $atts ));

		$target = "_blank";

		$thumb_id = get_post_thumbnail_id();
		$thumb_url = wp_get_attachment_image_src($thumb_id, 'thumbnail-size', true);

		ob_start();
		?>
		
			<ul class="social-nav">
				<?php if ( $type == 'share' || basel_get_opt( 'fb_link' ) != ''): ?>
					<li class="facebook"><a href="<?php echo ($type == 'follow') ? basel_get_opt( 'fb_link' ) : 'https://www.facebook.com/sharer/sharer.php?u=' . get_the_permalink(); ?>" target="<?php echo esc_attr( $target ); ?>" class="<?php if( $tooltip == "yes" ) echo 'basel-tooltip'; ?>"><i class="fa fa-facebook"></i></a></li>
				<?php endif ?>

				<?php if ( $type == 'share' || basel_get_opt( 'twitter_link' ) != ''): ?>
					<li class="twitter"><a href="<?php echo ($type == 'follow') ? basel_get_opt( 'twitter_link' ) : 'http://twitter.com/share?url=' . get_the_permalink(); ?>" target="<?php echo esc_attr( $target ); ?>" class="<?php if( $tooltip == "yes" ) echo 'basel-tooltip'; ?>"><i class="fa fa-twitter"></i></a></li>
				<?php endif ?>

				<?php if ( $type == 'share' || basel_get_opt( 'google_link' ) != ''): ?>
					<li class="google"><a href="<?php echo ($type == 'follow') ? basel_get_opt( 'google_link' ) : 'http://plus.google.com/share?url=' . get_the_permalink(); ?>" target="<?php echo esc_attr( $target ); ?>" class="<?php if( $tooltip == "yes" ) echo 'basel-tooltip'; ?>"><i class="fa fa-google-plus"></i></a></li>
				<?php endif ?>

				<?php if ( basel_get_opt( 'social_email' ) != ''): ?>
					<li class="email"><a href="mailto:<?php echo '?subject=' . __('Check this ', 'basel') . get_the_permalink(); ?>" target="<?php echo esc_attr( $target ); ?>" class="<?php if( $tooltip == "yes" ) echo 'basel-tooltip'; ?>"><i class="fa fa-envelope"></i></a></li>
				<?php endif ?>

				<?php if ( $type == 'follow' && basel_get_opt( 'isntagram_link' ) != ''): ?>
					<li class="instagram"><a href="<?php echo ($type == 'follow') ? basel_get_opt( 'isntagram_link' ) : '' . get_the_permalink(); ?>" target="<?php echo esc_attr( $target ); ?>" class="<?php if( $tooltip == "yes" ) echo 'basel-tooltip'; ?>"><i class="fa fa-instagram"></i></a></li>
				<?php endif ?>

				<?php if ( $type == 'follow' && basel_get_opt( 'youtube_link' ) != ''): ?>
					<li class="youtube"><a href="<?php echo ($type == 'follow') ? basel_get_opt( 'youtube_link' ) : '' . get_the_permalink(); ?>" target="<?php echo esc_attr( $target ); ?>" class="<?php if( $tooltip == "yes" ) echo 'basel-tooltip'; ?>"><i class="fa fa-youtube"></i></a></li>
				<?php endif ?>

				<?php if ( $type == 'share' || basel_get_opt( 'pinterest_link' ) != ''): ?>
					<li class="pinterest"><a href="<?php echo ($type == 'follow') ? basel_get_opt( 'pinterest_link' ) : 'http://pinterest.com/pin/create/button/?url=' . get_the_permalink() . '&media=' . $thumb_url[0]; ?>" target="<?php echo esc_attr( $target ); ?>" class="<?php if( $tooltip == "yes" ) echo 'basel-tooltip'; ?>"><i class="fa fa-pinterest"></i></a></li>
				<?php endif ?>

				<?php if ( $type == 'follow' && basel_get_opt( 'tumblr_link' ) != ''): ?>
					<li class="tumblr"><a href="<?php echo ($type == 'follow') ? basel_get_opt( 'tumblr_link' ) : '' . get_the_permalink(); ?>" target="<?php echo esc_attr( $target ); ?>" class="<?php if( $tooltip == "yes" ) echo 'basel-tooltip'; ?>"><i class="fa fa-tumblr"></i><?php _e('Tumblr', 'basel') ?></a></li>
				<?php endif ?>

				<?php if ( $type == 'follow' && basel_get_opt( 'linkedin_link' ) != ''): ?>
					<li class="linkedin"><a href="<?php echo ($type == 'follow') ? basel_get_opt( 'linkedin_link' ) : '' . get_the_permalink(); ?>" target="<?php echo esc_attr( $target ); ?>" class="<?php if( $tooltip == "yes" ) echo 'basel-tooltip'; ?>"><i class="fa fa-linkedin"></i><?php _e('LinkedIn', 'basel') ?></a></li>
				<?php endif ?>

				<?php if ( $type == 'follow' && basel_get_opt( 'vimeo_link' ) != ''): ?>
					<li class="vimeo"><a href="<?php echo ($type == 'follow') ? basel_get_opt( 'vimeo_link' ) : '' . get_the_permalink(); ?>" target="<?php echo esc_attr( $target ); ?>" class="<?php if( $tooltip == "yes" ) echo 'basel-tooltip'; ?>"><i class="fa fa-vimeo"></i><?php _e('Vimeo', 'basel') ?></a></li>
				<?php endif ?>

				<?php if ( $type == 'follow' && basel_get_opt( 'flickr_link' ) != ''): ?>
					<li class="flickr"><a href="<?php echo ($type == 'follow') ? basel_get_opt( 'flickr_link' ) : '' . get_the_permalink(); ?>" target="<?php echo esc_attr( $target ); ?>" class="<?php if( $tooltip == "yes" ) echo 'basel-tooltip'; ?>"><i class="fa fa-flickr"></i><?php _e('Flickr', 'basel') ?></a></li>
				<?php endif ?>

				<?php if ( $type == 'follow' && basel_get_opt( 'github_link' ) != ''): ?>
					<li class="github"><a href="<?php echo ($type == 'follow') ? basel_get_opt( 'github_link' ) : '' . get_the_permalink(); ?>" target="<?php echo esc_attr( $target ); ?>" class="<?php if( $tooltip == "yes" ) echo 'basel-tooltip'; ?>"><i class="fa fa-github"></i><?php _e('GitHub', 'basel') ?></a></li>
				<?php endif ?>

				<?php if ( $type == 'follow' && basel_get_opt( 'dribbble_link' ) != ''): ?>
					<li class="dribbble"><a href="<?php echo ($type == 'follow') ? basel_get_opt( 'dribbble_link' ) : '' . get_the_permalink(); ?>" target="<?php echo esc_attr( $target ); ?>" class="<?php if( $tooltip == "yes" ) echo 'basel-tooltip'; ?>"><i class="fa fa-dribbble"></i><?php _e('Dribbble', 'basel') ?></a></li>
				<?php endif ?>

				<?php if ( $type == 'follow' && basel_get_opt( 'behance_link' ) != ''): ?>
					<li class="behance"><a href="<?php echo ($type == 'follow') ? basel_get_opt( 'behance_link' ) : '' . get_the_permalink(); ?>" target="<?php echo esc_attr( $target ); ?>" class="<?php if( $tooltip == "yes" ) echo 'basel-tooltip'; ?>"><i class="fa fa-behance"></i><?php _e('Behance', 'basel') ?></a></li>
				<?php endif ?>

				<?php if ( $type == 'follow' && basel_get_opt( 'soundcloud_link' ) != ''): ?>
					<li class="soundcloud"><a href="<?php echo ($type == 'follow') ? basel_get_opt( 'soundcloud_link' ) : '' . get_the_permalink(); ?>" target="<?php echo esc_attr( $target ); ?>" class="<?php if( $tooltip == "yes" ) echo 'basel-tooltip'; ?>"><i class="fa fa-soundcloud"></i><?php _e('Soundcloud', 'basel') ?></a></li>
				<?php endif ?>

				<?php if ( $type == 'follow' && basel_get_opt( 'spotify_link' ) != ''): ?>
					<li class="spotify"><a href="<?php echo ($type == 'follow') ? basel_get_opt( 'spotify_link' ) : '' . get_the_permalink(); ?>" target="<?php echo esc_attr( $target ); ?>" class="<?php if( $tooltip == "yes" ) echo 'basel-tooltip'; ?>"><i class="fa fa-spotify"></i><?php _e('Spotify', 'basel') ?></a></li>
				<?php endif ?>

			</ul>
	
		<?php
		$output = ob_get_contents();
		ob_end_clean();

		return $output; 
	}

	add_shortcode( 'social_buttons', 'basel_shortcode_social' );
}



/**
* ------------------------------------------------------------------------------------------------
* Shortcode function to display posts teaser
* ------------------------------------------------------------------------------------------------
*/

if( ! function_exists( 'basel_shortcode_posts_teaser' )) {
	function basel_shortcode_posts_teaser($atts, $query = false) {
		global $woocommerce_loop;
		$posts_query = $el_class = $args = $my_query = $title_out = $output = '';
		$posts = array();
		extract( shortcode_atts( array(
			'el_class' => '',
			'posts_query' => '',
			'style' => 'default',
			'title' => '',
		), $atts ) );

		if( ! $query ) {
			list( $args, $query ) = vc_build_loop_query( $posts_query ); //
		}

		$carousel_id = 'teaser-' . rand(100,999);

		if( $title != '' ) {
			$title_out = '<h3 class="title teaser-title">' . $title . '</h3>';
		}

		ob_start();

		if($query->have_posts()) {
			echo $title_out;
			?>
				<div id="<?php echo esc_html( $carousel_id ); ?>">
					<div class="posts-teaser teaser-style-<?php echo esc_attr( $style ); ?> <?php echo esc_attr( $el_class ); ?>">

						<?php
							$_i = 0;
							while ( $query->have_posts() ) {
								$_i++;
								$query->the_post(); // Get post from query
								?>
									<div class="post-teaser-item teaser-item-<?php echo esc_attr( $_i ); ?>">

										<?php if( has_post_thumbnail() ) {
											?>
												<a href="<?php echo esc_url( get_permalink() ); ?>"><?php the_post_thumbnail( ( $_i == 1 ) ? 'large' : 'medium' ); ?></a>
											<?php
										} ?>

										<a href="<?php echo esc_url( get_permalink() ); ?>" class="post-title"><?php the_title(); ?></a> 

										<?php basel_post_meta(array(
											'author' => 0,
											'labels' => 1,
											'cats' => 0,
											'tags' => 0
										)); ?>

									</div>
								<?php
							}	
						?>

					</div> <!-- end posts-teaser -->
				</div> <!-- end #<?php echo esc_html( $carousel_id ); ?> -->
				<?php

		}
		wp_reset_postdata();

		$output = ob_get_contents();
		ob_end_clean();

		return $output; 
	}

	add_shortcode( 'basel_posts_teaser', 'basel_shortcode_posts_teaser' );
}



/**
* ------------------------------------------------------------------------------------------------
* Shortcode function to display any owl slider
* ------------------------------------------------------------------------------------------------
*/

if( ! function_exists( 'basel_shortcode_carousel' )) {
	function basel_shortcode_carousel($atts, $content = '') {
		global $woocommerce_loop;
		$el_class = $speed = '';
		$slides_per_view = $wrap = $scroll_per_page = $title_out = '';
		$autoplay = $hide_pagination_control = $hide_prev_next_buttons = $output = '';
		$posts = array();
		extract( shortcode_atts( array(
			'el_class' => '',
			'posts_query' => '',
			'speed' => '5000',
			'slides_per_view' => '1',
			'product_hover'  => basel_get_opt( 'products_hover' ),
			'wrap' => '',
			'autoplay' => 'no',
			'hide_pagination_control' => '',
			'hide_prev_next_buttons' => '',
			'title' => '',
			'scroll_per_page' => 'yes'
		), $atts ) );

		$carousel_id = 'carousel-' . rand(100,999);

		if( $title != '' ) {
			$title_out = '<h3 class="title slider-title">' . $title . '</h3>';
		}

		$woocommerce_loop['product_hover']   = $product_hover;

		ob_start();

		echo $title_out;
		?>
			<div id="<?php echo esc_attr( $carousel_id ); ?>" class="vc_carousel_container">
				<div class="owl-carousel carousel-items <?php echo esc_attr( $el_class ); ?>">
					<div class="product-item owl-carousel-item">
						<div class="owl-carousel-item-inner">	
							
							<?php echo do_shortcode( $content ); ?>

						</div>
					</div>
				</div> <!-- end product-items -->
			</div> <!-- end #<?php echo esc_html( $carousel_id ); ?> -->

		<?php 
			$func_name = 'carousel_' . $carousel_id;
			$func_name = function() use( $carousel_id, $slides_per_view, $autoplay, $hide_pagination_control, $hide_prev_next_buttons, $scroll_per_page, $wrap ) {
				
				$items = array();
				$items['desktop'] = ($slides_per_view > 0) ? $slides_per_view : 1;
				$items['desktop_small'] = ($items['desktop'] > 1) ? $items['desktop'] - 1 : 1;
				$items['tablet'] = ($items['desktop_small'] > 1) ? $items['desktop_small'] - 1 : 1;
				$items['mobile'] = ($items['tablet'] > 1) ? $items['tablet'] - 1 : 1;

				if($items['mobile'] > 2) {
					$items['mobile'] = 2;
				}

				?>

				<script type="text/javascript">
					jQuery( document ).ready(function( $ ) {
						$("#<?php echo esc_js( $carousel_id ); ?> .owl-carousel").owlCarousel({
				            items: <?php echo esc_js( $items['desktop'] ); ?>, 
				            itemsDesktop: [1199,<?php echo esc_js( $items['desktop'] ); ?>],
				            itemsDesktopSmall: [979,<?php echo esc_js( $items['desktop_small'] ); ?>],
				            itemsTablet: [768,<?php echo esc_js( $items['tablet'] ); ?>],
				            itemsMobile: [479,<?php echo esc_js( $items['mobile'] ); ?>],
				            lazyLoad : false,
				            autoPlay: <?php echo ($autoplay == 'yes') ? $speed : 'false'; ?>,
				            pagination: <?php echo ($hide_pagination_control == 'yes') ? 'false' : 'true'; ?>,
				            navigation: <?php echo ($hide_prev_next_buttons == 'yes') ? 'false' : 'true'; ?>,
				            scrollPerPage:  <?php echo ($scroll_per_page == 'yes') ? 'true' : 'false'; ?>,
				            navigationText:false,
				            rewindNav: <?php echo ($wrap == 'yes') ? 'true' : 'false'; ?>,
				            afterUpdate: function() {
				            	$(window).resize();
				            }
						});
		            	$(window).resize();
					});
				</script>

				<?php
			};

			add_action( 'basel_after_footer', $func_name );


		$output = ob_get_contents();
		ob_end_clean();

		return $output; 
	}

	//add_shortcode( 'basel_carousel', 'basel_shortcode_carousel');
}


if( ! function_exists( 'basel_shortcode_carousel_item' )) {
	function basel_shortcode_carousel_item($atts, $content = '') {
		$el_class = '';

		extract( shortcode_atts( array(
			'el_class' => '',
		), $atts ) );



		ob_start();
		?>
			<div class="carousel-item">
				
			</div>
		<?php

		$output = ob_get_contents();
		ob_end_clean();

		return $output; 
	}

	//add_shortcode( 'basel_carousel_item', 'basel_shortcode_carousel_item');
}
/**
* ------------------------------------------------------------------------------------------------
* Shortcode function to display posts as a slider or as a grid
* ------------------------------------------------------------------------------------------------
*/

if( ! function_exists( 'basel_shortcode_posts' ) ) {

	function basel_shortcode_posts( $atts ) {
		return basel_generate_posts_slider( $atts );
	}

	add_shortcode( 'basel_posts', 'basel_shortcode_posts' );
}

if( ! function_exists( 'basel_generate_posts_slider' )) {
	function basel_generate_posts_slider($atts, $query = false) {
		global $woocommerce_loop, $basel_loop;
		$posts_query = $el_class = $args = $my_query = $speed = '';
		$slides_per_view = $wrap = $scroll_per_page = $title_out = '';
		$autoplay = $hide_pagination_control = $hide_prev_next_buttons = $output = '';
		$posts = array();
		extract( shortcode_atts( array(
			'el_class' => '',
			'posts_query' => '',
	        'img_size' => 'large',
			'speed' => '5000',
			'slides_per_view' => '1',
			'wrap' => '',
			'autoplay' => 'no',
			'hide_pagination_control' => '',
			'hide_prev_next_buttons' => '',
			'title' => '',
			'scroll_per_page' => 'yes'
		), $atts ) );

		$basel_loop['img_size'] = $img_size;

		if( ! $query ) {
			list( $args, $query ) = vc_build_loop_query( $posts_query ); //
		}

		$carousel_id = 'carousel-' . rand(100,999);

		if( $title != '' ) {
			$title_out = '<h3 class="title slider-title">' . $title . '</h3>';
		}

		ob_start();

		if($query->have_posts()) {
			echo $title_out;
			?>
				<div id="<?php echo esc_attr( $carousel_id ); ?>" class="vc_carousel_container">
					<div class="owl-carousel product-items <?php echo esc_attr( $el_class ); ?>">

						<?php
							while ( $query->have_posts() ) {
								$query->the_post(); // Get post from query
								?>
									<div class="product-item owl-carousel-item">
										<div class="owl-carousel-item-inner">	

											<?php if ( get_post_type() == 'product' && basel_woocommerce_installed() ): ?>
												<?php $woocommerce_loop['slider'] = true; ?>
												<?php woocommerce_get_template_part('content-product'); ?>
											<?php else: ?>
												<?php get_template_part( 'content', 'slider' ); ?>
											<?php endif ?>

										</div>
									</div>
								<?php
							}	

							unset( $woocommerce_loop['slider'] );

						?>

					</div> <!-- end product-items -->
				</div> <!-- end #<?php echo esc_html( $carousel_id ); ?> -->

				<?php 
					$func_name = 'carousel_' . $carousel_id;
					$func_name = function() use( $carousel_id, $slides_per_view, $autoplay, $speed, $hide_pagination_control, $hide_prev_next_buttons, $scroll_per_page, $wrap ) {
						
						$items = array();
						$items['desktop'] = ($slides_per_view > 0) ? $slides_per_view : 1;
						$items['desktop_small'] = ($items['desktop'] > 1) ? $items['desktop'] - 1 : 1;
						$items['tablet'] = ($items['desktop_small'] > 1) ? $items['desktop_small'] : 1;
						$items['mobile'] = ($items['tablet'] > 2) ? $items['tablet'] - 2 : 1;

						if($items['mobile'] > 2) {
							$items['mobile'] = 2;
						}

						?>

						<script type="text/javascript">
							jQuery( document ).ready(function( $ ) {
								$("#<?php echo esc_js( $carousel_id ); ?> .owl-carousel").owlCarousel({
						            items: <?php echo esc_js( $items['desktop'] ); ?>, 
						            itemsDesktop: [1199,<?php echo esc_js( $items['desktop'] ); ?>],
						            itemsDesktopSmall: [979,<?php echo esc_js( $items['desktop_small'] ); ?>],
						            itemsTablet: [768,<?php echo esc_js( $items['tablet'] ); ?>],
						            itemsMobile: [479,<?php echo esc_js( $items['mobile'] ); ?>],
						            lazyLoad : false,
						            autoPlay: <?php echo ($autoplay == 'yes') ? $speed : 'false'; ?>,
						            pagination: <?php echo ($hide_pagination_control == 'yes') ? 'false' : 'true'; ?>,
						            navigation: <?php echo ($hide_prev_next_buttons == 'yes') ? 'false' : 'true'; ?>,
						            scrollPerPage:  <?php echo ($scroll_per_page == 'yes') ? 'true' : 'false'; ?>,
						            navigationText:false,
						            rewindNav: <?php echo ($wrap == 'yes') ? 'true' : 'false'; ?>,
						            afterUpdate: function() {
						            	$(window).resize();
						            }
								});
				            	$(window).resize();
							});
						</script>

						<?php
					};

					add_action( 'basel_after_footer', $func_name );

		}
		wp_reset_postdata();
		unset($basel_loop['img_size']);

		$output = ob_get_contents();
		ob_end_clean();

		return $output; 
	}
}


/**
* ------------------------------------------------------------------------------------------------
* Shortcode function to display posts as a slider or as a grid
* ------------------------------------------------------------------------------------------------
*/

if( ! function_exists( 'basel_shortcode_products' ) ) {
	add_shortcode( 'basel_products', 'basel_shortcode_products' );
	function basel_shortcode_products($atts, $query = false) {
		global $woocommerce_loop, $basel_loop;
	    $parsed_atts = shortcode_atts( array(
	        'post_type'  => 'product',
	        'layout' => 'grid',
	        'include'  => '',
	        'custom_query'  => '',
	        'taxonomies'  => '',
	        'pagination'  => '',
	        'items_per_page'  => 12,
			'product_hover'  => basel_get_opt( 'products_hover' ),
	        'columns'  => 4,
	        'sale_countdown'  => 0,
	        'offset'  => '',
	        'orderby'  => 'date',
	        'order'  => 'DESC',
	        'meta_key'  => '',
	        'exclude'  => '',
	        'class'  => '',
	        'ajax_page' => '',
			'speed' => '5000',
			'slides_per_view' => '1',
			'wrap' => '',
			'autoplay' => 'no',
			'hide_pagination_control' => '',
			'hide_prev_next_buttons' => '',
			'scroll_per_page' => 'yes',
	        'img_size' => 'shop_catalog',
	    ), $atts );

	    extract( $parsed_atts );

	    $encoded_atts = json_encode( $parsed_atts );

		$basel_loop['img_size'] = $img_size;

	    $is_ajax = (defined( 'DOING_AJAX' ) && DOING_AJAX);

		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

		if( $ajax_page > 1 ) $paged = $ajax_page;

		$ordering_args = WC()->query->get_catalog_ordering_args( $orderby, $order );

		$meta_query   = WC()->query->get_meta_query();

		if( $post_type == 'featured' ) {
			$meta_query[] = array(
				'key'   => '_featured',
				'value' => 'yes'
			);
		}

		if( $orderby == 'post__in' ) {
			$ordering_args['orderby'] = $orderby;
		}

	    $args = array(
	    	'post_type' 			=> 'product',
	    	'status' 				=> 'published',
			'ignore_sticky_posts' 	=> 1,
	    	'paged' 			  	=> $paged,	
			'orderby'             	=> $ordering_args['orderby'],
			'order'               	=> $ordering_args['order'],
	    	'posts_per_page' 		=> $items_per_page,
	    	'meta_query' 			=> $meta_query
		);
		if( $post_type == 'ids' && $include != '' ) {
			$args['post__in'] = explode(',', $include);
		}

		if( ! empty( $exclude ) ) {
			$args['post__not_in'] = explode(',', $exclude);
		}

		if( ! empty( $taxonomies ) ) {
			$taxonomy_names = get_object_taxonomies( 'product' );
			$terms = get_terms( $taxonomy_names, array(
				'orderby' => 'name',
				'include' => $taxonomies
			));

			if( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
				$args['tax_query'] = array('relation' => 'OR');
				foreach ($terms as $key => $term) {
					$args['tax_query'][] = array(
				        'taxonomy' => $term->taxonomy,     
				        'field' => 'slug',                  
				        'terms' => array( $term->slug ),   
				        'include_children' => true,        
				        'operator' => 'IN'  
					);
				}
			}
		}

		if( ! empty( $order ) ) {
			$args['order'] = $order;
		}

		if( ! empty( $offset ) ) {
			$args['offset'] = $offset;
		}


		if( $post_type == 'sale' ) {
			$args['post__in'] = array_merge( array( 0 ), wc_get_product_ids_on_sale() );
		}

		if( $post_type == 'bestselling' ) {
			$args['orderby'] = 'meta_value_num';
			$args['meta_key'] = 'total_sales';
		}

		$woocommerce_loop['timer']   = $sale_countdown;
		$woocommerce_loop['product_hover']   = $product_hover;

		$products                    = new WP_Query( $args );

		// Simple products carousel
		if( $layout == 'carousel' ) return basel_generate_posts_slider( $parsed_atts, $products );


		$woocommerce_loop['columns'] = $columns;
		$woocommerce_loop['masonry'] = false;

		if ( $pagination == 'more-btn' ) {
			$woocommerce_loop['masonry'] = true;
		}

		if ( $pagination != 'arrows' ) {
			$woocommerce_loop['loop'] = $items_per_page * ( $paged - 1 );
		}

		$class .= ' pagination-' . $pagination;
		$class .= ' grid-columns-' . $columns;
		if( $woocommerce_loop['masonry'] ) {
			$class .= ' grid-masonry';
		}

		ob_start();

		if(!$is_ajax) echo '<div class="basel-products-element">';

	    if(!$is_ajax && $pagination != 'more-btn') echo '<div class="basel-products-loader"></div>';
	    
	    if(!$is_ajax) echo '<div class="products elements-grid row basel-products-holder ' . esc_attr( $class) . '" data-paged="1" data-atts="' . esc_attr( $encoded_atts ) . '">';
		
		if ( $products->have_posts() ) : 
			while ( $products->have_posts() ) : 
				$products->the_post();
				wc_get_template_part( 'content', 'product' ); 
			endwhile; 
		endif;

    	if(!$is_ajax) echo '</div>';

		woocommerce_reset_loop();
		wp_reset_postdata();

		if ( $products->max_num_pages > 1 && !$is_ajax ) {
			?>
		    	<div class="products-footer">
		    		<?php if ($pagination == 'more-btn'): ?>
		    			<a href="#" class="btn basel-products-load-more"><?php _e('Load more products', 'basel'); ?></a>
		    		<?php elseif ($pagination == 'arrows'): ?>
		    			<a href="#" class="btn basel-products-load-prev disabled"><?php _e('Load previous products', 'basel'); ?></a>
		    			<a href="#" class="btn basel-products-load-next"><?php _e('Load next products', 'basel'); ?></a>
		    		<?php endif ?>
		    	</div>
		    <?php 
		}

    	if(!$is_ajax) echo '</div>';

		$output = ob_get_clean();

	    if( $is_ajax ) {
	    	$output =  array(
	    		'items' => $output,
	    		'status' => ( $products->max_num_pages > $paged ) ? 'have-posts' : 'no-more-posts'
	    	);
	    }
	    
	    return $output;

	}



}

if( ! function_exists( 'basel_get_shortcode_products_ajax' ) ) {
	add_action( 'wp_ajax_basel_get_products_shortcode', 'basel_get_shortcode_products_ajax' );
	add_action( 'wp_ajax_nopriv_basel_get_products_shortcode', 'basel_get_shortcode_products_ajax' );
	function basel_get_shortcode_products_ajax() {
		if( ! empty( $_POST['atts'] ) ) {
			$atts = $_POST['atts'];
			$paged = (empty($_POST['paged'])) ? 2 : (int) $_POST['paged'];
			$atts['ajax_page'] = $paged;

			$data = basel_shortcode_products($atts);

			echo json_encode( $data );

			die();
		}
	}
}

// Register shortcode [html_block id="111"]
add_shortcode('html_block', 'basel_html_block_shortcode');

if( ! function_exists( 'basel_html_block_shortcode' ) ) {
	function basel_html_block_shortcode($atts) {
		extract(shortcode_atts(array(
			'id' => 0
		), $atts));

		return basel_get_html_block($id);
	}
}