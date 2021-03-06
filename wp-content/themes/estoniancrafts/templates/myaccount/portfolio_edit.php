<style>
    .vertical-alignment-helper {
        display:table;
        height: 100%;
        width: 100%;
        pointer-events:none;
    }
    .vertical-align-center {
        /* To center vertically */
        display: table-cell;
        vertical-align: middle;
        pointer-events:none;
    }
    .modal-content {
        /* Bootstrap sets the size of the modal in the modal-dialog class, we need to inherit it */
        width:inherit;
        height:inherit;
        /* To center horizontally */
        margin: 0 auto;
        pointer-events:all;
    }
    .modal-backdrop {
        display: none;
    }
</style>
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
            <input type="text" class="form-control" name="post_title" value="<?php echo $portfolio->post_title; ?>">
        </div>
        <div class="col-md-4">
            <button type="submit" name="submit"><?php _e('Save', 'ktt'); ?></button>
        </div>
    </div>
    <br/>
    <div id="gallery_repeater">
        <?php
        $nextPictureId = 1;
        if (count($pictures)): 
            foreach ( $pictures as $key => $picture ):
        ?>  
                <fieldset>
                    <div class="row">
                        <div class="col-md-4">
                            <img class="img-portfolio" src="<?= $picture['url'] ?>" data-toggle="modal" data-target="#myModal_<?= $key ?>">
                            <input class="picture" type="hidden" name="pictures[<?= $nextPictureId ?>][picture]" value="<?= $picture['picture'] ?>">

                            <button class="btn portfolio_add_image">Add Image</button>
                            <button class="btn portfolio_remove_image">Remove Image</a>
                        </div>
                        <div class="col-md-8">
                            <textarea name="pictures[<?= $nextPictureId ?>][description]"><?= $picture['description'] ?></textarea>
                        </div>
                    </div>
                    <!-- Modal -->
                    <div id="myModal_<?= $key ?>" class="modal fade" role="dialog">
                        <!-- Modal content-->
                        <div class="vertical-alignment-helper">
                            <div class="modal-dialog vertical-align-center">
                                <div class="modal-content text-center">
                                    <img class="modal-image" src="<?= $picture['url'] ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>



            <?php 
                ++$nextPictureId;
            endforeach; ?> 
        <?php else: ?>
            <fieldset>
                <div class="row">
                    <div class="col-md-4">
                        <img src="" data-img="post_picture">
                        <input class="picture" type="hidden" name="pictures[0][picture]" value="">

                        <button class="btn portfolio_add_image">Add Image</button>
                        <button class="hide btn portfolio_remove_image">Remove Image</a>
                    </div>
                    <div class="col-md-8">
                        <textarea name="pictures[0][description]"></textarea>
                    </div>
                </div>
            </fieldset>
        <?php endif; ?>
    </div>
    <a href="#" class="add_more_images btn pull-right">
        <?php _e('Add New images', 'ktt'); ?>
    </a>
</form>
<br>

<script>var next_picture_id=<?= (int)$nextPictureId ?>;</script>
<?php /*
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
<?php //*/ ?>




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
