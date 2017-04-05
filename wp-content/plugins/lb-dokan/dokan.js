jQuery(document).ready(function($){

	// Display the edit page inside .container (visual tweak)
	$('.dokan-dashboard .main-page-wrapper .container-fluid').removeClass('container-fluid').addClass('container');

	$('.lb-elastic-add').click(function(){

		var elem = $(this).parent().find('.lb-elastic-element:first').clone();
		elem.find('input, select').val('');

		var newId = Date.now();
		elem.find('input, select').each(function() {
		    this.name = this.name.replace('[0]', '['+ newId +']');
		});

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

	        // var image_url = json.url;
	        // var image_caption = json.caption;
	        // var image_title = json.title;

	        // console.log(json);

	        clicked.parent().find('input').val(json.id);
	        clicked.parent().find('.lb-file-placeholder').addClass('active');
	        clicked.parent().find('.lb-remove-doc').addClass('active');
	        clicked.removeClass('active');

	    });

	    media_uploader.open();
	}

	$('.lb-add-img').click(function(){

		open_media_uploader_image();

		return false;
	});

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



	/**
	 * Tag selection
	 */
	// var lastResults = [];
	$("#lb-tags").select2({
	    multiple: true,
	    placeholder: "Please enter tags",
	    tokenSeparators: [","],
	    ajax: {
	        multiple: true,
		    url: siteurl + '/wp-admin/admin-ajax.php?action=lb_tags',
	        dataType: "json",
	        type: "POST",
	        // results: function (data, page) {
	        //     lastResults = data.results;
	        //     return data;
	        // }
	    }
	});

});