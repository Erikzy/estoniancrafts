<?php
/**
 * Share template
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 2.0.13
 */

if ( ! defined( 'YITH_WCWL' ) ) {
    exit;
} // Exit if accessed directly
?>

<div class="col-sm-12">
    <h4 class="yith-wcwl-share-title wishlist-h"><?php echo $share_title ?></h4>
    <div class="center-block social-nav-container">
          <ul class="social-nav">
            <?php if( $share_facebook_enabled ): ?>
     
                      <li class="facebook lf-m"> <a target="_blank" class="facebook" href="https://www.facebook.com/sharer.php?s=100&amp;p%5Btitle%5D=<?php echo $share_link_title ?>&amp;p%5Burl%5D=<?php echo urlencode( $share_link_url ) ?>" title="<?php _e( 'Facebook', 'yith-woocommerce-wishlist' ) ?>"><i class="fa fa-facebook"></i></a></li>
            <?php endif; ?>

            <?php if( $share_twitter_enabled ): ?>

                    <li class="twitter"><a target="_blank" class="twitter" href="https://twitter.com/share?url=<?php echo $share_link_url ?>&amp;text=<?php echo $share_twitter_summary ?>" title="<?php _e( 'Twitter', 'yith-woocommerce-wishlist' ) ?>"><i class="fa fa-twitter"></i></a></li>
            <?php endif; ?>

            <?php if( $share_pinterest_enabled ): ?>
     

                          <li class="pinterest"> <a target="_blank" class="pinterest" href="http://pinterest.com/pin/create/button/?url=<?php echo $share_link_url ?>&amp;description=<?php echo $share_summary ?>&amp;media=<?php echo $share_image_url ?>" title="<?php _e( 'Pinterest', 'yith-woocommerce-wishlist' ) ?>" onclick="window.open(this.href); return false;"><i class="fa fa-pinterest"></i></a></li>
            <?php endif; ?>

            <?php if( $share_googleplus_enabled ): ?>
    

                  <li class="google"> <a target="_blank" class="googleplus" href="https://plus.google.com/share?url=<?php echo $share_link_url ?>&amp;title=<?php echo $share_link_title ?>" title="<?php _e( 'Google+', 'yith-woocommerce-wishlist' ) ?>" onclick='javascript:window.open(this.href, "", "menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600");return false;'><i class="fa fa-google-plus"></i></a></li>
            <?php endif; ?>

            <?php if( $share_email_enabled ): ?>

              <li class="email"> <a class="email" href="mailto:?subject=<?php echo urlencode( apply_filters( 'yith_wcwl_email_share_subject', __( 'I wanted you to see this site', 'yith-woocommerce-wishlist' ) ) )?>&amp;body=<?php echo apply_filters( 'yith_wcwl_email_share_body', $share_link_url ) ?>&amp;title=<?php echo $share_link_title ?>" title="<?php _e( 'Email', 'yith-woocommerce-wishlist' ) ?>"><i class="fa fa-envelope"></i></a></li>
            <?php endif; ?>
        </ul>
    </div>

</div>