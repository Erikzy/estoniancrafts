<?php
if ( ! class_exists( 'VCMC_Select_Image' ) ) {
	class VCMC_Select_Image {
		function __construct() {
			if ( class_exists( 'WpbakeryShortcodeParams' ) ) {
				WpbakeryShortcodeParams::addField( 'vcmc_select_image', array( $this, 'output_param' ) );
			}
		}

		function output_param( $settings, $saved_value ) {
			$param_name = isset( $settings['param_name'] ) ? $settings['param_name'] : '';
			$values     = isset( $settings['value'] ) ? $settings['value'] : '';
			$image      = isset( $values[ $saved_value ]['image'] ) ? $values[ $saved_value ]['image'] : '';

			$uid    = uniqid( 'vcmc_select_image_' );
			$output = '';

			if ( is_array( $values ) && ( count( $values ) > 0 ) ) {
				$output .= '<div class="vc_row" id="' . esc_attr( $uid ) . '"><div class="vc_col-sm-12 vcmc_select_image_input"><select name="' . $param_name . '" class="wpb_vc_param_value">';
				foreach ( $values as $key => $value ) {
					$output .= '<option value="' . $key . '" data-img="' . $value['image'] . '" ' . ( $saved_value == $key ? 'selected' : '' ) . '>' . $value['name'] . '</option>';
				}
				$output .= '</select></div><div class="vc_col-sm-12 vcmc_select_image_preview">' . ( $image != '' ? '<img src="' . $image . '"/>' : '' ) . '</div></div>';
				$output .= '<script type="text/javascript">jQuery("#' . $uid . ' select").on("change", function(){var img = jQuery(this).find(":selected").attr("data-img"); if(img !=""){jQuery("#' . $uid . ' .vcmc_select_image_preview").html("<img src="+img+">");} else {jQuery("#' . $uid . ' .vcmc_select_image_preview").html("");}});</script>';
			}

			return $output;
		}
	}
}
if ( class_exists( 'VCMC_Select_Image' ) ) {
	$VCMC_Select_Image = new VCMC_Select_Image();
}