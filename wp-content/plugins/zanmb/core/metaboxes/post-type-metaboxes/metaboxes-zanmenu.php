<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Include and setup custom metaboxes and fields.
 *
 * @category Lucky Shop Core
 * @package  Metaboxes
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link     https://github.com/WebDevStudios/CMB2
 */


if ( !class_exists( 'cmb_Meta_Box' ) ) {
	zmb_require_once( ZMB_CLASSES . '/metaboxes/init.php' );
};

add_filter( 'cmb2_admin_init', 'zmb_zanmenu_metaboxes' );
/**
 * Define the metabox and field configurations.
 *
 * @return array
 */
function zmb_zanmenu_metaboxes() {

	// Start with an underscore to hide fields from custom fields list
	$prefix = '_zmb_';

	$meta_boxes = new_cmb2_box(
		array(
			'title'        => esc_html__( 'Advance', 'zanmb' ),
			'id'           => 'zmb_zanmenu_metas',
			'object_types' => array( 'zanmenu' ), // Post type
			// 'show_on_cb' => 'lena_core_show_if_front_page', // function should return a bool value
			'context'      => 'normal',
			'priority'     => 'high',
			'show_names'   => true, // Show field names on the left
			// 'cmb_styles' => false, // false to disable the CMB stylesheet
			// 'closed'     => true, // true to keep the metabox closed by default
		)
	);

	$field_args = array(
		array(
			'name'    => esc_html__( 'Background Color', 'zanmb' ),
			'id'      => $prefix . 'bg_color',
			'type'    => 'zmb_color_picker',
			'default' => 'rgba(255, 255, 255, 1)',
			'desc'    => esc_html__( 'Set menu item content background color', 'zanmb' )
		),
		array(
			'name'    => esc_html__( 'Custom Class', 'zanmb' ),
			'desc'    => esc_html__( 'Custom additional class. Ex: no-padding no-margin', 'zanmb' ),
			'default' => '',
			'id'      => 'zmb_custom_class',
			'type'    => 'text',
		)
	);

	foreach ( $field_args as $field ):

		$meta_boxes->add_field( $field );

	endforeach;

}

