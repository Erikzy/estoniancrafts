<?php
/*
 
 	Html template
 */


?>

<div>









</div>

<script type="text/javascript">


jQuery(document).ready(function($) {

  // Define a variable to be used to store the frame data
  var file_frame;
  var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id
  var set_to_post_id = <?php echo $instanceId?>; // Set this

  $('#upload_button').live('click', function( event ){

    element = $(this);

    event.preventDefault();

    // If the media frame already exists, reopen it.
    if (file_frame) {
      // Set the post ID to what we want
      file_frame.uploader.uploader.param('post_id', set_to_post_id);
      // Open frame
      file_frame.open();
      return;
    } else {
      // Set the wp.media post id so the uploader grabs the ID we want when initialised
      wp.media.model.settings.post.id = set_to_post_id;
    }

    // Create the media frame.
    file_frame = wp.media.frames.file_frame = wp.media({
      title: $(this).data('uploader_title'),
      button: {
        text: $(this).data('uploader_button_text'),
      },
      multiple: true  // Set to false to allow only one file to be selected
    });

    // When an image(s) have been selected, run a callback.
    file_frame.on('select', function() {

      // Do something if necessary
        function_to_fire();

    });

    // Finally, open the modal
    file_frame.open();
  });

});

</script>