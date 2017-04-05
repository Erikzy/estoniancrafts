<?php


add_action('woocommerce_edit_account_form_start', function(){

	?>

	<p class="form-row form-row-wide">
        <label for="account_sex"><?php _e( 'Sex', 'ktt' ); ?> <span class="required">*</span></label>
        <input type="text" class="input-text" name="account_sex" id="account_sex" value="<?php echo esc_attr( $user->sex ); ?>" />
    </p>

	<p class="form-row form-row-wide">
        <label for="account_dob"><?php _e( 'Date of birth', 'ktt' ); ?> <span class="required">*</span></label>
        <input type="text" class="input-text" name="account_dob" id="account_dob" value="<?php echo esc_attr( $user->dob ); ?>" />
    </p>

	<?php

});