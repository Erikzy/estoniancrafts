<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}


add_action( 'vc_before_init', 'zmbPostsList' );
function zmbPostsList() {
	global $zan_vc_anim_effects_in;
	vc_map(
		array(
			'name'     => esc_html__( 'Zan Posts list', 'zanmb' ),
			'base'     => 'zmb_posts_list', // shortcode
			'class'    => '',
			'category' => esc_html__( 'Zan', 'zanmb' ),
			'params'   => array(
				array(
					'type'       => 'dropdown',
					'class'      => '',
					'heading'    => esc_html__( 'Style', 'zanmb' ),
					'param_name' => 'style',
					'value'      => array(
						esc_html__( 'Style 1 (show post thumbnail)', 'zanmb' ) => 'style_1',
						esc_html__( 'Style 2 (show post date)', 'zanmb' )      => 'style_2'
					),
					'std'        => 'style_1',
				),
				array(
					'type'       => 'zmb_select_cat_field',
					'holder'     => 'div',
					'class'      => '',
					'heading'    => esc_html__( 'Posts Category', 'zanmb' ),
					'param_name' => 'cat_slug',
					'std'        => '',
				),
				array(
					'type'        => 'textfield',
					'holder'      => 'div',
					'class'       => '',
					'heading'     => esc_html__( 'Thumbnail Size', 'zanmb' ),
					'param_name'  => 'img_size',
					'std'         => '70x70',
					'description' => wp_kses( __( 'Format {width}x{height}. Default <strong>70x70</strong>.', 'zanmb' ), array( 'strong' => array(), 'b' => array() ) ),
					'dependency'  => array(
						'element' => 'style',
						'value'   => 'style_1',
					),
				),
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
					'type'        => 'textfield',
					'holder'      => 'div',
					'class'       => '',
					'heading'     => esc_html__( 'Number Of Posts', 'zanmb' ),
					'param_name'  => 'number_of_items',
					'std'         => 4,
					'description' => esc_html__( 'Maximum number of posts will load', 'zanmb' ),
				),
				array(
					'type'        => 'textfield',
					'holder'      => 'div',
					'class'       => '',
					'heading'     => esc_html__( 'Excerpt Max Words', 'zanmb' ),
					'param_name'  => 'excerpt_max_words',
					'std'         => 9,
					'description' => esc_html__( 'Maximum number of post excerpt words', 'zanmb' ),
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


function zmb_posts_list( $atts ) {

	$atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'zmb_posts_list', $atts ) : $atts;

	extract(
		shortcode_atts(
			array(
				'style'             => 'style_1',
				'cat_slug'          => '',
				'img_size'          => '70x70',
				'title_color'       => '#000',
				'text_color'        => '#7c7c7c',
				'number_of_items'   => 4,
				'excerpt_max_words' => 9,
				'css_animation'     => '',
				'animation_delay'   => '0.4',  // In second
				'css'               => '',
			), $atts
		)
	);

	$css_class = 'zmb-posts-list-wrap zan-posts-list-wrap wow ' . $css_animation . ' post-list-' . $style;
	if ( function_exists( 'vc_shortcode_custom_css_class' ) ):
		$css_class .= ' ' . apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), '', $atts );
	endif;

	if ( !is_numeric( $animation_delay ) ) {
		$animation_delay = '0';
	}
	$animation_delay = $animation_delay . 's';

	$args = array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'ignore_sticky_posts' => 1,
		'showposts'           => intval( $number_of_items ),
	);

	$cat_slug = trim( $cat_slug );
	if ( $cat_slug != '' ):

		$args['tax_query'] = array(
			array(
				'taxonomy' => 'category',
				'field'    => 'slug',
				'terms'    => $cat_slug
			)
		);

	endif;

	$html = '';

	ob_start();

	$posts = new WP_Query( $args );
	$total_posts = $posts->post_count;

	$img_size_x = 70;
	$img_size_y = 70;
	if ( trim( $img_size ) != '' ) {
		$img_size = explode( 'x', $img_size );
	}
	$img_size_x = isset( $img_size[0] ) ? max( 1, intval( $img_size[0] ) ) : $img_size_x;
	$img_size_y = isset( $img_size[1] ) ? max( 1, intval( $img_size[1] ) ) : $img_size_y;

	?>

	<?php if ( $posts->have_posts() ): ?>
		<div class="<?php echo esc_attr( $css_class ); ?>" data-wow-delay="<?php echo esc_attr( $animation_delay ); ?>">
			<div class="zmb-posts-list zan-posts-list">
				<?php while ( $posts->have_posts() ) :
					$posts->the_post(); ?>
					<?php

					$left_part_html = '';

					// If show post thumbnail
					if ( $style == 'style_1' ) {
						$img = zmb_resize_image( get_post_thumbnail_id(), null, $img_size_x, $img_size_y, true, true, false );
						$left_part_html = '<a href="' . esc_url( get_permalink() ) . '" class="hover-plus-effect">
											<figure>
												<img width="' . esc_attr( $img['width'] ) . '"
												     height="' . esc_attr( $img['height'] ) . '"
												     src="' . esc_url( $img['url'] ) . '" alt="' . esc_attr( get_the_title() ) . '"/>
											</figure>
										</a>';
					}
					// If show post date
					if ( $style == 'style_2' ) {
						$post_day = get_the_date( 'd' );
						$post_month = get_the_date( 'm' );
						$left_part_html .= '<div class="post-date" style="color: ' . esc_attr( $title_color ) . '; border-color: ' . esc_attr( $title_color ) . ';"><span class="post-day">' . esc_html( $post_day ) . '</span><span class="post-month">' . esc_html( $post_month ) . '</span></div>';
					}

					?>
					<div class="item-post">
						<div class="left-part">
							<?php echo $left_part_html; ?>
						</div>
						<div class="right-part">
							<div class="post-title-wrap">
								<h5 style="color:<?php echo esc_attr( $title_color ); ?>;"><a
										href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
							</div>
							<div class="post-excerpt-wrap" style="color:<?php echo esc_attr( $text_color ); ?>;">
								<?php echo zmb_trim_excerpt( '', $excerpt_max_words ); ?>
							</div>
						</div>
					</div>
				<?php endwhile; ?>
			</div><!-- /.lk-slide-post -->
		</div><!-- /.<?php echo esc_attr( $css_class ); ?> -->
	<?php endif; // End if ( $posts->have_posts() ) ?>

	<?php

	wp_reset_postdata();

	$html .= ob_get_clean();

	return $html;

}

add_shortcode( 'zmb_posts_list', 'zmb_posts_list' );
