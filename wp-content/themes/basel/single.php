<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that other
 * 'pages' on your WordPress site will use a different template.
 *
 */

get_header(); ?>

<?php 
	
	// Get content width and sidebar position
	$content_class = basel_get_content_class();

?>


<div class="site-content <?php echo esc_attr( $content_class ); ?>" role="main">

		<?php /* The loop */ ?>
		<?php while ( have_posts() ) : the_post(); ?>
				 
				<?php get_template_part( 'content', get_post_format() ); ?>

				<?php if ( basel_get_opt( 'blog_share' ) ): ?>
					<div class="single-post-social-p">
						<div class=" center-block">
						<?php if( function_exists( 'basel_shortcode_social' ) ) echo basel_shortcode_social(array('type' => 'share', 'tooltip' => 'no', 'style' => 'colored')) ?>
					</div>
					<script type="text/javascript">
   			jQuery(".social-nav > li > a ").click(function(e){
     				e.preventDefault();
  				})
			jQuery(document).ready(function(){
				jQuery(".social-nav > li > a ").each(function(){
					jQuery(this).attr("href",  "");
					jQuery(this).attr("target", "");
						
				});
				
			});	
			</script>
					
					</div>
				<?php endif;
				
				$userid = get_current_user_id();
				$author = get_the_author_meta('ID');
				if($author == $userid){
					$id = get_the_ID();
				  //	echo  '<a href="/my-account/blog/edit?id='.$id.'"><span style="margin-bottom:10px;" class="edit-button-custom" >EDIT POST</span></a>';
				}
				
				
				 ?>
			
				<?php if ( basel_get_opt( 'blog_navigation' ) ): ?>
					<div class="single-post-navigation">
						 <?php previous_post_link('<div class="prev-link">%link</div>', esc_html__('Previous Post', 'basel')); ?> 
						 <?php next_post_link('<div class="next-link">%link</div>', esc_html__('Next Post', 'basel')); ?> 
					</div>
				<?php endif ?>

				<?php 

					if ( basel_get_opt( 'blog_related_posts' ) ) {
					    $args = basel_get_related_posts_args( $post->ID );

					    $query = new WP_Query( $args );

						 if( function_exists( 'basel_generate_posts_slider' ) ) echo basel_generate_posts_slider(array(
							'title' => esc_html__('Related Posts', 'basel'),
							'slides_per_view' => 2
						), $query); 
					}

				?>

				<?php comments_template(); ?>

		<?php endwhile; ?>

</div><!-- .site-content -->


<?php get_sidebar(); ?>

<?php get_footer(); ?>