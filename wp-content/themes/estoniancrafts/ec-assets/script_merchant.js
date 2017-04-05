
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
});
