
    <form action="" id="edit-blog-post-form" method="POST">
        <?php   wp_nonce_field( 'blog_post_token', 'blog_post_token' ); ?>
        <p class="row">
            <div class="col-md-8">
                <div class="fetaute-image">
                    <img src="<?php if( $post_thumbnail_url ) echo $post_thumbnail_url[0]; ?>" data-img="post_picture">
                    <input type="hidden" name="post_picture" value="<?php if( $post_thumbnail_id ) echo $post_thumbnail_id; ?>">
                    <a href="#" btn-name="post_picture" data-action="add" data-btn="manage_image"  class=" <?php if( $post_thumbnail_id ) echo 'hide'; ?>">Add Image</a>
                    <a href="#" btn-name="post_picture" data-action="remove"  data-btn="manage_image" class="<?php if( !$post_thumbnail_id ) echo 'hide'; ?>">Remove Image</a>   
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
                <input type="submit" class="btn" name="edit_action[delete]" value="<?php  _e('Delete', 'ktt'); ?>" />
                <input type="submit" class="btn" name="edit_action[draft]" value="<?php  _e('Draft', 'ktt'); ?>" />
                <input type="submit" class="btn btn-color-primary" name="edit_action[publish]" value="<?php  _e('Publish', 'ktt'); ?>" />
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
                    <a href="#" class="btn close-popup  before-publish" data-value="cancle">Review</a>
                    <a href="#" class="btn btn-color-primary view-cart before-publish" data-value="publish">Publish</a>
                </div>
                <button  type="button" class=" mfp-close  before-publish" data-value="cancle">×</button>
            </div>
        </div>
    </div>
</div>