
jQuery(document).ready(function($)
{

	if (!window.hasOwnProperty('ec_product_limits')) {
		window.ec_product_limits = {};
	}

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

	var ec_merchant = {
		
		init: function ()
		{
			$('body').on('submit', '#ec_add_shop_user', this.add_shop_user);
			
			$('body').on('change input keyup', '#ec_add_shop_user input[name="email"]', function(e){ $('#ec_add_shop_user button[type="submit"]').toggle($(e.target).val() !== ''); });
			$('body').on('click', '.ec_remove_shop_user', this.remove_shop_user);
			
			$('body').on('submit', '.ec_add_user_relation_value', this.add_shop_user_relation_value);
			$('body').on('change input', '.ec_add_user_relation_value input[name="value"]', function (e){ $(e.target).parent().find('button[type="submit"]').show(); });
			$('body').on('click', '.get-product-statistics', ec_merchant.getProductStatistics);

			$('.dokan-product-shipping-tax.dokan-edit-row select[name="lb-dimension-unit"], #_length, #_width, #_height').on('change', ec_merchant.set_dimension_limits);
			ec_merchant.set_dimension_limits();

			// description limits
			if (typeof(window['tinymce']) != 'undefined') {
				tinymce.on('addeditor', function (event) {
					var editor = event.editor;
					if (editor.id === 'post_content') {
						editor.settings.charLimit = ec_product_limits.descriptionLimit;
						editor.onKeyDown.add(ec_merchant.check_tinymce_limit);
					} else if (editor.id === 'post_excerpt') {
						editor.settings.charLimit = ec_product_limits.shortDescriptionLimit;
						editor.onKeyDown.add(ec_merchant.check_tinymce_limit);
					}
				});
			}

		},

		check_tinymce_limit: function (editor, e)
		{
			var charLimit = editor.settings.charLimit;
			var charCount = editor.getContent().replace(/(<([^>]+)>)/ig,"").length;

			if (e.keyCode != 8 && e.keyCode != 46 && charCount + 1 > charLimit) {
				e.preventDefault();
				e.stopPropagation();
				return false;
			}
		},

		previousUnit: null,

		set_dimension_limits: function ()
		{
			var unit = $('.dokan-product-shipping-tax.dokan-edit-row select[name="lb-dimension-unit"]').val();
			// find limit correction
			var limitCorrection = 1.0;
			if (unit === 'cm') {
				limitCorrection = 0.1;
			} else if (unit === 'm') {
				limitCorrection = 0.001;
			}

			var maxLength = ec_product_limits.maxLength * limitCorrection;
			var maxWidth = ec_product_limits.maxWidth * limitCorrection;
			var maxHeight = ec_product_limits.maxHeight * limitCorrection;
			
			// find value correction
			var valueCorrection = 1.0;
			var prev = ec_merchant.previousUnit;
			if (prev) {
				if (prev === 'mm') {
					if (unit === 'cm') {
						valueCorrection = 0.1;
					} else if (unit === 'm') {
						valueCorrection = 0.001;
					}
				} else if (prev === 'cm') {
					if (unit === 'mm') {
						valueCorrection = 10.0;
					} else if (unit === 'm') {
						valueCorrection = 0.01;
					}
				} else if (prev === 'm') {
					if (unit === 'cm') {
						valueCorrection = 100.0;
					} else if (unit === 'mm') {
						valueCorrection = 1000.0;
					}
				}
			}

			// clamp values
			var _length = parseFloat($('#_length').val()) * valueCorrection;
			var _width = parseFloat($('#_width').val()) * valueCorrection;
			var _height = parseFloat($('#_height').val()) * valueCorrection;

			_length = _length > maxLength ? maxLength : _length < 0.0 ? 0.0 : _length;
			_width = _width > maxWidth ? maxWidth : _width < 0.0 ? 0.0 : _width;
			_height = _height > maxHeight ? maxHeight : _height < 0.0 ? 0.0 : _height;

			// set limits and correct the value
			$('#_length').attr('max', maxLength);
			$('#_length').val(_length);
			$('#_width').attr('max', maxWidth);
			$('#_width').val(_width);
			$('#_height').attr('max', maxHeight);
			$('#_height').val(_height);

			ec_merchant.previousUnit = unit;
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
    
    
     var mediaUploader;
    //post picture
    //Add picture
    $('[data-action="add"]').click(function(e){
        
        e.preventDefault();
        var $parent_element = $(this).parent();
        var $this = $(this);
        
         // If the uploader object has already been created, reopen the dialog
                if (mediaUploader) {
                    mediaUploader.open();
                    return;
                }
                // Extend the wp.media object
                mediaUploader = wp.media.frames.file_frame = wp.media({
                    title: 'Choose Image',
                    button: {
                        text: 'Choose Image'
                    },
                    multiple: false
                });

                // When a file is selected, grab the URL and set it as the text field's value
                mediaUploader.on('select', function() {
                   
                    attachment = mediaUploader.state().get('selection').first().toJSON();
                    $('[name="post_picture"]', $parent_element).val(attachment.id);
                    $('img', $parent_element).attr('src', attachment.url);
                    $this.addClass('hide');
                    $('[data-action="remove"]', $parent_element).removeClass('hide');
                });
                // Open the uploader dialog
                mediaUploader.open();
        
        
    });
    
    
    //remove image
     $('[data-action="remove"]').click(function(e){
         
        e.preventDefault();
        var $parent_element = $(this).parent();
        var $this = $(this);
        $('[name="post_picture"]', $parent_element).val('');
        $('img', $parent_element).removeAttr('src');
        $this.addClass('hide');
        $('[data-action="add"]', $parent_element).removeClass('hide');
         
         
     });
    

    //get post status and notify to merchan when click on publish button
    $('[data-btn="submit"]').click(function(){
        $('[name="post_status"]').val($(this).attr('data-action'));
         var $parent_element = $(this).closest('#primaryPostForm');
        if($('[name="post_status"]').val()=='pending')
           {
               if (confirm($('.publish_notify', $parent_element).html()) == false) 
               {
                    return false;
               } 
           }
    })
    
    
    
   

});
