
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
                <label class="publish_notify hide"><?php _e('Carefully look through the post. The post will go to translation', 'ktt'); ?></label>
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