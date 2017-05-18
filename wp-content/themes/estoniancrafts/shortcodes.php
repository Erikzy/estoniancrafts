<?php

class EC_Shortcodes
{
	public static function init()
	{
		add_action( 'wp_loaded', array(__CLASS__, 'wp_loaded_action') );
	}

	public static function wp_loaded_action()
	{
	}
}
EC_Shortcodes::init();