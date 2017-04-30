<?php if ( ! defined('BASEL_THEME_DIR')) exit('No direct script access allowed');

/**
 * ------------------------------------------------------------------------------------------------
 * Array of versions for dummy content import section
 * ------------------------------------------------------------------------------------------------
 */

return apply_filters( 'basel_get_versions_to_import', array(
	'base' => array(
		'title' => 'Base content (required)',
		'process' => 'xml,home,shop,menu,widgets,options,sliders'
	),
	'hero-slider' => array(
		'title' => 'Hero slider',
		'process' => 'xml,home,options,widgets,sliders'
	),
	'watches' => array(
		'title' => 'Watches store',
		'process' => 'xml,home,options,widgets,sliders'
	),
	'flowers' => array(
		'title' => 'Flowers store',
		'process' => 'xml,home,options,widgets,sliders'
	),
	'furniture' => array(
		'title' => 'Furniture demo',
		'process' => 'xml,home,options,widgets,sliders'
	),
	'fashion' => array(
		'title' => 'Fashion demo',
		'process' => 'xml,home,options,widgets,sliders'
	),
	'bicycle' => array(
		'title' => 'Bicycle demo',
		'process' => 'xml,home,options,widgets,sliders'
	),
	'minimalist' => array(
		'title' => 'The Minimalist demo',
		'process' => 'xml,home,options,widgets,sliders'
	),
	'lingerie' => array(
		'title' => 'Lingerie demo',
		'process' => 'xml,home,options,widgets,sliders'
	),
	'jewellery' => array(
		'title' => 'Jewellery demo',
		'process' => 'xml,home,options,widgets,sliders'
	),
	'electronics' => array(
		'title' => 'Electronics demo',
		'process' => 'xml,home,options,widgets,sliders'
	),
	'wine' => array(
		'title' => 'Wine demo',
		'process' => 'xml,home,options,widgets,sliders'
	),
	'fashion-flat' => array(
		'title' => 'Fashion flat',
		'process' => 'xml,home,options,widgets,sliders'
	),
	'cosmetics' => array(
		'title' => 'Cosmetics',
		'process' => 'xml,home,options,widgets,sliders'
	),
	'sport' => array(
		'title' => 'Sport',
		'process' => 'xml,home,options,widgets,sliders'
	),
	'landing' => array(
		'title' => 'Landing',
		'process' => 'xml,home,options,widgets,sliders'
	),
	'flat-full-width' => array(
		'title' => 'Flat full-width',
		'process' => 'xml,home,options,widgets,sliders'
	),
	'coffee' => array(
		'title' => 'Coffee',
		'process' => 'xml,home,options,widgets,sliders'
	),
	'organic' => array(
		'title' => 'Organic',
		'process' => 'xml,home,options,widgets,sliders'
	),
	'parallax' => array(
		'title' => 'Parallax',
		'process' => 'xml,home,options,widgets,sliders'
	),
	'contact-us' => array(
		'title' => 'Contact us',
		'process' => 'xml,page_menu'
	),
	'contact-us-2' => array(
		'title' => 'Contact Us 2',
		'process' => 'xml,page_menu'
	),
	'faq' => array(
		'title' => 'FaQs',
		'process' => 'xml,page_menu'
	),
	'about-me' => array(
		'title' => 'About me',
		'process' => 'xml,page_menu'
	),
	'our-shop' => array(
		'title' => 'Our shop',
		'process' => 'xml,page_menu'
	),
	'our-services' => array(
		'title' => 'Our Services',
		'process' => 'xml,page_menu'
	),
	'our-company' => array(
		'title' => 'Our Company',
		'process' => 'xml,page_menu'
	),
	'our-gallery' => array(
		'title' => 'Our Gallery',
		'process' => 'xml,page_menu,sliders'
	),
	'coming-soon' => array(
		'title' => 'Coming soon (maintenance)',
		'process' => 'xml'
	),
	'coming-soon-2' => array(
		'title' => 'Coming soon 2 (maintenance)',
		'process' => 'xml,sliders'
	),

) );