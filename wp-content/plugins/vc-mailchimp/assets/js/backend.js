jQuery( document ).ready( function() {
	jQuery( '.vcmc-lists-select' ).live( 'change', function() {
		var api_box = jQuery( this ).closest( '.vcmc-api-box' );
		var api_key = api_box.find( '.vcmc-api-key' ).val();
		var list_id = jQuery( this ).val();
		api_box.find( '.vcmc-api-value' ).val( api_key + ',' + list_id );
	} );

	jQuery( '.vcmc-api-key' ).live( 'keyup', function() {
		var api_box = jQuery( this ).closest( '.vcmc-api-box' );
		var api_key = jQuery( this ).val();
		var list_id = api_box.find( '.vcmc-lists-select' ).val();
		api_box.find( '.vcmc-api-value' ).val( api_key + ',' + list_id );
	} );

	jQuery( '.vcmc-renew-lists' ).live( 'click', function() {
		var this_btn = jQuery( this );
		var api_box = jQuery( this ).closest( '.vcmc-api-box' );
		var api_key = api_box.find( '.vcmc-api-key' ).val();
		this_btn.addClass( 'loading' );
		this_btn.val( 'Getting...' );
		data = {
			action: 'vcmc_get_lists',
			vcmc_nonce: vcmc_vars.vcmc_nonce,
			api_key: api_key
		};
		jQuery.ajax( {
			method: 'POST',
			url: vcmc_vars.vcmc_ajax_url,
			data: data,
			success: function( response ) {
				api_box.find( '.vcmc-lists' ).html( response );
				this_btn.removeClass( 'loading' );
				this_btn.val( 'Get lists' );
			},
		} )
	} );
} );