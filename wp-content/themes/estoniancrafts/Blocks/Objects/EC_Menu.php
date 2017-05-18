<?php
include_once(get_stylesheet_directory().'/Blocks/EC_Block.php');

class EC_Menu extends EC_Base
{
	public $id;						// string
	public $title;					// string
	public $items = array();		// array
}