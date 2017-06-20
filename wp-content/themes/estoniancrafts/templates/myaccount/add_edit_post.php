
    <form action="" id="primaryPostForm" method="POST">
        <div class="row">
            <div class="col-md-8">
                <br>
                <?php if ( $postTitleError != '' ) { ?>
                    <span class="error"><?php echo $postTitleError; ?></span>
                    <div class="clearfix"></div>
                <?php } ?> 
                <br>
                <label for="postTitle"><?php _e('Post Title:', 'framework'); ?></label>
                <input type="text" name="postTitle" id="postTitle" class="" value="<?php if ( isset( $_POST['postTitle'] ) )  echo $_POST['postTitle']; elseif( $post_title ) echo $post_title; ?>"/>
                <br>
                <br>    
                <label for="postContent"><?php  _e('Post Content:', 'framework'); ?></label>
                <?php wp_editor(html_entity_decode($post_content),  'postContent', array('editor_height' => 150,  'media_buttons' => true, 'editor_class' => '') ); ?>
                <br>
                <input type="hidden" name="submitted" id="submitted" value="true" />
                <input type="hidden" name="post_status" value="<?php if($post_status) echo $post_status; ?>" />
                <label class="publish_notify hide"><?php _e('Carefully look through the post. The post will go to translation', 'ktt'); ?></label>
            </div>
            <div class="col-md-4">
                <br>
                <div class="fetaute-image">
                    <img src="<?php if( $post_thumbnail_url ) echo $post_thumbnail_url[0]; ?>" data-img="post_picture">
                    <input type="hidden" name="post_picture" value="<?php if( $post_thumbnail_id ) echo $post_thumbnail_id; ?>">
                    <a href="#" btn-name="post_picture" data-action="add" data-btn="manage_image"  class=" <?php if( $post_thumbnail_id ) echo 'hide'; ?>">Add Image</a>
                    <a href="#" btn-name="post_picture" data-action="remove"  data-btn="manage_image" class="<?php if( !$post_thumbnail_id ) echo 'hide'; ?>">Remove Image</a>
                    
                </div>
            </div>
        </div>
        <?php   wp_nonce_field( 'post_nonce', 'post_nonce_field' ); ?>
        <?php if ( $post_status!='pending' && $post_status!='publish' ) : ?>
        <button type="submit" class="btn" data-action="pending" data-btn="submit"><?php  _e('Publish', 'ktt'); ?></button>
        <button type="submit" class="btn" data-action="draft" data-btn="submit"><?php  _e('Draft', 'ktt'); ?></button>
        <button type="submit" class="btn" data-action="trash" data-btn="submit"><?php  _e('Delete', 'ktt'); ?></button>
        <?php endif; ?>
    </form>