<a class="manage_image btn"><?php _e('Add or Edit Portfolio', 'ktt'); ?></a>
<form method="post" id="gallery_form">
    <input type="hidden" id="portfolio_gallery" name="portfolio_gallery" value="<?php if(get_post_meta($current_user->ID, 'portfolio_gallery')) { echo get_post_meta($current_user->ID, 'portfolio_gallery', true); } ?>">
    <?php wp_nonce_field( 'post_nonce', 'post_nonce_field' ); ?>
    <input type="hidden" name="gallery_submit" value="true">
</form>
<div class="gallery">
    <ul class="row">
        <?php $gallery_id =  EC_UserRelation::get_gallery_attachments_to_id($current_user->ID, 'portfolio_gallery');
        if(!empty($gallery_id)):
foreach($gallery_id as $gallry)
{
echo '<li class="col-md-2">';
$attchment = get_post( intval(preg_replace('/[^0-9]+/', '', $gallry), 10) );
echo '<img src="'.$attchment->guid.'" ><span>'.$attchment->post_content.'</span>';
echo '</li>';
}
        endif;
    wp_reset_query();
?>
    </ul>
</div>
