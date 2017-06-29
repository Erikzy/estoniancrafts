<br>
<form method="post" id="gallery_form">
    <?php wp_nonce_field( 'post_nonce', 'post_nonce_field' ); ?>
    <div class="row">
        <div class="col-md-8">
              <label for="postTitle"><?php _e('Gallery Title:', 'framework'); ?>
                    <?php if ( isset($errors['post_title']) ) { ?>
                    <span class="error"><?= $errors['post_title'] ?></span>
                    <div class="clearfix"></div>
                <?php } ?> 
                </label>
            <input type="hidden" name="gallery_submit" value="true">
            <input type="text" class="form-control" name="post_title" value="<?php echo $post->post_title; ?>">
            <input type="hidden" id="portfolio_gallery" name="portfolio_gallery" value="<?php echo  $post->post_content; ?>">
        </div>
        <div class="col-md-4">
            <button type="submit" name="submit"><?php _e('Save', 'ktt'); ?></button>
        </div>
    </div>
    <br>
    <div id="gallery_repeater">
    <fieldset>
        <div class="row">
            <div class="col-md-4">
                <div class="fetaute-image">
                <img src="" data-img="post_picture">
                <input type="hidden" name="post_picture[]" value="">
                <a href="#" btn-name="post_picture" data-action="add" data-btn="manage_image" class="btn">Add Image</a>
                <a href="#" btn-name="post_picture" data-action="remove" data-btn="manage_image" class="hide btn">Remove Image</a>
            </div>
            </div>
            <div class="col-md-8">
             <textarea name="post_discription[]" value=""></textarea>
            </div>
        </div>
    </fieldset>
    </div>
    <a href="#" class="add_more_images btn pull-right">
        <?php _e('Add New images', 'ktt'); ?>
    </a>
</form>
<br>

<div class="gallery">
    <ul class="row">
        <?php $gallery_id =  get_gallery_attachments_to_id($current_user->ID, 'portfolio_gallery');
if(!empty($gallery_id)):
foreach($gallery_id as $gallry)
{
echo '<li class="col-md-2">';
$attchment = get_post( intval(preg_replace('/[^0-9]+/', '', $gallry), 10) );
echo '<img src="'. wp_get_attachment_url( $attchment->ID ) .'" ><span>'.$attchment->post_content.'</span>';
echo '</li>';
}
endif;
wp_reset_query();
?>
    </ul>
</div>




<!--
<ul class="dokan-seller-wrap">


<li class="dokan-single-seller">
<div class="dokan-store-thumbnail">

<div class="dokan-store-banner-wrap">

</div>

<div class="dokan-store-caption">
<h3><a href="">fghg</a></h3>



<p><a class="dokan-btn dokan-btn-theme" href=""><?php _e( 'Visit Store', 'dokan' ); ?></a></p>

</div> 
</div> 
</li> 

</ul>

-->
