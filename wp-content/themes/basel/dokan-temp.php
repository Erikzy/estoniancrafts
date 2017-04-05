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