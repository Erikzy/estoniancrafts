<?php

function lb_register_scripts(){
	global $wp_scripts;

	if (!is_admin()) {

		wp_enqueue_style( 'lb-dokan', get_template_directory_uri() . '/dokan-temp.css', false,'1.0','all');
		wp_enqueue_script( 'lb-dokan', get_template_directory_uri() . '/dokan-temp.js', false,'1.0','all');

	}


}

add_action('wp_enqueue_scripts', 'lb_register_scripts', 15);


function lb_save_additional_account_details( $user_ID ){

	$user_avatar = get_user_meta( $user_ID, 'dokan_profile_settings', true );
    $user_avatar['gravatar'] = $_POST['dokan_gravatar'];
    update_user_meta( $user_ID, 'dokan_profile_settings', $user_avatar );

    $account_mobile = ! empty( $_POST['account_mobile'] ) ? wc_clean( $_POST['account_mobile'] ) : '';
    update_user_meta( $user_ID, 'mobile', $account_mobile );

    $account_skype = ! empty( $_POST['account_skype'] ) ? wc_clean( $_POST['account_skype'] ) : '';
    update_user_meta( $user_ID, 'skype', $account_skype );

    $account_sex = ! empty( $_POST['account_sex'] ) ? wc_clean( $_POST['account_sex'] ) : '';
    update_user_meta( $user_ID, 'sex', $account_sex );

    $account_dob = ! empty( $_POST['account_dob'] ) ? wc_clean( $_POST['account_dob'] ) : '';
    update_user_meta( $user_ID, 'dob', $account_dob );

    $account_workyears = ! empty( $_POST['account_workyears'] ) ? wc_clean( $_POST['account_workyears'] ) : '';
    update_user_meta( $user_ID, 'workyears', $account_workyears );

    $account_video = ! empty( $_POST['account_video'] ) ? wc_clean( $_POST['account_video'] ) : '';
    update_user_meta( $user_ID, 'video', $account_video );

    $account_description = ! empty( $_POST['account_description'] ) ? wc_clean( $_POST['account_description'] ) : '';
    update_user_meta( $user_ID, 'description', $account_description );

    $account_education = ! empty( $_POST['account_education'] ) ? wc_clean( $_POST['account_education'] ) : '';
    update_user_meta( $user_ID, 'education', $account_education );


    // Location / address
    $account_location_country = ! empty( $_POST['account_location_country'] ) ? wc_clean( $_POST['account_location_country'] ) : '';
    update_user_meta( $user_ID, 'location_country', $account_location_country );

    $account_location_state = ! empty( $_POST['account_location_state'] ) ? wc_clean( $_POST['account_location_state'] ) : '';
    update_user_meta( $user_ID, 'location_state', $account_location_state );

    $account_location_city = ! empty( $_POST['account_location_city'] ) ? wc_clean( $_POST['account_location_city'] ) : '';
    update_user_meta( $user_ID, 'location_city', $account_location_city );

    $account_location_address = ! empty( $_POST['account_location_address'] ) ? wc_clean( $_POST['account_location_address'] ) : '';
    update_user_meta( $user_ID, 'location_address', $account_location_address );

}

add_action( 'woocommerce_save_account_details', 'lb_save_additional_account_details' );

function lb_display_country_select($selected = false, $field_name = 'account_location_country'){

    $countries_obj   = new WC_Countries();
    $countries   = $countries_obj->__get('countries');

    ?>

    <select class="input-text" name="<?= $field_name ?>">

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
    return $vars;
}

add_rewrite_tag( '%user%', '([^&]+)' );
add_rewrite_rule(
    '^user/([^/]*)/?',
    'index.php?user=$matches[1]',
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
}