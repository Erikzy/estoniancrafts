<?php
/**
 * The Template for displaying all single posts.
 *
 * @package dokan
 * @package dokan - 2014 1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$company_types = array(
			'1'=>  __( 'FIE', 'ktt'),
		 	'2'=>  __( 'OÜ', 'ktt'),
			'3'=>  __( 'AS', 'ktt')
);	



$ec_page = apply_filters('ec_get_store_page', null);
$store_user   = $ec_page->user;
$store_info   = $ec_page->store_info;
$map_location = isset( $store_info['location'] ) ? esc_attr( $store_info['location'] ) : '';
$company_nr  = isset( $ec_page->ktt_extended_settings['company_nr'] ) ? esc_attr( $ec_page->ktt_extended_settings['company_nr'] ) : '';
$company_name  = isset( $ec_page->ktt_extended_settings['company_name'] ) ? esc_attr( $ec_page->ktt_extended_settings['company_name'] ) : '';
$company_type  = isset( $ec_page->ktt_extended_settings['company_type'] ) ? esc_attr( $ec_page->ktt_extended_settings['company_type'] ) : '';
$company_description  = isset( $ec_page->ktt_extended_settings['description'] ) ? esc_attr( $ec_page->ktt_extended_settings['description'] ) : '';

if($company_type != '' && $company_type != 'none'){
  $company_name .= " ".$company_types[$company_type];
}




get_header( 'shop' );
?>
    <?php do_action( 'woocommerce_before_main_content' ); ?>

    <?php if ( dokan_get_option( 'enable_theme_store_sidebar', 'dokan_general', 'off' ) == 'off' ) { ?>
        <div id="dokan-secondary" class="dokan-clearfix dokan-w3 dokan-store-sidebar" role="complementary">
            <div class="dokan-widget-area widget-collapse">

				<?php // Store logo ?>
				<?php if($ec_page->logo_url): ?>
					<div class="profile-image">
						<img src="<?= $ec_page->logo_url ?>" title="<?= $ec_page->title ?>" />
					</div>
				<?php endif; ?>

				<?php // Store info widget ?>
				<aside class="widget ec-store-info">

					<?php // Shop name ?>
					<h5 class="widget-title"><?= $ec_page->title ?></h5>
					<div class="clear"></div>

					<?php // User meta ?>
					<ul class="user-fields">
						<?php if(strlen($company_name) > 0): ?>
							<li><?php echo $company_name ?></li>
						<?php endif; ?>
						<?php if(strlen($company_nr) > 0): ?>
							<li><?php echo $company_nr ?></li>
						<?php endif; ?>
						
					
						<!--<li class="dokan-store-rating">
							<i class="fa fa-star"></i>
							<?php dokan_get_readable_seller_rating( $ec_page->id ); ?>
						</li>
						<li>
							<?php do_action( 'lb_store_after_rating', $ec_page->id ); ?>
						</li>-->
						<?php if($ec_page->phone): ?>
							<li><?php echo $ec_page->phone ?></li>
						<?php endif; ?>
						<?php if($ec_page->email): ?>
							<li><?php echo $ec_page->email ?></li>
						<?php endif; ?>
                        <?php // Website link ?>
                        <?php if(!empty($ec_page->website)): ?>
                            <li><a href="<?= $ec_page->website ?>" target="_blank"><?= $ec_page->website ?></a></li>
                        <?php endif; ?>
					</ul>

                    <div class="co-workers expanded button-group ">
                        <?php foreach($ec_page->related_people as $person): ?>
                            <?php //echo '<pre>';
                            //print_r($ec_page); ?>
                            <div class="btn-add">
                                <span class="basel-tooltip-label" ><div class="dokan-store-caption"><a class="button  btn-add custom-dokan-left "  href="<?= !empty($person->profile_url) ? $person->profile_url : '#' ?>">
                                    <?php /* if(!empty($person->avatar_url)): ?>
                                        <img class="co-worker-img" src="<?= $person->avatar_url ?>">
                                    <?php endif; */ ?>
                                    <!--<h4><?= $person->name ?></h4>-->

                                    <?= __('Vaata profiili', 'ktt') ?>
                                    <?php /* <em>Moekunstnik</em> */ ?>
                                </a></div>
                            </span>
                            </div>
                        <?php endforeach; ?>
                    </div>



					<?php // Contact us button ?>
					<?php if(bp_loggedin_user_domain()): ?>
					<div class="expanded button-group">
					
					
					
					 <?php
					 /* 	if(bp_loggedin_user_domain()){
					     $compose_url  = bp_loggedin_user_domain() . bp_get_messages_slug() . '/compose/?';
			  		     $compose_url .= 'r=' . bp_core_get_username( $store_user->ID );
					     echo '<a class="button" href="'.wp_nonce_url($compose_url).'">'.__('Send message', 'ktt').'</a>';
						}else{
						 echo __('Log in in order to send a message.','ktt');			
						}*/
					 ?>
					
					
					</div>
					<?php endif; ?>

                    <?php // Share facebook ?>
                    <div class="facebook-share-profile">
                        <h5 class="widget-title nullify-padding"><?= __('Jaga profiili', 'ktt') ?></h5>
                        <?php do_action( 'dolmit_share_profile_on_facebook'); ?>

                        <?php // Social media ?>
                        <div style="width:80%;float:left">
                            <ul class="social-nav">
                                <?php if(($link = isset($ec_page->sm_links['facebook']) ? $ec_page->sm_links['facebook'] : null)): ?>
                                    <li class="facebook"><a href="<?php echo $link->url ?>" target="_blank"><i class="fa fa-facebook fa-lg"></i></a></li>
                                <?php endif; ?>
                                <?php if(($link = isset($ec_page->sm_links['googleplus']) ? $ec_page->sm_links['googleplus'] : null)): ?>
                                    <li class="twitter"><a href="<?php echo $link->url ?>" target="_blank"><i class="fa fa-twitter fa-lg"></i></a></li>
                                <?php endif; ?>
                                <?php if(($link = isset($ec_page->sm_links['twitter']) ? $ec_page->sm_links['twitter'] : null)): ?>
                                    <li class="instagram"><a href="<?php echo $link->url ?>" target="_blank"><i class="fa fa-instagram fa-lg"></i></a></li>
                                <?php endif; ?>
                                <?php if(($link = isset($ec_page->sm_links['linkedin']) ? $ec_page->sm_links['linkedin'] : null)): ?>
                                    <li class="linkedin"><a href="<?php echo $link->url ?>" target="_blank"><i class="fa fa-linkedin fa-lg"></i></a></li>
                                <?php endif; ?>
                                <?php if(($link = isset($ec_page->sm_links['youtube']) ? $ec_page->sm_links['youtube'] : null)): ?>
                                    <li class="youtube"><a href="<?php echo $link->url ?>" target="_blank"><i class="fa fa-youtube fa-lg"></i></a></li>
                                <?php endif; ?>
                                <?php if(($link = isset($ec_page->sm_links['instagram']) ? $ec_page->sm_links['instagram'] : null)): ?>
                                    <li class="instagram"><a href="<?php echo $link->url ?>" target="_blank"><i class="fa fa-instagram fa-lg"></i></a></li>
                                <?php endif; ?>
                                <?php if(($link = isset($ec_page->sm_links['flickr']) ? $ec_page->sm_links['flickr'] : null)): ?>
                                    <li class="flickr"><a href="<?php echo $link->url ?>" target="_blank"><i class="fa fa-flickr fa-lg"></i></a></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>

					<div>
                        <h5 class="widget-title"><?= __('Blog', 'ktt') ?></h5>
                        <div class="clear"></div>
						<ul class="user-fields">
							<li>
								<a href="<?= ec_dokan_get_store_url($store_user->ID) ?>blog"><?= __('Blog', 'ktt') ?></a>
							</li>
						</ul>
					</div>
				</aside>

				<?php do_action( 'dokan_sidebar_store_before', $store_user, $store_info ); ?>
                <?php
                if ( ! dynamic_sidebar( 'sidebar-store' ) ) {

                    $args = array(
                        'before_widget' => '<aside class="widget">',
                        'after_widget'  => '</aside>',
                        'before_title'  => '<h5 class="widget-title">',
                        'after_title'   => '</h5>',
                    );

                    if ( class_exists( 'Dokan_Store_Location' ) ) {
                        the_widget( 'Dokan_Store_Category_Menu', array( 'title' => __( 'Store Category', 'dokan' ) ), $args );

                        if ( dokan_get_option( 'store_map', 'dokan_general', 'on' ) == 'on' ) {
                            the_widget( 'Dokan_Store_Location', array( 'title' => __( 'Store Location', 'dokan' ) ), $args );
                        }

                        if ( dokan_get_option( 'contact_seller', 'dokan_general', 'on' ) == 'on' ) {
                            the_widget( 'Dokan_Store_Contact_Form', array( 'title' => __( 'Contact Seller', 'dokan' ) ), $args );
                        }
                    }

                }
                ?>

                <?php do_action( 'dokan_sidebar_store_after', $store_user, $store_info ); ?>
            </div>
        </div><!-- #secondary .widget-area -->
    <?php
    } else {
        get_sidebar( 'store' );
    }
    ?>

    <div id="dokan-primary" class="dokan-single-store dokan-w8">

        <?php woocommerce_breadcrumb(); ?>

        <div id="dokan-content" class="store-page-wrap woocommerce" role="main">
        	<?php if ($ec_page->custom_content !== null) : ?>
        		<?= $ec_page->custom_content ?>
        	<?php else: ?>
				<?php // Banner ?>
				
				<div class="user-hero profile-info-summery-wrapper dokan-clearfix">
		
                       <?php echo do_shortcode( '[display_user_banner user_id="'.$store_user->ID.'" banner_name="company_banner" width="800" height="300"]' ); 

                       ?>
                    <span class="img-white-layer"></span>
                    <span class="store-description"><?= $company_description ?> </span>
                    <div class="store-user-image">
                    <?php foreach($ec_page->related_people as $person): ?>

                        <?php echo get_avatar($store_user->ID, 150, '', '', array('class' => 'co-worker-img')); ?>
                        <!--<?php if(!empty($person->avatar_url)): ?>
                            <img class="co-worker-img" src="<?= $person->avatar_url ?>">
                        <?php endif; ?>-->
                    <?php endforeach; ?>
                    </div>
				</div>



	            <?php // dokan_get_template_part( 'store-header' ); ?>

	            <?php do_action( 'dokan_store_profile_frame_after', $store_user, $store_info ); ?>

                <?php

                $video = get_user_meta($store_user->ID, 'ktt_extended_profile', true)['video'];

                preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $video, $videoId);

                $article_meta = get_user_meta($store_user->ID, 'ktt_extended_profile', true);

                if (isset($article_meta['articles_links'])) {
                    $article_links_orig = $article_meta['articles_links'];
                } else {
                    $article_links_orig = false;
                }

                if($article_links_orig) {
                    $article_links = explode(" ", $article_links_orig);
                } else {
                    $article_links = [];
                }
                ?>

	            <?php if ( have_posts() ) { ?>

	                <div class="seller-items">
                        <?php if (isset($videoId[0]) && $videoId[0]) { ?>
                            <div class="video-container video-container-margins">
                                <iframe width="560" height="315"
                                        src="<?php echo sprintf("https://www.youtube.com/embed/%s", $videoId[0]); ?>"
                                        frameborder="0" allowfullscreen></iframe>
                            </div>
                        <?php } ?>

                        <?php if (!empty($article_links) && !empty($article_links) && count($article_links) > 0) { ?>
                            <h5 class="widget-title nullify-padding article-head-title"><?= __("Artiklite lingid", "ktt") ?></h5>
                        <?php } ?>

                        <?php foreach ($article_links as $link) { ?>
                            <?php
                            $file_headers = @get_headers($link);
                            if(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found') {
                                continue;
                            }

                            $data = file_get_contents($link);
                            $title = preg_match('/<title[^>]*>(.*?)<\/title>/ims', $data, $matches) ? $matches[1] : null;

                            $article_meta_tags = get_meta_tags($link);

                            ?>
                            <a href="<?= $link ?>"><h3 class="article-linking-title product-title"><?php echo ($title ? $title : ($article_meta_tags['title'] ?: __('Missing title', 'ktt'))) ?></h3></a>
                            <p class="article-linking-subtitle">(<?= $link ?>)</p>
                            <p><?= $article_meta_tags['description'] ?: "" ?></p>
                        <?php } ?>

                        <h5 class="widget-title nullify-padding"><?= __("Products", "ktt") ?></h5>
	                    <?php woocommerce_product_loop_start(); ?>

	                        <?php while ( have_posts() ) : the_post(); ?>
	                            <?php wc_get_template_part( 'content', 'product' ); ?>

	                        <?php endwhile; // end of the loop. ?>

	                    <?php woocommerce_product_loop_end(); ?>

	                </div>

	                <?php dokan_content_nav( 'nav-below' ); ?>

	            <?php } else { ?>

	                <p class="dokan-info"><?php _e( 'No products were found of this seller!', 'dokan' ); ?></p>

	            <?php } ?>
	        <?php endif; ?>
        </div>

    </div><!-- .dokan-single-store -->

    <?php do_action( 'woocommerce_after_main_content' ); ?>

<?php get_footer( 'shop' ); ?>