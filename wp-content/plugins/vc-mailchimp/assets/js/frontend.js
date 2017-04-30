jQuery( document ).ready( function() {
	jQuery( ".vcmc-form" ).submit( function( e ) {
		var vcmc_fields = {};
		var vcmc_api = jQuery( this ).attr( 'data-api' );
		var vcmc_list = jQuery( this ).attr( 'data-list' );
		var vcmc_skip = jQuery( this ).attr( 'data-skip' );
		var vcmc_message = jQuery( this ).attr( 'data-message' );
		var vcmc_message_json = JSON.parse( vcmc_message );
		var vcmc_fname = jQuery( this ).find( '.vcmc-fname' ).val();
		var vcmc_lname = jQuery( this ).find( '.vcmc-lname' ).val();
		var vcmc_email = jQuery( this ).find( '.vcmc-email' ).val();

		jQuery( '.vcmc-field-js' ).each( function() {
			var this_tag = jQuery( this ).attr( 'data-tag' );
			var this_val = jQuery( this ).val();
			if ( jQuery( this ).attr( 'type' ) == 'radio' ) {
				if ( jQuery( this ).is( ':checked' ) ) {
					if ( (
						     this_tag != ''
					     ) && (
						     this_val != ''
					     ) ) {
						vcmc_fields[this_tag] = this_val;
					}
				}
			} else if ( jQuery( this ).attr( 'type' ) == 'checkbox' ) {
				if ( jQuery( this ).is( ':checked' ) ) {
					if ( (
						     this_tag != ''
					     ) && (
						     this_val != ''
					     ) ) {
						if ( typeof vcmc_fields[this_tag] === 'undefined' ) {
							vcmc_fields[this_tag] = this_val;
						} else {
							vcmc_fields[this_tag] = vcmc_fields[this_tag] + ', ' + this_val;
						}
					}
				}
			} else {
				if ( (
					     this_tag != ''
				     ) && (
					     this_val != ''
				     ) ) {
					vcmc_fields[this_tag] = this_val;
				}
			}
		} );

		var $vcmc_submit = jQuery( this ).find( '.vcmc-submit' );
		$vcmc_submit.addClass( 'loading' );
		if ( (
			     vcmc_email != ''
		     ) && vcmcValidateEmail( vcmc_email ) ) {
			var vcmc_data = {
				action: 'vcmc_subscribe',
				vcmc_nonce: vcmc_vars.vcmc_nonce,
				vcmc_api: vcmc_api,
				vcmc_list: vcmc_list,
				vcmc_skip: vcmc_skip,
				vcmc_fname: vcmc_fname,
				vcmc_lname: vcmc_lname,
				vcmc_email: vcmc_email,
				vcmc_fields: JSON.stringify( vcmc_fields )
			};
			jQuery.ajax( {
				method: 'POST',
				url: vcmc_vars.vcmc_ajax_url,
				data: vcmc_data,
				success: function( response ) {
					var response_message = '';
					var growl_style = 'notice';
					switch ( response ) {
						case '0':
							response_message = vcmc_message_json.messages_error;
							growl_style = 'error';
							break;
						case '1':
							if ( vcmc_skip == 'true' ) {
								response_message = vcmc_message_json.messages_successfully_skip;
							} else {
								response_message = vcmc_message_json.messages_successfully;
							}
							break;
						case '21':
							response_message = vcmc_message_json.messages_missing;
							growl_style = 'error';
							break;
						case '22':
							response_message = vcmc_message_json.messages_already;
							growl_style = 'warning';
							break;
						case '23':
							response_message = vcmc_message_json.messages_error;
							growl_style = 'error';
							break;
						default:
							response_message = '';
					}
					jQuery.growl( {
						location: vcmc_message_json.messages_position,
						style: growl_style,
						title: '',
						message: response_message
					} );
					$vcmc_submit.removeClass( 'loading' );
				},
			} );
		} else {
			response_message = vcmc_message_json.messages_missing_email;
			growl_style = 'error';
			if ( response_message != '' ) {
				jQuery.growl( {
					location: vcmc_message_json.messages_position,
					style: growl_style,
					title: '',
					message: response_message
				} );
			}
			$vcmc_submit.removeClass( 'loading' );
		}
		e.preventDefault();
	} );
} );

function vcmcValidateEmail( email ) {
	var emailReg = /\S+@\S+\.\S+/;
	if ( ! emailReg.test( email ) ) {
		return false;
	} else {
		return true;
	}
}
