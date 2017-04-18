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
	foreach(func_get_args() as $arg)
	{
		print '<script>console.log('. json_encode( $arg ) .');</script>';
	}
}