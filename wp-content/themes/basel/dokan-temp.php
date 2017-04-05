<?php


function lb_display_country_select($selected = false, $field_name = 'account_location_country'){

    $countries_obj   = new WC_Countries();
    $countries   = $countries_obj->__get('countries');

    ?>

    <select class="input-text dokan-form-control" name="<?= $field_name ?>">

    	<option value=""> - <?php _e( 'select country', 'ktt' ); ?> - </option>

    	<?php foreach($countries as $code => $name){ ?>
    		<option value="<?= $code ?>" <?= (($code == $selected)? 'selected':'') ?>><?= $name ?></option> 
    	<?php } ?>

    </select>

    <?php
}



/**
 * Dokan seller menu customization
 */
add_filter( 'dokan_get_dashboard_nav', 'lb_seller_nav' );

function lb_seller_nav( $urls ) {

    unset( $urls['coupons'] );
    unset( $urls['reviews'] );
    unset( $urls['withdraw'] );

    unset( $urls['settings']['sub']['shipping'] );
    unset( $urls['settings']['sub']['payment'] );
 
    return $urls;
}


// Remove registration setup wizard
remove_filters_for_anonymous_class( 'woocommerce_registration_redirect', 'Dokan_Seller_Setup_Wizard', 'filter_woocommerce_registration_redirect', 10 );

// Products sold count
add_action( 'woocommerce_single_product_summary', 'lb_product_sold_count', 11 );
function lb_product_sold_count() {
    global $product;
    $units_sold = get_post_meta( $product->id, 'total_sales', true );
    echo '<p>' . sprintf( __( 'Units Sold: %s', 'woocommerce' ), $units_sold ) . '</p>';
}

// Make sure every comment needs to be approved
add_filter( 'pre_comment_approved', 'lb_comment_approval' );
function lb_comment_approval( $approved ){
    return 0;
}

// Hidden products add to cart redirect to checkout
// Empty cart if its a special product
add_filter ('woocommerce_add_to_cart_redirect', 'lb_redirect_to_checkout');
function lb_redirect_to_checkout($url) {

    if ( ! isset( $_REQUEST['add-to-cart'] ) || ! is_numeric( $_REQUEST['add-to-cart'] ) ) {
        return $url;
    }

    $product_id = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_REQUEST['add-to-cart'] ) );
    
    // Only redirect the product IDs in the array to the checkout
    if ( in_array( $product_id, array( 135, 137 ) ) ) {
        $url = WC()->cart->get_checkout_url();
    }

    return $url;
}
add_filter( 'woocommerce_add_cart_item_data', 'lb_empty_cart', 10,  3);
function lb_empty_cart( $cart_item_data, $product_id, $variation_id ) {

    if ( in_array( $product_id, array( 135, 137 ) ) ) {
        WC()->cart->empty_cart(); 
    }

    return $cart_item_data;
}





/**
 * User profile pages
 */

// Create the query var so that WP catches your custom /user/username url
add_filter( 'query_vars', 'wpleet_rewrite_add_var' );
function wpleet_rewrite_add_var( $vars )
{
    $vars[] = 'user';
    $vars[] = 'lbpdf';
    return $vars;
}

add_rewrite_tag( '%user%', '([^&]+)' );
add_rewrite_tag( '%lbpdf%', '([^&]+)' );
add_rewrite_rule(
    '^user/([^/]*)/?',
    'index.php?user=$matches[1]',
    'top'
);
add_rewrite_rule(
    '^lbpdf/([^/]*)/?',
    'index.php?lbpdf=$matches[1]',
    'top'
);


add_action( 'template_redirect', 'wpleet_rewrite_catch' );
function wpleet_rewrite_catch()
{
    global $wp_query;

    if ( array_key_exists( 'user', $wp_query->query_vars ) ) {
        include (TEMPLATEPATH . '/user-profile.php');
        exit;
    }
    if ( array_key_exists( 'lbpdf', $wp_query->query_vars ) ) {
        include (ABSPATH . 'wp-content/plugins/lb-pdf/display.php');
        exit;
    }
}

