<?php

get_header(); ?>


<div class="site-content" role="main">

	<?php /* The loop */ ?>
	<?php while ( have_posts() ) : the_post(); ?>

		<?php 
		global $post;

		$current_user = wp_get_current_user();
        $shared_emails = get_post_meta($post->ID, '_shared_emails', true);

        if ( is_user_logged_in() && ( $current_user->ID == $post->post_author || in_array($current_user->user_email, $shared_emails) ) ){
        ?>

			<?php get_template_part( 'content', get_post_format() ); ?>

			<?php comments_template(); ?>

		<?php }else{

			get_template_part( 'content', 'not_auth' );

		} ?>

	<?php endwhile; ?>

</div><!-- .site-content -->


<?php get_footer(); ?>