<?php
include_once(get_stylesheet_directory().'/Blocks/EC_Block.php');

class EC_MenuItem extends EC_Base
{
	public $id;						// string
	public $type = 'link';			// string. link, separator, comment
	public $title;					// string
	public $url;					// string
	public $url_endpoint;			// string
	public $url_target = '_self';	// string
	public $is_open = false;		// boolean
	public $is_current = false;		// boolean
	public $class;					// string
	public $submenu;				// EC_Menu
}