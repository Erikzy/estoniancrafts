<?php
if ( ! class_exists( 'VCMC_Param_API' ) ) {
	class VCMC_Param_API {
		function __construct() {
			if ( class_exists( 'WpbakeryShortcodeParams' ) ) {
				WpbakeryShortcodeParams::addField( 'vcmc_api', array( $this, 'output_param' ) );
			}
		}

		function output_param( $settings, $value ) {
			$param_name       = isset( $settings['param_name'] ) ? $settings['param_name'] : '';
			$value_api        = '';
			$value_api_option = '';
			$value_list       = '';
			if ( $value != '' ) {
				$value_arr = explode( ',', $value );
				if ( isset( $value_arr[0] ) ) {
					$value_api        = $value_arr[0];
					$value_api_option = VCMC_Helper::generate_key( 'vcmc_lists_', $value_api );
				}
				if ( isset( $value_arr[1] ) ) {
					$value_list = $value_arr[1];
				}
			}
			$uid    = uniqid( 'vcmc-api-' );
			$output = '<div id="' . $uid . '" class="vcmc-api-box"><table>';
			$output .= '<tr><td><input name="' . $param_name . '" value="' . $value . '" class="wpb_vc_param_value vcmc-api-value" type="hidden" readonly/><input class="vcmc-api-key" type="text" value="' . esc_attr( $value_api ) . '"/></td><td><input class="vcmc-renew-lists" type="button" value="Get lists"/></td></tr>';
			$output .= '<tr><td colspan="2"><div class="vcmc-lists">';
			if ( ( $value_api_option != '' ) && get_option( $value_api_option ) ) {
				$output .= '<select class="vcmc-lists-select">';
				foreach ( get_option( $value_api_option ) as $key => $value ) {
					$output .= '<option value="' . $key . '" ' . ( $key == $value_list ? 'selected' : '' ) . '>' . $value . '</option>';
				}
				$output .= '</select>';
			}
			$output .= '</div></td></tr>'; // lists
			$output .= '</table></div>'; // vcmc-api-box

			return $output;
		}
	}
}
if ( class_exists( 'VCMC_Param_API' ) ) {
	$VCMC_Param_API = new VCMC_Param_API();
}