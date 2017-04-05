jQuery(document).ready(function($){

	$('.lb-elastic-add').click(function(){

		var elem = $(this).parent().find('.lb-elastic-element:first').clone();
		elem.find('input, select').val('');

		var newId = Date.now();
		elem.find('input, select').each(function() {
		    this.name = this.name.replace('[0]', '['+ newId +']');
		});

		$(this).parent().find('.lb-elastic-elements').append(elem);

		return false;
	});

	var media_uploader = null;

	function open_media_uploader_image()
	{
	    media_uploader = wp.media({
	        frame:  "post", 
	        state:  "insert", 
	        multiple: false
	    });

	    media_uploader.on("insert", function(){
	        var json = media_uploader.state().get("selection").first().toJSON();

	        var image_url = json.url;
	        var image_caption = json.caption;
	        var image_title = json.title;

	        console.log(image_url);

	    });

	    media_uploader.open();
	}

	$('.lb-add-img').click(function(){

		open_media_uploader_image();

		return false;
	});

	$('body').on('click', '.lb-add-doc', function(){

		open_media_uploader_image();

		return false;
	});

});