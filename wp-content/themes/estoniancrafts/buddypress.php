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

    <div class="site-content" role="main">
	<div class="sidebar_widget_left_new">
        <?php /* The loop */ 
       
      echo  	do_shortcode('[vc_widget_sidebar sidebar_id="sidebar-my-account" el_class="sidebar_widget_left_new"]');
        ?>
     </div>   
   	<div class="user_content_widget_right">
        <?php while ( have_posts() ) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                <div class="entry-content">
                    <?php the_content(); ?>
                    <?php wp_link_pages(); ?>
                </div>

                <?php basel_entry_meta(); ?>

            </article><!-- #post -->

            <?php
            // If comments are open or we have at least one comment, load up the comment template.
            if ( basel_get_opt('page_comments') && (comments_open() || get_comments_number()) ) :
                comments_template();
            endif;
            ?>

        <?php endwhile; ?>
	</div>
    </div><!-- .site-content -->


<?php  ?>

<?php get_footer(); ?>