
    <?php wp_enqueue_media(); 
    // Get WordPress' media upload URL
$upload_link = esc_url( get_upload_iframe_src( 'image', $post->ID ) );

    ?>
    <form action="" id="edit-blog-post-form" method="POST">
       <?php wp_nonce_field( 'user_banner_upload', 'user_banner_upload_form' ); ?>
        <p class="row">
            <div class="col-md-8">
                <div class="fetaute-image">
                    <img src="<?php if( $post_thumbnail_url ) echo $post_thumbnail_url[0]; ?>" id='f-image' data-img="post_picture">
                    <input type="hidden" name="post_picture" id='hidden-input' value="<?php if( $post_thumbnail_id ) echo $post_thumbnail_id; ?>">
                    <a href="<?php echo $upload_link ?>" btn-name="post_picture" data-action="add" data-btn="manage_image"  id='upload_button' class=" <?php if( $post_thumbnail_id ) echo 'hide'; ?>">Add Image</a>
                    <a href="#" btn-name="post_picture" data-action="remove" id='remove_button'  data-btn="manage_image" class="<?php if( !$post_thumbnail_id ) echo 'hide'; ?>">Remove Image</a>   
                </div>
            </div>            
        </p>
        <p class="row">
            <div class="col-md-8">
                <label for="postTitle"><?php _e('Post Title:', 'framework'); ?>
                    <?php if ( isset($errors['post_title']) ) { ?>
                    <span class="error"><?= $errors['post_title'] ?></span>
                    <div class="clearfix"></div>
                <?php } ?> 
                </label>
                <input type="text" name="post_title" id="postTitle" class="" value="<?= $post->post_title ?>"/>  
            </div>
        </p>
        <p class="row">
            <div class="col-md-8">
                <label for="postContent"><?php  _e('Post Content:', 'framework'); ?>
                    <?php if ( isset($errors['post_content']) ) { ?>
                    <span class="error"><?= $errors['post_content'] ?></span>
                    <div class="clearfix"></div>
                <?php } ?> 
                </label>
                <?php wp_editor(html_entity_decode($post->post_content),  'post_content', array('editor_height' => 150,  'media_buttons' => true, 'editor_class' => '') ); ?>
                <input type="hidden" name="temp_publish" value="exist">
            </div>
        </p>
        <p class="row">
            <div class="col-md-8">
                <input type="submit" class="btn button medium-gray-button" name="edit_action[delete]" value="<?php  _e('Delete', 'ktt'); ?>" />
                <input type="submit" class="btn button medium-gray-button" name="edit_action[draft]" value="<?php  _e('Draft', 'ktt'); ?>" />
                <input type="submit" class="btn btn-color-primary button medium-orange-button" name="edit_action[publish]" value="<?php  _e('Publish', 'ktt'); ?>" />
            </div>
        </p>
    </form>
<div class="clear"></div>
<div class="mfp-bg mfp-ready confirm-publish" style="display:none"></div>
<div class="mfp-wrap mfp-close-btn-in mfp-auto-cursor mfp-ready  confirm-publish" id="before_publish" style="display:none;overflow-y: auto;">
    <div class="mfp-container mfp-s-ready mfp-inline-holder" style="height:40%;">
        <div class="mfp-content">
            <div class="white-popup add-to-cart-popup popup-added_to_cart">
                <div class="added-to-cart">
                    <p><?php _e('Carefully look through the post. The post will go to translation', 'ktt'); ?></p>
                    <a href="#" class="btn close-popup  before-publish smaller-gray-button" data-value="cancle">Review</a>
                    <a href="#" class="btn btn-color-primary button view-cart before-publish smaller-orange-button" data-value="publish">Publish</a>
                </div>
                <button  type="button" class=" mfp-close  before-publish" data-value="cancle">×</button>
            </div>
        </div>
    </div>
</div>
<script>
    jQuery(document).ready(function($) {

  // Define a variable to be used to store the frame data
  var file_frame;
  var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id
  var set_to_post_id = "<?php echo $post->ID; ?>"; // Set this
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
      title: $(this).data('Add your image '),
      button: {
        text: $(this).data('Add'),
      },
      multiple: false,  // Set to false to allow only one file to be selected

    });

    file_frame.on('select', function() {
        attachment = file_frame.state().get('selection').first().toJSON();
        //$( '#image-preview' ).attr( 'src', attachment.url ).css( 'width', 'auto' );
        //$( '#image_attachment_id' ).val( attachment.id );
        jQuery('#hidden-input').val( attachment.id );
        jQuery('#f-image').attr('src', attachment.url  ) ;
        jQuery('#upload_button').addClass('hide') ;
        jQuery('#remove_button').removeClass('hide') ;
        wp.media.model.settings.post.id = wp_media_post_id;
        //file_frame.setState("cropper");
        //file_frame.open();
    });
    file_frame.open();
  });

   jQuery('#remove_button').on( 'click', function( event ){

    event.preventDefault();

    // Clear out the preview image
    jQuery('#f-image').attr('src', '');

    // Un-hide the add image link
    jQuery('#upload_button').removeClass( 'hide' );

    // Hide the delete image link
    jQuery('#remove_button').addClass( 'hide' );

    // Delete the image id from the hidden input
    jQuery('#hidden-input').val( '' );

  });

});
</script>