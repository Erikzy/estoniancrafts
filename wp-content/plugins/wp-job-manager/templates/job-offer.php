<?php

$post = get_post();
if($post->post_status === 'preview') {
    return false;
}

$path = explode(DIRECTORY_SEPARATOR, dirname( __FILE__ ));
array_pop($path);
$path[] = 'includes';
$path[] = 'class-wp-job-offer.php';
$file = implode(DIRECTORY_SEPARATOR, $path);

require_once $file;
$jobOffer = new WP_Job_Offer();

$errors = $jobOffer->validate();
$postData = $jobOffer->getPostData();
$jobOffer->createTermsPageIfNotExit();
$termsPage = $jobOffer->getTermPage();

if (!empty($postData) && count($errors) === 0) {
    $jobOffer->submit();
    $data = $jobOffer->getDefaultData();
} else {
    $data = $jobOffer->getFormData();
}

$cheapestPriceRow = $jobOffer->getCheapestPriceRow();

?>

<div class='make-offer'>

    <?php if($cheapestPriceRow): ?>
    <div class='price-info'>
        <h3><?php echo __('Offers', 'wp-job-manager'); ?></h3>
        <div class='price'>
            <span class='label2'><?php echo __('The lowest price', 'wp-job-manager'); ?>:</span>
            <span class='text'><?php echo $cheapestPriceRow->price; ?> <?php echo __('EUR', 'wp-job-manager'); ?></span>
        </div>
        <div class='name'>
            <span class='label2'><?php echo __('Bidder', 'wp-job-manager'); ?>:</span>
            <span class='text'><?php echo $cheapestPriceRow->name; ?></span>
        </div>
        <div class='count'>
            <span class='label2'><?php echo __('Total bids', 'wp-job-manager'); ?>:</span>
            <span class='text'><?php echo $cheapestPriceRow->count; ?></span>
        </div>
    </div>
    <?php endif; ?>

    <h3><?php echo __('Make offer', 'wp-job-manager'); ?></h3>

    <div class='deadline-info'>
        <div class='deadline' data-date='<?php echo get_the_job_deadline(); ?>'>
            <?php echo __('Time remaining', 'wp-job-manager'); ?>
            <span class='days'></span><?php echo __('Days', 'wp-job-manager'); ?>
            <span class='hours'></span><?php echo __('Hours', 'wp-job-manager'); ?>
            <span class='minutes'></span><?php echo __('Minutes', 'wp-job-manager'); ?>
            <span class='seconds'></span><?php echo __('Seconds', 'wp-job-manager'); ?>
        </div>
    </div>
    
    <?php
    if (count($errors) > 0) {
        foreach ($errors AS $error) {
            ?>
            <div class='alert alert-danger'><?php echo $error; ?></div>
            <?php
        }
    }
    
    if(!isset($previewMode) && wp_get_current_user()->ID > 0) {
    
    ?>

    <form method="post">

        <?php
        $formFields = $jobOffer->getFormFields();
        foreach($formFields AS $field => $fieldData) {
            $value = array_key_exists($field, $data) ? $data[$field] : '';
            $label = $fieldData['label'];
            $type = $fieldData['type'];
            $required = $fieldData['required'] ? 'required="required"' : '';
            if($type === 'hidden') {
                ?><input type='hidden' name='<?php echo $field; ?>' value='<?php echo $value; ?>' /><?php
            } else if($type === 'text') {
                ?>
                <div class='input'>
                    <label for='<?php echo $field; ?>'><?php echo $label; ?></label>
                    <input type='text' name='<?php echo $field; ?>' id='<?php echo $field; ?>' <?php echo $required; ?> value='<?php echo $value; ?>' />
                </div>
                <?php
            } else if($type === 'textarea') {
                ?>
                <div class='input'>
                    <label for='<?php echo $field; ?>'><?php echo $label; ?></label>
                    <textarea name='<?php echo $field; ?>' id='<?php echo $field; ?>'><?php echo $value; ?></textarea>
                </div>
                <?php
            } else if($type === 'checkbox') {
                ?>
                <div class='input'>
                    <label for='<?php echo $field; ?>'><?php echo $label; ?></label>
                    <input type='checkbox' name='<?php echo $field; ?>' id='job_offer_name' <?php echo $required; ?> value='1' />
                </div>
                <?php
            }
        }
        ?>
                
        <div class='input'>
            <label for='job_offers_terms'><?php echo __('Terms', 'wp-job-manager'); ?></label>
            <input type='checkbox' name='job_offers_terms' id='job_offers_terms' required="required" <?php if(array_key_exists('job_offers_terms', $data)) { ?>checked="checked"<?php } ?> value='1' />
            <a href='<?php echo $termsPage['guid']; ?>' alt='<?php echo __('Terms', 'wp-job-manager'); ?>' target="_blank">
                <?php echo __('I accept the terms', 'wp-job-manager'); ?>
            </a>
        </div>

        <div class='button'>
            <input type='submit' value="<?php echo __('Submit', 'wp-job-manager'); ?>" />
        </div>
    </form>
</div>

<?php
} else {
    echo "<a class='button' href='".wp_login_url(get_permalink())."'>" . __('Login', 'wp-job-manager') . "</a>";
}
?>