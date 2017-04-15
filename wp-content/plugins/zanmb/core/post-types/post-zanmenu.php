<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/*-----------------------------------------------------------------------------------*/
/* MEGA MENU
/*-----------------------------------------------------------------------------------*/
function zmb_post_type_zanmenu() {

	$labels = array(
		'name'               => esc_html__( 'Zan Post', 'zanmb' ),
		'singular_name'      => esc_html__( 'Zan Post Item', 'zanmb' ),
		'add_new'            => esc_html__( 'Add New', 'zanmb' ),
		'add_new_item'       => esc_html__( 'Add New Zan Post Item', 'zanmb' ),
		'edit_item'          => esc_html__( 'Edit Zan Post Item', 'zanmb' ),
		'new_item'           => esc_html__( 'New Zan Post Item', 'zanmb' ),
		'view_item'          => esc_html__( 'View Zan Post Item', 'zanmb' ),
		'search_items'       => esc_html__( 'Search Zan Post Items', 'zanmb' ),
		'not_found'          => esc_html__( 'No Zan Post Items found', 'zanmb' ),
		'not_found_in_trash' => esc_html__( 'No Zan Post Items found in Trash', 'zanmb' ),
		'parent_item_colon'  => esc_html__( 'Parent Zan Post Item:', 'zanmb' ),
		'menu_name'          => esc_html__( 'Zan Post', 'zanmb' ),
	);

	$args = array(
		'labels'              => $labels,
		'hierarchical'        => false,
		'description'         => esc_html__( 'Zan Posts.', 'zanmb' ),
		'supports'            => array( 'title', 'editor' ),
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 40,
		'show_in_nav_menus'   => true,
		'publicly_queryable'  => false,
		'exclude_from_search' => true,
		'has_archive'         => false,
		'query_var'           => true,
		'can_export'          => true,
		'rewrite'             => false,
		'capability_type'     => 'post'
	);

	register_post_type( 'zanmenu', $args );
}

add_action( 'init', 'zmb_post_type_zanmenu' );

