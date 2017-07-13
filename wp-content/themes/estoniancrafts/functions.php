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

// Institutions
if(!function_exists('ec_create_posttype_institutes')) {
	function ec_create_posttype_institutes() {
		register_post_type( 'institution', array(
				'labels' => array(
					'name' => __( 'Institutsioonid' ),
					'singular_name' => __( 'Institutsioon' ),
				),
				'public' => true,
				'has_archive' => true,
				'rewrite' => array('slug' => 'institution'),
				'supports' => array('title','editor','thumbnail','page-attributes')
			)
		);
	}
}
add_action( 'init', 'ec_create_posttype_institutes' );