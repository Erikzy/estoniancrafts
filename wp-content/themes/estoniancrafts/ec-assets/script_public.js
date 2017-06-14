
jQuery(document).ready(function($) {

    // Class toggle click
    $('.toggle-class').on('click', function(e)
    {
        e.preventDefault();

        var target = $(this).attr('data-target');
        var toggleClass = $(this).attr('data-toggle-class');

        $(target).toggleClass(toggleClass);
    });

    var ec_public = {

    	init: function () 
    	{
    		$('#ask_information_form').on('submit', ec_public.ask_information);
    	},

    	ask_information: function (e)
    	{
    		e.preventDefault();

    		var parameters = $('#ask_information_form').serialize();

    		$('#ask_information_form').block({ message: null, overlayCSS: { background: '#fff url(' + dokan.ajax_loader + ') no-repeat center', opacity: 0.6 } });
    
    		$.post($('#ask_information_form').attr('action'), parameters, function (response) {
    			$('#ask_information_form').unblock();
                var result = JSON.parse(response);
    			if (result.success) {
    				$('#ask_information_form_container').html(result.message);
    			} else if (result.message) {
                    alert(result.message);
                }
    		})

    		return false;
    	}

    };

    ec_public.init();

});
