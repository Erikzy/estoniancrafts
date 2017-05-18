<?php
/**
 * The Template for displaying all single posts.
 *
 * @package dokan
 * @package dokan - 2014 1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$ec_page = apply_filters('ec_get_store_page', null);
$store_user   = $ec_page->user;
$store_info   = $ec_page->store_info;
$map_location = isset( $store_info['location'] ) ? esc_attr( $store_info['location'] ) : '';

get_header( 'shop' );
?>
    <?php do_action( 'woocommerce_before_main_content' ); ?>

    <?php if ( dokan_get_option( 'enable_theme_store_sidebar', 'dokan_general', 'off' ) == 'off' ) { ?>
        <div id="dokan-secondary" class="dokan-clearfix dokan-w3 dokan-store-sidebar" role="complementary" style="margin-right:3%;">
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
					<h1 class="widget-title"><?= $ec_page->title ?></h1>
					<div class="clear"></div>

					<?php // User meta ?>
					<ul class="user-fields">
						<li class="dokan-store-rating">
							<i class="fa fa-star"></i>
							<?php dokan_get_readable_seller_rating( $ec_page->id ); ?>
						</li>
						<li>
							<?php do_action( 'lb_store_after_rating', $ec_page->id ); ?>
						</li>
						<?php if($ec_page->phone): ?>
							<li><?php echo $ec_page->phone ?></li>
						<?php endif; ?>
						<?php if($ec_page->email): ?>
							<li><?php echo $ec_page->email ?></li>
						<?php endif; ?>
					</ul>

					<?php // Social media ?>
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

					<?php // Contact us button ?>
					<?php if($ec_page->contact_us_url): ?>
					<div class="expanded button-group">
						<a class="button" href="<?= $ec_page->contact_us_url ?>"><?= __('Võta ühendust', 'ktt') ?></a>
					</div>
					<?php endif; ?>

					<?php // Website link ?>
					<?php if(!empty($ec_page->website)): ?>
					<a href="<?= $ec_page->website ?>" target="_blank" class="ec-store-website-link"><?= $ec_page->website ?></a>
					<?php endif; ?>

				</aside>

				<?php // Store related people widget ?>
				<?php if(!empty($ec_page->related_people)): ?>
				<aside class="widget ec-store-related-people">

					<?php // Shop name ?>
					<h5 class="widget-title"><?= __('Related people', 'ktt') ?></h5>
					<div class="clear"></div>

					<div class="co-workers">
						<ul>
							<?php foreach($ec_page->related_people as $person): ?>
								<li class="co-worker">
									<a href="<?= !empty($person->profile_url) ? $person->profile_url : '#' ?>">
										<?php if(!empty($person->avatar_url)): ?>
											<img class="co-worker-img" src="<?= $person->avatar_url ?>">
										<?php endif; ?>
										<h4><?= $person->name ?></h4>
										<?php /* <em>Moekunstnik</em> */ ?>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>

				</aside>
				<?php endif; ?>

				<?php do_action( 'dokan_sidebar_store_before', $store_user, $store_info ); ?>
                <?php
                if ( ! dynamic_sidebar( 'sidebar-store' ) ) {

                    $args = array(
                        'before_widget' => '<aside class="widget">',
                        'after_widget'  => '</aside>',
                        'before_title'  => '<h3 class="widget-title">',
                        'after_title'   => '</h3>',
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
        <div id="dokan-content" class="store-page-wrap woocommerce" role="main">

			<?php // Banner ?>
			<?php if(($banner = $ec_page->banner) && $banner->image_url): ?>
			<div class="user-hero profile-info-summery-wrapper dokan-clearfix">
				<img src="<?= $banner->image_url ?>" title="<?= $banner->title ?>" />
			</div>
			<?php endif; ?>

            <?php // dokan_get_template_part( 'store-header' ); ?>

            <?php do_action( 'dokan_store_profile_frame_after', $store_user, $store_info ); ?>

            <?php if ( have_posts() ) { ?>

                <div class="seller-items">

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
        </div>

    </div><!-- .dokan-single-store -->

    <?php do_action( 'woocommerce_after_main_content' ); ?>

<?php get_footer( 'shop' ); ?>