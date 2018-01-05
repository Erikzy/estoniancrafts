<?php

$currentDirname = dirname(__FILE__);

// Load filters
include_once($currentDirname.'/filters.php');

// Load actions
include_once($currentDirname.'/actions.php');

// Load shortcodes
include_once($currentDirname.'/shortcodes.php');

// Load widgets
include_once($currentDirname.'/widgets.php');

// Load facebook
include_once($currentDirname.'/facebook/class-facebook-login.php');

// Load shops
include_once($currentDirname.'/shop/shop-functions.php');


// Load portfolio
include_once($currentDirname.'/portfolio/portfolio-functions.php');





//WC_Cache_Helper::prevent_caching();

/**
 * @return string
 */
function ec_get_sidebar_name()
{
	// Organisation page
	if(bp_is_current_component( 'groups' ) && bp_is_group_single()) {
		return 'sidebar-organisation';
	}

	// Mailbox
	if(ec_is_mailbox_page()) {
		return 'sidebar-my-account';
	}

	// Theme default
	return basel_get_sidebar_name();
}



 function redirect_to_user_appropriate_home() {
		$user = wp_get_current_user();
		if(in_array('seller', $user->roles)){
  			return get_site_url(null, 'my-account/dashboard/');
  		}else{
  			return get_site_url(null, 'my-account/edit-account/');
  		}
	}

	add_filter('login_redirect','redirect_to_user_appropriate_home');
	
/**
 * @return boolean
 */
function ec_is_personal_profile_page()
{
	$url_parts = wp_parse_url($_SERVER['REQUEST_URI']);
	if(is_array($url_parts) && isset($url_parts['path']) && !empty($url_parts['path']))
	{
		$url_parts['path'] = trim($url_parts['path'], '/');
		$url_path_parts = explode('/', $url_parts['path']);
		if($url_path_parts[0] == 'user') {
			return true;
		}
	}

	return false;
}

/**
 * @return boolean
 */
function ec_is_organisation_page()
{
	if(bp_is_current_component( 'groups' ) && bp_is_group_single()) {
		return true;
	}

	return false;
}

/**
 * @return boolean
 */
function ec_is_mailbox_page()
{
	$url_parts = wp_parse_url($_SERVER['REQUEST_URI']);
	if(is_array($url_parts) && isset($url_parts['path']) && !empty($url_parts['path']))
	{
		$url_parts['path'] = trim($url_parts['path'], '/');
		$url_path_parts = explode('/', $url_parts['path']);
		if(count($url_path_parts) >= 3 && $url_path_parts[0] == 'members' && $url_path_parts[2] == 'messages') {
			return true;
		}
	}

	return false;
}

function ec_debug()
{
	print '<pre>';
	foreach(func_get_args() as $arg)
	{
		print_r($arg);
		print "\n";
	}
	print '</pre>';
}

function ec_debug_to_console()
{
	if(WP_DEBUG === true) {
		foreach(func_get_args() as $arg)
		{
			print '<script>console.log('. json_encode( $arg ) .');</script>';
		}
	}
}

/**
 * Output the user id to the page of the current thread's last author.
 */
if (!function_exists('bp_message_thread_from_id')) {
    function bp_message_thread_from_id() {
        echo bp_get_message_thread_from_id();
    }
}
/**
 * Get the user id to the page of the current thread's last author.
 *
 * @return string
 */
if (!function_exists('bp_get_message_thread_from_id')) {
    function bp_get_message_thread_from_id() {
        global $messages_template;
        return $messages_template->thread->last_sender_id;
    }
}

function custom_tribe_events_event_schedule_details( $event = null, $before = '', $after = '' ) {
    if ( is_null( $event ) ) {
        global $post;
        $event = $post;
    }

    if ( is_numeric( $event ) ) {
        $event = get_post( $event );
    }

    $inner                    = '';
    $format                   = '';
    $date_without_year_format = 'd M';
    $date_with_year_format    = 'd M Y';
    $time_format              = get_option( 'time_format' );
    $datetime_separator       = tribe_get_option( 'dateTimeSeparator', ' @ ' );
    $time_range_separator     = tribe_get_option( 'timeRangeSeparator', ' - ' );

    $settings = array(
        'show_end_time' => true,
        'time'          => true,
    );

    $settings = wp_parse_args( apply_filters( 'tribe_events_event_schedule_details_formatting', $settings ), $settings );
    if ( ! $settings['time'] ) {
        $settings['show_end_time'] = false;
    }

    /**
     * @var $show_end_time
     * @var $time
     */
    extract( $settings );

    $format = $date_with_year_format;

    // if it starts and ends in the current year then there is no need to display the year
    if ( tribe_get_start_date( $event, false, 'Y' ) === date( 'Y' ) && tribe_get_end_date( $event, false, 'Y' ) === date( 'Y' ) ) {
        $format = $date_without_year_format;
    }

    if ( tribe_event_is_multiday( $event ) ) { // multi-date event
        $inner .= tribe_get_start_date( $event, true, 'd M' ) .' - ';
        $inner .= tribe_get_end_date( $event, true, 'd M' );
    } else {
        if ( tribe_get_start_date( $event, false, 'g:i A' ) === tribe_get_end_date( $event, false, 'g:i A' ) ) { // Same start/end time
            $inner .= tribe_get_start_date( $event, true, 'd M' );
        } else { // defined start/end time
            $inner .= tribe_get_start_date( $event, true, 'd M H:i' );
            if ($show_end_time) {
                $inner .= ' - '.tribe_get_end_date( $event, true, 'H:i' );
            }
        }
//        $inner .= str_replace(array('--', '++'), array('<small>', '</small>'), tribe_get_start_date( $event, true, 'd--M++' ));
    }

    return str_replace('.', '', $inner);
}

if (!function_exists('tribe_is_started_event')) {
    // Usage tribe_is_started_event( $event_id )
    function tribe_is_started_event( $event = null ){
        if ( ! tribe_is_event( $event ) ){
            return false;
        }
        $event = tribe_events_get_event( $event );
        // Grab the event End Date as UNIX time
        $start_date = tribe_get_start_date( $event, true, 'U' );
        return time() > $start_date;
    }
}

// override 'woocommerce_package_rates' filter
if (function_exists('dokan_multiply_flat_rate_price_by_seller')) {
    remove_filter('woocommerce_package_rates', 'dokan_multiply_flat_rate_price_by_seller', 1);
}

function ec_multiply_flat_rate_price_by_seller( $rates, $package ) {

    $flat_rate_array = preg_grep("/^flat_rate:*/", array_keys( $rates ) );
    if (count($flat_rate_array)) {
        $flat_rate       = $flat_rate_array[0];

        foreach ( $package['contents'] as $product ) {
            $sellers[] = get_post_field( 'post_author', $product['product_id'] );
        }

        $sellers = array_unique( $sellers );

        $selllers_count = count( $sellers );

        if ( isset( $rates[$flat_rate] ) && ! is_null( $rates[$flat_rate] ) ) {

            $rates[$flat_rate]->cost = $rates[$flat_rate]->cost * $selllers_count;

            // we assumed taxes key will always be 1, if different condition appears in future, we'll update the script
            if ( isset( $rates[$flat_rate]->taxes[1] ) ) {
                $rates[$flat_rate]->taxes[1] = $rates[$flat_rate]->taxes[1] * $selllers_count;
            }

        } elseif ( isset( $rates['international_delivery'] ) && ! is_null( $rates['international_delivery'] ) ) {

            $rates['international_delivery']->cost = $rates['international_delivery']->cost * $selllers_count;
            // we assumed taxes key will always be 1, if different condition appears in future, we'll update the script
            $rates['international_delivery']->taxes[1] = $rates['international_delivery']->taxes[1] * $selllers_count;

         }

    }

    return $rates;
}
add_filter( 'woocommerce_package_rates', 'ec_multiply_flat_rate_price_by_seller', 2,2);
add_image_size('user_banner_upload',800,300, false);
add_image_size('user_banner_upload_new',400,300, array('center','center'));

add_image_size('user_banner_upload_scaled',400,150, array('center','center'));

if (!function_exists('is_user_idcard')) {
    function is_user_idcard() {
        // Just to be sure if user is currently logged in
        if (!is_user_logged_in()) {
            return false;
        }

        global $wpdb;

        $current_user = wp_get_current_user();
        $user = $wpdb->get_row(
            $wpdb->prepare(
                "select * from $wpdb->prefix" . "idcard_users WHERE userid=%s", $current_user->ID
            )
        );

        return (bool) $user != NULL;
    }
  
}

if (!function_exists('is_user_facebook')) {
    function is_user_facebook() {
        // Just to be sure if user is currently logged in
        if (!is_user_logged_in()) {
            return false;
        }

		global $current_user; 
 
		get_currentuserinfo();
 
		if ( $current_user ) {
    		$permission = get_user_meta( $current_user->ID, 'ec_facebook_id' , true );
     
    		if ( ! empty( $permission ) {
        		return true;
    		}
		}
		return false;
    }
  
}
/*
* User visual composer carousel widget
*/

if(class_exists('WPBakeryShortCode')):

class EC_vcUserCarousel extends WPBakeryShortCode
{

    public function __construct()
    {
        add_action('init', array($this, 'vc_mapping'));
        add_shortcode('ec_user_carousel', array($this, 'vc_html'));
    }

    public function vc_mapping()
    {
        vc_map([
            'name' => __('EC Users', 'ktt'),
            'base' => 'ec_user_carousel',
            'category' => __('My Custom Elements', 'text-domain'),
            'params' => [
                [
                    'type' => 'checkbox',
                    'heading' => __('Is brand', 'ktt'),
                    'param_name' => 'is_brand',
                    'description' => __('Check to show users as brand', 'ktt'),
                ],
                [
                    'type' => 'autocomplete',
                    'param_name' => 'include',
                    'heading' => __( 'Users', 'ktt' ),
                    'description' => __( 'Add users by email', 'ktt' ),
                    'settings' => [
                        'multiple' => true,
                        'sortable' => true,
                        'groups' => true,
                        'min_length' => 1,
                    ]
                ]
            ]
        ]);
    }

    public function vc_html($atts)
    {
        extract(
            shortcode_atts(
                array(
                    'include' => '',
                    'is_brand' => false
                ),
                $atts
            )
        );

        $users = [];
        if(isset($atts['include'])) {

            // get users
            $include = explode(',', $atts['include']);
            $include = array_map(function ($value) {
                return (int)$value;
            }, $include);
            $tmpusers = get_users(['include' => $include]);
            // reorder
            $findUser = function (&$users, $id) {
                foreach ($users as $user) {
                    if ((int)$user->ID === $id) {
                        return $user;
                    }
                }
            };
            foreach ($include as $id) {
                $users[] = $findUser($tmpusers, (int)$id);
            }
        }

        // get is brand
        $isBrand = isset($atts['is_brand']) ? $atts['is_brand'] : false;

        ob_start(); 

        echo '<div class="top-users">';
        foreach ($users as $user):
            if ($user) { ?>
            <div class="user-item">
                <?= get_avatar($user->ID, 128) ?>
                <h3>
                    <?php if ($isBrand) : ?>
                        <?= esc_html(dokan_get_store_info($user->ID)['store_name']) ?>
                    <?php else: ?>
                        <?= $user->first_name ?> <?= $user->last_name ?>
                    <?php endif; ?>
                </h3>

            </div>
            <?php
        }
        endforeach;
        echo '</div>';

        return ob_get_clean();
    }

}

new EC_vcUserCarousel();

if ( 'vc_get_autocomplete_suggestion' === vc_request_param( 'action' ) || 'vc_edit_form' === vc_post_param( 'action' ) ) {
    function ec_user_carousel_include_callback($query, $tag = '', $param_name = '')
    {
        global $wpdb;
        $suggestions = $wpdb->get_results($wpdb->prepare("SELECT ID AS value, user_email AS label FROM `ktt_users` WHERE user_email LIKE %s", '%'.$query.'%'), ARRAY_A);
        return $suggestions;
    }
    add_filter('vc_autocomplete_ec_user_carousel_include_callback', 'ec_user_carousel_include_callback');

    function ec_include_field_render($params)
    {
        global $wpdb;
        $user = $wpdb->get_row($wpdb->prepare("SELECT ID AS value, user_email AS label FROM `ktt_users` WHERE ID = %d", (int)$params['value']), ARRAY_A);
        return $user;
    }
    add_filter( 'vc_autocomplete_ec_user_carousel_include_render', 'ec_include_field_render', 10, 1 ); // Render exact product. Must return an array (label,value)
}
 endif;   
function ec_dokan_get_store_url( $user_id )
{
    $userdata = get_userdata( $user_id );
    $user_nicename = ( !false == $userdata ) ? $userdata->user_nicename : '';

    return sprintf( '%s/%s/', home_url(), $user_nicename );
}

function ec_dokan_get_store_latests_posts( $user_id )
{

        $args = array(
          'author'        =>  $user_id, 
          'orderby'       =>  'post_date',
          'order'         =>  'DESC',
          'posts_per_page' => 3 // no limit
        );


        $user_posts = get_posts( $args );
        
    return  $user_posts;
}

function ec_user_profile_data(){
     global $wp_query;
     $username = get_user_by('id',$wp_query->query_vars["author"])->user_login;
     $user= get_user_by('login', $username);
     include_once(get_stylesheet_directory().'/Blocks/Objects/EC_Link.php');
     include_once(get_stylesheet_directory().'/Blocks/Objects/EC_Address.php');
     $ext_profile = get_user_meta( $user->data->ID, 'ktt_extended_profile', true );
     $fn = isset($user->first_name) ? $user->first_name : null;
     $ln = isset($user->last_name) ? $user->last_name : null;
     $name = $fn. ' '. $ln;
     $nick = isset($user->data->nickname) ? $user->data->nickname : null;
     $email = isset($user->data->user_email) ? $user->data->user_email : null;
     $gender = isset($ext_profile['gender']) ? $ext_profile['gender'] : null;
     $phone = isset($ext_profile['mobile']) ? $ext_profile['mobile'] : null;
     $skype = isset($ext_profile['skype']) ? $ext_profile['skype'] : null;


     $page_data = array(
            "name" => $name,
            "nickname" => $nick,
            "email" => $email,
            "gender" => $gender,
            "phone" => $phone,
            "skype" => $skype
        ); 


     return $page_data;
}

function ec_get_portfolio_url( $portfolio, $user )
{
    return home_url('/user/'.$user->data->user_login.'/portfolio/'.$portfolio->ID);
}
if( ! function_exists( 'basel_product_video_car_button' ) ) {
    function basel_product_video_car_button($postid) {
        $meta = get_post_meta($postid, '', true);
        //dokan data
        //$video_url = get_post_meta(get_the_ID(),  '_basel_product_video', true );
       // for ($a =0; $a < sizeof($meta); $a++)
       
        if(isset($meta["_product_videos"])):
        $meta = $meta["_product_videos"]; 
        $a = unserialize( $meta[0] );
        $params = explode("=",$a[0]);
		if(isset($params[1])){
        
        $code = $params[1];
       	?>
            
               <div class="product-video-button product-video-youtube owl-item"   >




           <!--  <div class="product-video-button owl-item product-video-youtube "   > -->
                <div style="display:table;">
                    <div style="display:table-cell; " >
                    <a href="<?php echo esc_url($a[0]); ?>" >

                        <!-- <img src="https://img.youtube.com/vi/<?php echo $code; ?>/hqdefault.jpg"> -->
                        <img src="<?php echo get_template_directory_uri() ?>/images/youtube.jpg" class="ybutton" id="youtube-button" >
                        
                        </a>
                    </div>
                </div>
    
            </div>
        <?php
        }
        endif;
        }
    }

function ec_save_account_details(){
 if ( 'POST' !== strtoupper( $_SERVER[ 'REQUEST_METHOD' ] ) ) {
		return;
	}

	if ( empty( $_POST[ 'action' ] ) || 'dokan_save_account_details' !== $_POST[ 'action' ] || empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'dokan_save_account_details' ) ) {
		return;
	}

	$errors       = new WP_Error();
	$user         = new stdClass();

	$user->ID     = (int) get_current_user_id();
	$current_user = get_user_by( 'id', $user->ID );

	if ( $user->ID <= 0 ) {
		return;
	}

	$account_first_name = ! empty( $_POST[ 'account_first_name' ] ) ? wc_clean( $_POST[ 'account_first_name' ] ) : '';
	$account_last_name  = ! empty( $_POST[ 'account_last_name' ] ) ? wc_clean( $_POST[ 'account_last_name' ] ) : '';
	$account_email      = ! empty( $_POST[ 'account_email' ] ) ? sanitize_email( $_POST[ 'account_email' ] ) : '';
	$pass_cur           = ! empty( $_POST[ 'password_current' ] ) ? $_POST[ 'password_current' ] : '';
	$pass1              = ! empty( $_POST[ 'password_1' ] ) ? $_POST[ 'password_1' ] : '';
	$pass2              = ! empty( $_POST[ 'password_2' ] ) ? $_POST[ 'password_2' ] : '';
	$save_pass          = true;

	$user->first_name   = $account_first_name;
	$user->last_name    = $account_last_name;

	// Prevent emails being displayed, or leave alone.
	$user->display_name = is_email( $current_user->display_name ) ? $user->first_name : $current_user->display_name;

	// Handle required fields
	$required_fields = apply_filters( 'woocommerce_save_account_details_required_fields', array(
		'account_first_name' => __( 'First Name', 'dokan' ),
		'account_last_name'  => __( 'Last Name', 'dokan' ),
		'account_email'      => __( 'Email address', 'dokan' ),
	) );

	foreach ( $required_fields as $field_key => $field_name ) {
		if ( empty( $_POST[ $field_key ] ) ) {
			wc_add_notice( '<strong>' . esc_html( $field_name ) . '</strong> ' . __( 'is a required field.', 'dokan' ), 'error' );
		}
	}

	if ( $account_email ) {
		if ( ! is_email( $account_email ) ) {
			wc_add_notice( __( 'Please provide a valid email address.', 'dokan' ), 'error' );
		} elseif ( email_exists( $account_email ) && $account_email !== $current_user->user_email ) {
			wc_add_notice( __( 'This email address is already registered.', 'dokan' ), 'error' );
		}
		$user->user_email = $account_email;
	}


	if(is_user_idcard() || is_user_facebook()){
		if ( ! empty( $pass1 ) && empty( $pass2 ) ) {
			wc_add_notice( __( 'Please re-enter your password.', 'dokan' ), 'error' );
			$save_pass = false;
		} elseif ( ( ! empty( $pass1 ) || ! empty( $pass2 ) ) && $pass1 !== $pass2 ) {
			wc_add_notice( __( 'New passwords do not match.', 'dokan' ), 'error' );
			$save_pass = false;
		}
		
		//wc_add_notice( __( 'unsetting id card user', 'dokan' ), 'error' );
		unset($pass_cur);
		$save_pass = true;
		
	} else {
		if ( ! empty( $pass1 ) && ! wp_check_password( $pass_cur, $current_user->user_pass, $current_user->ID ) ) {
			wc_add_notice( __( 'Your current password is incorrect.', 'dokan' ), 'error' );
			$save_pass = false;
		}
		
		if ( ! empty( $pass_cur ) && empty( $pass1 ) && empty( $pass2 ) ) {
			wc_add_notice( __( 'Please fill out all password fields.', 'dokan' ), 'error' );
			$save_pass = false;
		} elseif ( ! empty( $pass1 ) && empty( $pass_cur ) ) {
			wc_add_notice( __( 'Please enter your current password.', 'dokan' ), 'error' );
			$save_pass = false;
		} elseif ( ! empty( $pass1 ) && empty( $pass2 ) ) {
			wc_add_notice( __( 'Please re-enter your password.', 'dokan' ), 'error' );
			$save_pass = false;
		} elseif ( ( ! empty( $pass1 ) || ! empty( $pass2 ) ) && $pass1 !== $pass2 ) {
			wc_add_notice( __( 'New passwords do not match.', 'dokan' ), 'error' );
			$save_pass = false;
		}
	}

	

	if ( $pass1 && $save_pass ) {
		$user->user_pass = $pass1;
	}
	
	
	
	
	
	// Allow plugins to return their own errors.
	do_action_ref_array( 'woocommerce_save_account_details_errors', array( &$errors, &$user ) );

	if ( $errors->get_error_messages() ) {
		foreach ( $errors->get_error_messages() as $error ) {
			wc_add_notice( $error, 'error' );
		}
	}

	if ( wc_notice_count( 'error' ) === 0 ) {

		wp_update_user( $user ) ;

		wc_add_notice( __( 'Account details changed successfully.', 'dokan' ) );

		do_action( 'ec_save_account_details', $user->ID );

		//do_action( 'woocommerce_save_account_details', array(lbDokanUser, ) );

		wp_safe_redirect( dokan_get_navigation_url( ' edit-account' ) );
		exit;
	}

}
add_filter( 'woocommerce_save_account_details_required_fields','custom_woocommerce_save_account_details_required_fields' );
function custom_woocommerce_save_account_details_required_fields( $required_fields ) {
	unset($required_fields["password_current"]);
return $required_fields;
}


function get_attachement_url_ajax(){
    $id = $_POST['id'];
    //$response = wp_get_attachment_url($id);
/*    $response = json_encode($response);
    header( "Content-Type: application/json" );
    echo $response;*/
 
    exit;
} 
add_action('wp_ajax_get_attachement_url_ajax', 'get_attachement_url_ajax');
add_action('wp_ajax_nopriv_get_attachement_url_ajax', 'get_attachement_url_ajax');

remove_action( 'template_redirect', 'dokan_save_account_details' );
add_action( 'template_redirect', 'ec_save_account_details' );

add_action( 'after_setup_theme', 'setup' );
function setup(){
 add_theme_support( 'post-thumbnails' );
  //add_image_size( 'featured-image', 620, 200, true );
  add_image_size( 'event-calendar-image',523, 326, true );
    
}

add_filter( 'image_size_names_choose', 'wpshout_custom_sizes' );
function wpshout_custom_sizes( $sizes ) {
    return array_merge( $sizes, array(
        'event-calendar-image' => __( 'Event Calendar Image' ),
    ) );
}
