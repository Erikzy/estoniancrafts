<?php
/**
 *  Dokan Dashboard Template
 *
 *  Dokan Main Dahsboard template for Front-end
 *
 *  @since 2.5
 *
 *  @package dokan
 */

$user = get_user_by( 'id', get_current_user_id() );
?>
<div class="dokan-dashboard-wrap">

    <?php
        /**
         *  dokan_dashboard_content_before hook
         *
         *  @hooked get_dashboard_side_navigation
         *
         *  @since 2.4
         */
        do_action( 'dokan_dashboard_content_before' );
    ?>

    <div class="dokan-dashboard-content">

        <?php
            /**
             *  dokan_dashboard_content_before hook
             *
             *  @hooked show_seller_dashboard_notice
             *
             *  @since 2.4
             */
            do_action( 'dokan_dashboard_content_inside_before' );
        ?>

            <?php

                $ext_profile = get_user_meta( $user->ID, 'ktt_extended_profile', true );
    
                $organizations  = isset( $ext_profile['org'] ) ? $ext_profile['org'] : [['name' => '', 'link' => '', 'start' => '', 'end' => '']];
                $experience  = isset( $ext_profile['work_exp'] ) ? $ext_profile['work_exp'] : [['name' => '', 'field' => '', 'start' => '', 'end' => '']];
                $certificates  = isset( $ext_profile['certificates'] ) ? $ext_profile['certificates'] : [['name' => '', 'auth' => '', 'start' => '', 'end' => '', 'link' => '', 'file' => '']];


            ?>

            <?php lbDokan::get_instance()->user->display_user_profile_completeness($user->ID) ?>

            <?php lbPdf::display_bcard_links(); ?>

            <article class="dashboard-content-area woocommerce edit-account-wrap">

                <?php wc_print_notices();?>

                <h1 class="entry-title"><?php _e( 'Edit Account Details', 'dokan' ); ?></h1>

                <form class="edit-account" action="" method="post">

                    <?php do_action( 'woocommerce_edit_account_form_start' ); 
                    
                        $user_avatar = get_user_meta( $user->ID, 'dokan_profile_settings', true );
                        $user->gravatar = isset( $user_avatar['gravatar'] ) ? $user_avatar['gravatar'] : 0;

                    ?>

                    <?php $gravatar  = isset( $user->gravatar ) ? absint( $user->gravatar ) : 0; ?>
                    <div class="dokan-form-group">
                        <label class="dokan-w3 dokan-control-label" for="dokan_gravatar"><?php _e( 'Profile Picture', 'dokan' ); ?></label>

                        <div class="dokan-w5 dokan-gravatar">
                            <div class="dokan-left gravatar-wrap<?php echo $gravatar ? '' : ' dokan-hide'; ?>">
                                <?php $gravatar_url = $gravatar ? wp_get_attachment_url( $gravatar ) : ''; ?>
                                <input type="hidden" class="dokan-file-field" value="<?php echo $gravatar; ?>" name="dokan_gravatar">
                                <img class="dokan-gravatar-img" src="<?php echo esc_url( $gravatar_url ); ?>">
                                <a class="dokan-close dokan-remove-gravatar-image">&times;</a>
                            </div>
                            <div class="gravatar-button-area<?php echo $gravatar ? ' dokan-hide' : ''; ?>">
                                <a href="#" class="dokan-pro-gravatar-drag dokan-btn dokan-btn-default button medium-orange-button"><!-- <i class="fa fa-cloud-upload"></i>  --><?php _e( 'Upload Photo', 'dokan' ); ?></a>
                            </div>
                        </div>
                    </div>

                    <p class="form-row form-row-first">
                        <label for="account_first_name"><?php _e( 'First name', 'dokan' ); ?> <span class="required">*</span></label>
                        <input type="text" class="input-text" name="account_first_name" id="account_first_name" value="<?php echo esc_attr( $user->first_name ); ?>" />
                    </p>

                    <p class="form-row form-row-last">
                        <label for="account_last_name"><?php _e( 'Last name', 'dokan' ); ?> <span class="required">*</span></label>
                        <input type="text" class="input-text" name="account_last_name" id="account_last_name" value="<?php echo esc_attr( $user->last_name ); ?>" />
                    </p>
                    <div class="clear"></div>

                    <p class="form-row form-row-wide">
                        <label for="account_email"><?php _e( 'Email address', 'dokan' ); ?> <span class="required">*</span></label>
                        <input type="email" class="input-text" name="account_email" id="account_email" value="<?php echo esc_attr( $user->user_email ); ?>" />
                    </p>

                    <p class="form-row form-row-first">
                        <label for="account_mobile"><?php _e( 'Phone', 'ktt' ); ?></label>
                        <input type="text" class="input-text" name="account_mobile" id="account_mobile" value="<?php echo esc_attr( $ext_profile['mobile'] ); ?>" />
                    </p>

                    <p class="form-row form-row-last">
                        <label for="account_skype"><?php _e( 'Skype', 'ktt' ); ?></label>
                        <input type="text" class="input-text" name="account_skype" id="account_skype" value="<?php echo esc_attr( $ext_profile['skype'] ); ?>" />
                    </p>
                    <div class="clear"></div>

                    <p class="form-row form-row-wide">
                        <label for="account_description"><?php _e( 'Description', 'ktt' ); ?></label>
                        <textarea rows="4" class="input-text" name="account_description" id="account_description"><?php echo esc_attr( $ext_profile['description'] ); ?></textarea>
                    </p>

                    <p class="form-row form-row-first">
                        <label for="account_gender"><?php _e( 'Gender', 'ktt' ); ?> </label>
                        <select name="account_gender" id="account_gender">
                            <option value="none"><?php _e( ' - Select gender - ', 'ktt' ); ?></option>
                            <option value="male" <?= ($ext_profile['gender'] == 'male')? 'selected' : '' ?>><?php _e( 'Male', 'ktt' ); ?></option>
                            <option value="female" <?= ($ext_profile['gender'] == 'female')? 'selected' : '' ?>><?php _e( 'Female', 'ktt' ); ?></option>
                        </select>
                        
                    </p>

                    <p class="form-row form-row-last">
                        <label for="account_dob"><?php _e( 'Date of birth', 'ktt' ); ?></label>

                        <input type="text" pattern="(0[1-9]|1[0-9]|2[0-9]|3[01]).(0[1-9]|1[012]).[0-9]{4}" value="<?php echo esc_attr( $ext_profile['dob'] ); ?>" name="account_dob" id="account_dob" list="dates_pattern0_datalist" placeholder="dd.mm.yyyy">
                    </p>
                    <div class="clear"></div>

                    <p class="form-row form-row-first">
                        <label for="account_workyears"><?php _e( 'Working experience (years)', 'ktt' ); ?></label>
                        <input type="number" class="input-text" name="account_workyears" id="account_workyears" value="<?php echo esc_attr( $ext_profile['workyears'] ); ?>" />
                    </p>

                    <p class="form-row form-row-last">
                        <label for="account_video"><?php _e( 'YouTube video link', 'ktt' ); ?> <span class="required">*</span></label>
                        <input type="text" class="input-text" name="account_video" id="account_video" value="<?php echo esc_attr( $ext_profile['video'] ); ?>" />
                    </p>
                    <div class="clear"></div>

                    <p class="form-row form-row-wide">
                        <label for="account_articles_links"><?php _e( 'Articles linking (separate links with space)', 'ktt' ); ?></label>
                        <textarea rows="4" class="input-text" name="account_articles_links" id="account_articles_links"><?php echo esc_attr( $ext_profile['articles_links'] ); ?></textarea>
                    </p>


                    <fieldset>
                        <legend><?php _e( 'Education', 'ktt' ); ?></legend>

                        <p class="form-row form-row-first">
                            <label for="account_education"><?php _e( 'Education', 'ktt' ); ?></label>
                            <select name="account_education" id="account_education">
                                <option value="none"><?php _e( ' - Select your education - ', 'ktt' ); ?></option>
                                <option value="1" <?= ($ext_profile['education'] == '1')? 'selected' : '' ?>><?php _e( 'Basic education', 'ktt' ); ?></option>
                                <option value="2" <?= ($ext_profile['education'] == '2')? 'selected' : '' ?>><?php _e( 'Secondary education', 'ktt' ); ?></option>
                                <option value="3" <?= ($ext_profile['education'] == '3')? 'selected' : '' ?>><?php _e( 'Vocational education', 'ktt' ); ?></option>
                                <option value="4" <?= ($ext_profile['education'] == '4')? 'selected' : '' ?>><?php _e( 'Higher education', 'ktt' ); ?></option>
                            </select>
                        </p>

                        <p class="form-row form-row-last">
                            <label for="account_education_school"><?php _e( 'School name', 'ktt' ); ?></label>

                            <input type="text" class="input-text" name="account_education_school" id="account_education_school" value="<?php echo esc_attr( $ext_profile['education_school'] ); ?>" />
                        </p>
                        <div class="clear"></div>

                        <p class="form-row form-row-first">
                            <label for="account_education_start"><?php _e( 'Start date', 'ktt' ); ?></label>

                            <input type="text" class="input-text" name="account_education_start" id="account_education_start" value="<?php echo esc_attr( $ext_profile['education_start'] ); ?>" placeholder="mm.yyyy" />
                        </p>

                        <p class="form-row form-row-last">
                            <label for="account_education_end"><?php _e( 'End date', 'ktt' ); ?></label>

                            <input type="text" class="input-text" name="account_education_end" id="account_education_end" value="<?php echo esc_attr( $ext_profile['education_end'] ); ?>" placeholder="mm.yyyy" />
                        </p>
                        <div class="clear"></div>

                    </fieldset>

                    <fieldset>
                        <legend><?php _e( 'Work experience', 'ktt' ); ?></legend>

                        <div class="lb-elastic-container">
                            <div class="lb-elastic-elements">

                                <?php
                                    $i = 0;
                                    foreach( $experience as $exp){
                                ?>
                                    <div class="lb-elastic-element lb-input-margins">

                                        <p class="form-row form-row-first">
                                            <label for="account_work_exp_name"><?php _e( 'Company name', 'ktt' ); ?></label>
                                            <input type="text" class="input-text" name="account_work_exp_name[]" id="account_work_exp_name" value="<?php echo esc_attr( $exp['name'] ); ?>" />
                                            
                                        </p>

                                        <p class="form-row form-row-last">
                                            <label for="account_work_exp_field"><?php _e( 'Work field', 'ktt' ); ?></label>

                                            <input type="text" class="input-text" name="account_work_exp_field[]" id="account_work_exp_field" value="<?php echo esc_attr( $exp['field'] ); ?>" />
                                        </p>
                                        <div class="clear"></div>

                                        <p class="form-row form-row-first">
                                            <label for="account_work_exp_start"><?php _e( 'Start date', 'ktt' ); ?></label>

                                            <input type="text" class="input-text" name="account_work_exp_start[]" id="account_work_exp_start" value="<?php echo esc_attr( $exp['start'] ); ?>" placeholder="mm.yyyy" />
                                        </p>

                                        <p class="form-row form-row-last">
                                            <label for="account_work_exp_end"><?php _e( 'End date', 'ktt' ); ?></label>

                                            <input type="text" class="input-text" name="account_work_exp_end[]" id="account_work_exp_end" value="<?php echo esc_attr( $exp['end'] ); ?>" placeholder="mm.yyyy" />
                                        </p>
                                        <div class="clear"></div>
                                        <hr>

                                    </div>
                               
                                <?php $i++; } ?>
                        
                            </div>
                            <a href="#lb-add-more" class="lb-elastic-add"> + add more...</a>
                        </div>
                        
                    </fieldset>

                    <fieldset>
                        <legend><?php _e( 'Organization', 'ktt' ); ?></legend>

                        <div class="lb-elastic-container">
                            <div class="lb-elastic-elements">

                                <?php
                                    $i = 0;
                                    foreach( $organizations as $org){
                                ?>
                                    <div class="lb-elastic-element lb-input-margins">

                                        <p class="form-row form-row-first">
                                            <label for="account_org_name"><?php _e( 'Organization name', 'ktt' ); ?></label>
                                            <input type="text" class="input-text" name="account_org_name[]" id="account_org_name" value="<?php echo esc_attr( $org['name'] ); ?>" />
                                            
                                        </p>

                                        <p class="form-row form-row-last">
                                            <label for="account_org_link"><?php _e( 'Link', 'ktt' ); ?></label>

                                            <input type="text" class="input-text" name="account_org_link[]" id="account_org_link" value="<?php echo esc_attr( $org['link'] ); ?>" />
                                        </p>
                                        <div class="clear"></div>

                                        <p class="form-row form-row-first">
                                            <label for="account_org_start"><?php _e( 'Start date', 'ktt' ); ?></label>

                                            <input type="text" class="input-text" name="account_org_start[]" id="account_org_start" value="<?php echo esc_attr( $org['start'] ); ?>" placeholder="mm.yyyy" />
                                        </p>

                                        <p class="form-row form-row-last">
                                            <label for="account_org_end"><?php _e( 'End date', 'ktt' ); ?></label>

                                            <input type="text" class="input-text" name="account_org_end[]" id="account_org_end" value="<?php echo esc_attr( $org['end'] ); ?>" placeholder="mm.yyyy" />
                                        </p>
                                        <div class="clear"></div>
                                        <hr>

                                    </div>
                               
                                <?php $i++; } ?>
                        
                            </div>
                            <a href="#lb-add-more" class="lb-elastic-add"> + add more...</a>
                        </div>
                        
                    </fieldset>

                    <fieldset>
                        <legend><?php _e( 'Certificates', 'ktt' ); ?></legend>

                        <div class="lb-elastic-container">
                            <div class="lb-elastic-elements">

                                <?php
                                    $i = 0;
                                    foreach( $certificates as $cert){
                                ?>
                                    <div class="lb-elastic-element lb-input-margins">

                                        <p class="form-row form-row-first">
                                            <label for="account_cert_name"><?php _e( 'Certification name', 'ktt' ); ?></label>
                                            <input type="text" class="input-text" name="account_cert_name[]" id="account_cert_name" value="<?php echo esc_attr( $cert['name'] ); ?>" />
                                            
                                        </p>

                                        <p class="form-row form-row-last">
                                            <label for="account_cert_auth"><?php _e( 'Instructor / certificate authority', 'ktt' ); ?></label>

                                            <input type="text" class="input-text" name="account_cert_auth[]" id="account_cert_auth" value="<?php echo esc_attr( $cert['auth'] ); ?>" />
                                        </p>
                                        <div class="clear"></div>

                                        <p class="form-row form-row-first">
                                            <label for="account_cert_start"><?php _e( 'Start date', 'ktt' ); ?></label>

                                            <input type="text" class="input-text" name="account_cert_start[]" id="account_cert_start" value="<?php echo esc_attr( $cert['start'] ); ?>" placeholder="mm.yyyy" />
                                        </p>

                                        <p class="form-row form-row-last">
                                            <label for="account_cert_end"><?php _e( 'End date', 'ktt' ); ?></label>

                                            <input type="text" class="input-text" name="account_cert_end[]" id="account_cert_end" value="<?php echo esc_attr( $cert['end'] ); ?>" placeholder="mm.yyyy" />
                                        </p>
                                        <div class="clear"></div>

                                        <p class="form-row form-row-first">
                                            <label for="account_cert_link"><?php _e( 'Link', 'ktt' ); ?></label>

                                            <input type="text" class="input-text" name="account_cert_link[]" id="account_cert_link" value="<?php echo esc_attr( $cert['link'] ); ?>" />
                                        </p>

                                        <p class="form-row form-row-last">
                                            <label for="account_cert_file"><?php _e( 'File', 'ktt' ); ?></label>

                                            <input type="hidden" class="input-text" name="account_cert_file[]" id="account_cert_file" value="<?= (int)$cert['file']; ?>" />

                                            <a href="#remove" class="lb-file-placeholder <?php if( (int)$cert['file'] != 0){ echo 'active'; } ?>"></a>
                                            <a href="#add-file" class="lb-add-doc <?php if( (int)$cert['file'] == 0){ echo 'active'; } ?>"> + <?php _e( 'Add document', 'ktt' ); ?></a>
                                            <a href="#add-file" class="lb-remove-doc <?php if( (int)$cert['file'] != 0){ echo 'active'; } ?>"> + <?php _e( 'Remove document', 'ktt' ); ?></a>
                                        </p>
                                        <div class="clear"></div>
                                        <hr>

                                    </div>
                               
                                <?php $i++; } ?>
                        
                            </div>
                            <a href="#lb-add-more" class="lb-elastic-add"> + add more...</a>
                        </div>
                        
                    </fieldset>

                    <fieldset>
                        <legend><?php _e( 'Address', 'ktt' ); ?></legend>

                        <p class="form-row form-row-first">
                            <label for="account_location_country"><?php _e( 'Country', 'ktt' ); ?></label>

                            <?= lb_display_country_select($ext_profile['country']) ?>
                        </p>

                        <p class="form-row form-row-last">
                            <label for="account_location_state"><?php _e( 'State', 'ktt' ); ?> </label>
                            <input type="text" class="input-text" name="account_location_state" id="account_location_state" value="<?php echo esc_attr( $ext_profile['state'] ); ?>" />
                        </p>
                        <div class="clear"></div>

                        <p class="form-row form-row-first">
                            <label for="account_location_city"><?php _e( 'City', 'ktt' ); ?> </label>
                            <input type="text" class="input-text" name="account_location_city" id="account_location_city" value="<?php echo esc_attr( $ext_profile['city'] ); ?>" />
                        </p>

                        <p class="form-row form-row-last">
                            <label for="account_location_address"><?php _e( 'Address', 'ktt' ); ?> </label>
                            <input type="text" class="input-text" name="account_location_address" id="account_location_address" value="<?php echo esc_attr( $ext_profile['address'] ); ?>" />
                        </p>

                    </fieldset>

                    <fieldset style="position:relative;">
                        <legend><?php _e( 'Password Change', 'dokan' ); ?></legend>

                        <p class="form-row form-row-wide">
                            <label for="password_current"><?php _e( 'Current Password (leave blank to leave unchanged)', 'dokan' ); ?></label>
                            <input type="password" class="input-text" name="password_current" id="password_current" value="" />
                        </p>

                        <p class="form-row form-row-wide">
                            <label for="password_1"><?php _e( 'New Password (leave blank to leave unchanged)', 'dokan' ); ?></label>
                            <input onchange=triggerPasswordCheck()  oninput=triggerPasswordCheck() type="password" class="input-text" name="password_1" id="password_1" />
			    <div id="ec-wc-pass-hint" class="ec-woocommerce-password-hint">The password must be at least 6 characters long and contain both letters and numbers.</div>	               			
		         </p>
			<script type="text/javascript">
				function triggerPasswordCheck(){
					var pass = jQuery("#password_1").val();
					
					str = ec_calculatestrength(pass);
					
					console.log(str);	
					if(str.score < 3 && pass.length > 0){
					 jQuery('#ec-wc-pass-hint').show();
					}else{
					 jQuery('#ec-wc-pass-hint').hide();
					
					}	
					

				}

	
			</script>
			<style>
#ec-wc-pass-hint{
	display:none;
}
.ec-woocommerce-password-hint:after{

    content: "\\f0d7";
    position: absolute;
    top: 100%;
    left: 30px;
    font-size: 26px;
    line-height: 7px;
    text-shadow: 0 2px 3px rgba(0,0,0,.1);
    color: #fff;
    font-family: FontAwesome;
}

.ec-woocommerce-password-hint{
      position: absolute;
    top: 60px;
    right: 0;
    /* opacity: 0; */
    /* visibility: hidden; */
    margin-bottom: -10px;
    background: #fff;
    box-shadow: 0 0 4px rgba(0,0,0,.15);
    padding: 20px;
    max-width: 300px;
    width: 100%;
    transition: opacity .5s,visibility .5s;
    -webkit-transition: opacity .5s,visibility .5s;
    animation: fade-in .5s;
    -webkit-animation: fade-in .5s;
}

</style>
                        <p class="form-row form-row-wide">
                            <label for="password_2"><?php _e( 'Confirm New Password', 'dokan' ); ?></label>
                            <input type="password" class="input-text" name="password_2" id="password_2" />
                        </p>
                    </fieldset>

                    <div class="clear"></div>

                    <?php do_action( 'woocommerce_edit_account_form' ); ?>

                    <p>
                        <?php wp_nonce_field( 'dokan_save_account_details' ); ?>

                        <button name="dokan_save_account_details" class="dokan-btn dokan-btn-theme dokan-btn-lg btn-block medium-orange-button" data-balloon-length="medium" data-balloon="<?php _e( 'Make sure you didn\'t make any spelling mistakes. This data will be sent to our translators shortly.', 'ktt' ); ?>" data-balloon-pos="up" ><?php esc_attr_e( 'Save changes', 'dokan' ); ?></button>
                        <input type="hidden" name="action" value="dokan_save_account_details" />
                    </p>

                    <?php do_action( 'woocommerce_edit_account_form_end' ); ?>

                </form>

            </article><!-- .dashboard-content-area -->

         <?php

            /**
             *  dokan_dashboard_content_inside_after hook
             *
             *  @since 2.4
             */
            do_action( 'dokan_dashboard_content_inside_after' );
        ?>


    </div><!-- .dokan-dashboard-content -->

    <?php

        /**
         *  dokan_dashboard_content_after hook
         *
         *  @since 2.4
         */
        do_action( 'dokan_dashboard_content_after' );
    ?>

</div><!-- .dokan-dashboard-wrap -->
