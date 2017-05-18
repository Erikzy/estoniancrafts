<?php

class EC_Widgets
{
	public static function init()
	{
		add_action('widgets_init', array(__CLASS__, 'initWidgets'));
	}

	public static function initWidgets()
	{
		include_once('widgets/myaccount_sidebar_menu_widget.php');
		register_widget('EC_Myaccount_Sidebar_Menu_Widget');
	}
}
EC_Widgets::init();