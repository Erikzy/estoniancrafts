<?php
/*
 
 	Html template
 */
wp_enqueue_style('main-styles', plugins_url().'/user-banner-rotator/assets/css/user-banner-rotator.css' );

wp_register_style( 'font-awe', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css' );
wp_enqueue_style('font-awe');

wp_enqueue_media();
require_once( ABSPATH . 'wp-admin/includes/image.php' );
if ( isset( $_POST['submit_image_selector'] ) && isset( $_POST['image_attachment_id'] ) ) :
	update_option( 'media_selector_attachment_id', absint( $_POST['image_attachment_id'] ) );
endif;

?>

<div class="center-block ">	
  <div class="img-rot-wrapper center-block">
  <?php
  
  	 if(sizeof($slides) > 0){ 
  	    foreach($slides as $slide){
		  echo '<div class="user-rotator-banner-element">';
		  echo '<i class="fa fa-trash" onclick=remove_slide('.$slide->wp_attachment_id.','.$banner_instance_id.')>Delete</i>'  ;
    //  echo '<div class=" center-block >'.wp_get_attachment_image($slide->wp_attachment_id,array($banner_instance_width,$banner_instance_height)).'</div>';
		  echo '<div class=" center-block" >'.wp_get_attachment_image($slide->wp_attachment_id,'thumbnail').'</div>';
      echo '</div>';
 	    }
 	 }
   ?>
  <button id="upload_button">Add Slide</button>
</div>
  <?php wp_nonce_field( 'user_banner_upload', 'user_banner_upload_form' ); ?>
</div>

<script type="text/javascript">




jQuery(document).ready(function($) {

  // Define a variable to be used to store the frame data
  var file_frame;
  var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id
  var set_to_post_id = <?php echo $banner_instance_id;?>; // Set this

  var wp_nonce = $("#user_banner_upload_form").val();	
  $('#upload_button').live('click', function( event ){

    element = $(this);
    event.preventDefault();

    if (file_frame) {
      file_frame.uploader.uploader.param('post_id', set_to_post_id);
      file_frame.open();
      return;
    } else {
      wp.media.model.settings.post.id = set_to_post_id;
    }
	
    file_frame = wp.media.frames.file_frame = wp.media({
      title: $(this).data('Add slides'),
      button: {
        text: $(this).data('Add'),
      },
      multiple: false,  // Set to false to allow only one file to be selected
      states: [
         new wp.media.controller.Library({
                        library:   wp.media.query({ type: 'image' }),
                        multiple:  false,
                        date:      false,
                        priority:  20,
                        canEdit:   false
         }),

         new wp.media.controller.Cropper({
          	imgSelectOptions:	{
          	    handles: true,
                keys: true,
                canSkipCrop: false,
                instance: true,
                persistent: true,
                aspectRatio:'630:300',
                imageWidth: 630,
                imageHeight: 300,
                x1: 0,
                y1: 0,
                x2: 630,
                y2:300
            }
         })
      ]	
    });

    file_frame.on('select', function() {
		//attachment = file_frame.state().get('selection').first().toJSON();
		//$( '#image-preview' ).attr( 'src', attachment.url ).css( 'width', 'auto' );
		//$( '#image_attachment_id' ).val( attachment.id );
		//add_slide(attachment.id);
		//wp.media.model.settings.post.id = wp_media_post_id;
		file_frame.setState("cropper");
	    file_frame.open();
    });

    file_frame.on('cropped', function(croppedImage) {
		console.log(croppedImage);
		attachment = croppedImage;
		$( '#image-preview' ).attr( 'src', attachment.url ).css( 'width', 'auto' );
		$( '#image_attachment_id' ).val( attachment.attachment_id );
		add_slide(attachment.attachment_id, set_to_post_id);
		wp.media.model.settings.post.id = wp_media_post_id;
		file_frame.setState("library");
		file_frame.close();		
    });

    file_frame.on('skippedCrop', function(selection) {
	/*	attachment = selection;
		$( '#image-preview' ).attr( 'src', attachment.url ).css( 'width', 'auto' );
		$( '#image_attachment_id' ).val( attachment.id );
		add_slide(attachment.id);
	*/
		wp.media.model.settings.post.id = wp_media_post_id;
		file_frame.setState("library");
		file_frame.close();
    });


    file_frame.open();
  });

});

function add_slide(attachment_id,set_to_post_id){

  var nonce_value = jQuery('#user_banner_upload_form').val();
    var data = {
                'action': 'user-banner-add-slide',
                'user_attachment_id': attachment_id,
                'post_id': set_to_post_id,
                'user_banner_upload_form' : nonce_value
                
            };
     jQuery.post('<?php echo site_url();?>/wp-admin/admin-ajax.php', data, function (e) {
            console.log(e);
			location.reload();
      })
  
}

function remove_slide(attachment_id,set_to_post_id){
var nonce_value = jQuery('#user_banner_upload_form').val();
var data = {
                'action': 'user-banner-remove-slide',
                'user_attachment_id': attachment_id,
                'post_id': set_to_post_id,
                'user_banner_upload_form' : nonce_value
                
            };
     jQuery.post('<?php echo site_url();?>/wp-admin/admin-ajax.php', data, function (e) {
            console.log(e);
			location.reload();
      })
}
</script>