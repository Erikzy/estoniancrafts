<?php

class VCMC_Helper {
	public static function subscribe( $data, $apiKey, $listId ) {
		$dataCenter = substr( $apiKey, strpos( $apiKey, '-' ) + 1 );
		$url        = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . $listId . '/members/';

		$data_json = json_encode( $data );

		$ch = curl_init( $url );
		curl_setopt( $ch, CURLOPT_USERPWD, 'user:' . $apiKey );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, [ 'Content-Type: application/json' ] );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );
		curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $data_json );
		$result      = curl_exec( $ch );
		$result_json = json_decode( $result );
		$httpCode    = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
		curl_close( $ch );

		if ( $httpCode == 400 ) {
			if ( $result_json->title == 'Invalid Resource' ) {
				// invalid resource
				return '21';
			} elseif ( $result_json->title == 'Member Exists' ) {
				// member exists
				return '22';
			} else {
				// other errors
				return '23';
			}
		} elseif ( $httpCode == 200 ) {
			// successful
			return '1';
		} else {
			// have an error
			return '0';
		}
	}

	public static function get_lists( $apiKey ) {
		$dataCenter = substr( $apiKey, strpos( $apiKey, '-' ) + 1 );
		$url        = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists';

		$ch = curl_init( $url );

		curl_setopt( $ch, CURLOPT_USERPWD, 'user:' . $apiKey );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, [ 'Content-Type: application/json' ] );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );
		curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );

		$result      = curl_exec( $ch );
		$result_json = json_decode( $result );
		$httpCode    = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
		if ( $httpCode == 200 ) {
			if ( isset( $result_json->lists ) && count( $result_json->lists ) > 0 ) {
				$result_arr = array();
				$result_str = '<select class="vcmc-lists-select"><option value="">Please select a list</option>';
				foreach ( $result_json->lists as $item ) {
					$result_str .= '<option value="' . $item->id . '">' . $item->name . '</option>';
					$result_arr[ $item->id ] = $item->name;
				}
				$key_option = self::generate_key( 'vcmc_lists_', $apiKey );
				update_option( $key_option, $result_arr );
				$result_str .= '</select>';
			} else {
				$result_str = 'Have no list in this account, please login to MailChimp and create a list first.';
			}
		} else {
			$result_str = 'Have an error with this API key, please check again!';
		}

		return $result_str;
	}

	public static function generate_key( $prefix = 'vcmc_', $key = '' ) {
		$new_key = '';
		$key_arr = explode( '-', $key );
		if ( strlen( $key_arr[0] ) > 10 ) {
			$new_key = $prefix . substr( $key_arr[0], 0, 10 );
		}

		return $new_key;
	}

}
