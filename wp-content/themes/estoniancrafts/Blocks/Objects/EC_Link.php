<?php
include_once(get_stylesheet_directory().'/Blocks/EC_Block.php');

class EC_Link extends EC_Base
{
	public $title;					// string
	public $url;					// string
	public $target = '_self';		// string
}