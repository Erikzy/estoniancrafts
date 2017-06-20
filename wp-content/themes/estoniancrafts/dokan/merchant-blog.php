<?php
/**
 * The Template for displaying all reviews.
 *
 * @package dokan
 * @package dokan - 2014 1.0
 */

$store_user = get_userdata( get_query_var( 'author' ) );
$store_info = dokan_get_store_info( $store_user->ID );
 $ec_page = apply_filters('ec_get_store_page', null);

get_header( 'shop' );

        query_posts(array(
            'author'        =>  $ec_page->user->data->ID,
            'orderby'       =>  'post_date',
            'order'         =>  'DESC',
            'post_status' => 'publish',
            'posts_per_page' => -1
        ));
        $i=1;
        while ( have_posts() ) : the_post();
        
           get_template_part( 'content', get_post_format() );
        
        endwhile;
        wp_reset_query();
  

 get_footer(); ?>