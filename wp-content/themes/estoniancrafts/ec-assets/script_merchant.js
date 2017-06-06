
jQuery(document).ready(function($)
{
	
	// Init widgets
	$('.dropdown-toggle').dropdown();

	// Form section content toggle click
	$('.ec-section-toggle-btn').on('click', function(e)
	{
		e.preventDefault();

		var $container = $(this).closest('.dokan-edit-row').find('.dokan-side-right');
		if($container.is(':visible')) {
			$container.slideUp();
		} else {
			$container.slideDown();
		}
	});

	/*
	 * Products / Product save functions
	 */

	// Publish button click
	$('form.dokan-product-edit-form button.ec-product-publish-btn').on('click', function(e)
	{
		$(this).closest('form').find('input[name=post_status]').val('publish');
	});

	// Save as draft button click
	$('form.dokan-product-edit-form button.ec-merchant-product-save-btn').on('click', function(e)
	{
		$(this).closest('form').find('input[name=post_status]').val('draft');
	});

	var ec_merchant = {
		
		init: function ()
		{
			$('body').on('submit', '#ec_add_shop_user', this.add_shop_user);
			
			$('body').on('change input keyup', '#ec_add_shop_user input[name="email"]', function(e){ $('#ec_add_shop_user button[type="submit"]').toggle($(e.target).val() !== ''); });
			$('body').on('click', '.ec_remove_shop_user', this.remove_shop_user);
			
			$('body').on('submit', '.ec_add_user_relation_value', this.add_shop_user_relation_value);
			$('body').on('change input', '.ec_add_user_relation_value input[name="value"]', function (e){ $(e.target).parent().find('button[type="submit"]').show(); });
		},

		add_shop_user: function (e)
		{
			e.preventDefault();
			if (!$(e.target).find('input[name="email"]').val()) { return false; }
			$(e.target).block({message: null, overlayCSS: { background: '#fff url('+ basel_settings.ajax_loader +') no-repeat center', opacity: 0.6 }});

			$.post($(e.target).attr('action'), $(e.target).serialize(), function (response) {
				$(e.target).unblock();
				var result = JSON.parse(response);
				if (result.success) {
					$(e.target).find('input[name="email"]').val('');
					$('#ec_add_shop_user button[type="submit"]').hide();
					$('#shop_member_container').append(result.value);
				} else {
					alert(result.value);
				}
			});

			return false;
		},

		remove_shop_user: function (e)
		{
			e.preventDefault();

			$(e.target).parent().block({message: null, overlayCSS: { background: '#fff url('+ basel_settings.ajax_loader +') no-repeat center', opacity: 0.6 }});

			var params = {
				_wpnonce: $(e.target).data('security'),
				action: 'ec_remove_shop_user',
				relation_id: parseInt($(e.target).data('relationId'))
			};

			$.post($(e.target).attr('href'), params, function (response) {
				var id;
				if ((id = parseInt(response)) && id === params.relation_id) {
					$('#ec_user_relation_' + id).remove();
				} else {
					alert(response);
				}
				$(e.target).parent().unblock();
			});

			return false;
		},

		add_shop_user_relation_value: function (e) 
		{
			e.preventDefault();

			$(e.target).block({message: null, overlayCSS: { background: '#fff url('+ basel_settings.ajax_loader +') no-repeat center', opacity: 0.6 }});

			$.post($(e.target).attr('action'), $(e.target).serialize(), function (response) {
				var id;
				if ((id = parseInt(response))) {
					$(e.target).find('button[type="submit"]').hide();
				} else {
					alert(response);
				}
				$(e.target).unblock();
			});

			return false;
		}

	};

	ec_merchant.init();

});
