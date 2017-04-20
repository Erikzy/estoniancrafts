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
	$url_parts = wp_parse_url($_SERVER['REQUEST_URI']);
	if(is_array($url_parts) && isset($url_parts['path']) && !empty($url_parts['path']))
	{
		$url_parts['path'] = trim($url_parts['path'], '/');
		$url_path_parts = explode('/', $url_parts['path']);
		if(count($url_path_parts) >= 3 && $url_path_parts[0] == 'members' && $url_path_parts[2] == 'messages') {
			return 'sidebar-my-account';
		}
		
	}

	// Theme default
	return basel_get_sidebar_name();
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
function bp_message_thread_from_id() {
    echo bp_get_message_thread_from_id();
}
/**
 * Get the user id to the page of the current thread's last author.
 *
 * @return string
 */
function bp_get_message_thread_from_id() {
    global $messages_template;
    return $messages_template->thread->last_sender_id;
}