jQuery(document).ready(function($){

	$('.lb-elastic-add').click(function(){

		var elem = $(this).parent().find('.lb-elastic-element:first').clone();
		elem.find('input, select').val('');

		// Reset add a file inputs
		elem.find('.lb-file-placeholder, .lb-remove-doc').removeClass('active');
		elem.find('.lb-add-doc').addClass('active');

		$(this).parent().find('.lb-elastic-elements').append(elem);

		return false;
	});

	var media_uploader = null;

	function open_media_uploader_image( clicked )
	{
	    media_uploader = wp.media({
	        frame:  "post", 
	        state:  "insert", 
	        multiple: false
	    });

	    media_uploader.on("insert", function(){
	        var json = media_uploader.state().get("selection").first().toJSON();

	        clicked.parent().find('input').val(json.id);
	        clicked.parent().find('.lb-file-placeholder').addClass('active');
	        clicked.parent().find('.lb-remove-doc').addClass('active');
	        clicked.removeClass('active');

	    });

	    media_uploader.open();
	}

	$('body').on('click', '.lb-remove-doc', function(){

		$(this).parent().find('input').val('');
        $(this).parent().find('.lb-file-placeholder').removeClass('active');
        $(this).parent().find('.lb-add-doc').addClass('active');
        $(this).removeClass('active');

		return false;
	});

	$('body').on('click', '.lb-add-doc', function(){

		var clicked = $(this);

		open_media_uploader_image(clicked);

		return false;
	});

});