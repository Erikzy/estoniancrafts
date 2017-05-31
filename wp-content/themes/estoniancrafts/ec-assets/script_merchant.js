
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

	/*
	 * Order handling
	 */
	var ec_merchant = {
		init: function () {
			$('body').on('click', '.get-product-statistics', ec_merchant.getProductStatistics);
		},

		getProductStatistics: function (e) {
			e.preventDefault();

			var parent = $(e.target).parent().parent();
			$(parent).block({message: null, overlayCSS: { background: '#fff url('+ dokan.ajax_loader +') no-repeat center', opacity: 1.0 }});

			$.get($(e.target).parent().attr('href'), function (response) {
				$(parent).unblock();
				$(parent).html(response);
			});

			return false;
		}

	};

	ec_merchant.init();

});
