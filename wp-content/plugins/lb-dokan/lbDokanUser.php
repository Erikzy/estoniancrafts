<?php
/**
 * lbDokan user related extended fields and functionality
 */
class lbDokanUser {

	function __construct(){

        add_action( 'user_register', [$this, 'user_register']);
        add_action( 'dokan_store_profile_saved', [$this, 'save_shop'] );
        add_action( 'woocommerce_save_account_details', [$this, 'save_user_details'] );

        add_action( 'woocommerce_edit_account_form', [$this, 'woocommerce_user_form'] );
        add_action( 'ec_save_account_details', [$this, 'save_user_details'] );

   		add_action( 'ec_user_register', [$this, 'user_register']);
     

        add_action( 'show_user_profile', [$this, 'admin_user_profile_fields'], 22 );
        add_action( 'edit_user_profile', [$this, 'admin_user_profile_fields'], 22 );

        add_action( 'personal_options_update', [$this, 'admin_user_save'] );
        add_action( 'edit_user_profile_update', [$this, 'admin_user_save'] );

    }

    function user_register($user_id){

        // Just create the empty user_meta tags to avoid errors
        $ext_settings = [];
        $ext_settings['company_name'] = '';
        $ext_settings['company_nr'] = '';
        $ext_settings['company_type'] = '';
        $ext_settings['description'] = '';
        $ext_settings['media'] = [''];
        $ext_settings['address'] = [['country' => false, 'state' => '', 'city' => '', 'address' => '', 'email' => '', 'phone' => '']];

        update_user_meta( $user_id, 'ktt_extended_settings', $ext_settings );

        $ext_profile = [];
        $ext_profile['mobile'] = '';
        $ext_profile['skype'] = '';
        $ext_profile['gender'] = '';
        $ext_profile['dob'] = '';
        $ext_profile['workyears'] = '';
        $ext_profile['video'] = '';
        $ext_profile['articles_links'] = '';
        $ext_profile['description'] = '';
        $ext_profile['education'] = '';
        $ext_profile['education_school'] = '';
        $ext_profile['education_start'] = '';
        $ext_profile['education_end'] = '';
        $ext_profile['country'] = '';
        $ext_profile['state'] = '';
        $ext_profile['city'] = '';
        $ext_profile['address'] = '';
        $ext_profile['org'] = [['name' => '', 'link' => '', 'start' => '', 'end' => '']];
        $ext_profile['work_exp'] = [['name' => '', 'field' => '', 'start' => '', 'end' => '']];
        $ext_profile['certificates'] = [['name' => '', 'auth' => '', 'start' => '', 'end' => '', 'link' => '', 'file' => '']];

        update_user_meta( $user_id, 'ktt_extended_profile', $ext_profile );

    }
	
	function save_shop($store_id){

		$ext_settings = get_user_meta( $store_id, 'ktt_extended_settings', true );

    	$ext_settings['company_name'] = ! empty( $_POST['dokan_company_name'] ) ? wc_clean( $_POST['dokan_company_name'] ) : '';
    	$ext_settings['company_nr'] = ! empty( $_POST['dokan_company_nr'] ) ? wc_clean( $_POST['dokan_company_nr'] ) : '';
    	$ext_settings['company_type'] = ! empty( $_POST['dokan_company_type'] ) ? wc_clean( $_POST['dokan_company_type'] ) : '';
    	$ext_settings['description'] = ! empty( $_POST['dokan_description'] ) ? wc_clean( $_POST['dokan_description'] ) : '';

    	if( ! empty( $_POST['dokan_media'] ) ){

    		$media = $_POST['dokan_media'];

    		// Remove all empty strings first
    		$media = array_diff($media, array('http://', 'https://', ''));

	    	// Make sure all media liks have http:// or https:// in front of them
	    	$media = array_map(function($element) {
			        return (strpos($element, 'http://') !== 0 && strpos($element, 'https://') !== 0)? 'http://'.$element : $element;
			    },
			    $media
			);

			if(!count($media)){ $media = ['']; }
    		
    		$ext_settings['media'] = wc_clean( $media );
    	}

    	if( ! empty( $_POST['dokan_address'] ) ){

    		$addresses = [];

    		foreach ($_POST['dokan_address'] as $address) {
    			
    			$address_data_entered = array_diff($address, array('', ' '));
    			if( count($address_data_entered) ) { 
    				$addresses[] = $address; 
    			}

    		}

    		if(!count($addresses)){ 
    			$addresses = [['country' => false, 'state' => '', 'city' => '', 'address' => '', 'email' => '', 'phone' => '']]; 
    		}

    		$ext_settings['address'] = wc_clean( array_values($addresses) );

    	}

    	$ext_settings['ec_store_phone'] = ! empty( $_POST['ec_store_phone'] ) ? wc_clean( $_POST['ec_store_phone'] ) : '';
    	$ext_settings['ec_store_email'] = ! empty( $_POST['ec_store_email'] ) ? wc_clean( $_POST['ec_store_email'] ) : '';
    	$ext_settings['ec_store_website'] = ! empty( $_POST['ec_store_website'] ) ? wc_clean( $_POST['ec_store_website'] ) : '';
		$ext_settings['ec_store_logo'] = isset( $_POST['ec_store_logo'] ) ? wc_clean( $_POST['ec_store_logo'] ) : '';

		// Store social media
    	$ext_settings['ec_store_sm_fb'] = ! empty( $_POST['ec_store_sm_fb'] ) ? wc_clean( $_POST['ec_store_sm_fb'] ) : '';
    	$ext_settings['ec_store_sm_gplus'] = ! empty( $_POST['ec_store_sm_gplus'] ) ? wc_clean( $_POST['ec_store_sm_gplus'] ) : '';
    	$ext_settings['ec_store_sm_twitter'] = ! empty( $_POST['ec_store_sm_twitter'] ) ? wc_clean( $_POST['ec_store_sm_twitter'] ) : '';
    	$ext_settings['ec_store_sm_linkedin'] = ! empty( $_POST['ec_store_sm_linkedin'] ) ? wc_clean( $_POST['ec_store_sm_linkedin'] ) : '';
    	$ext_settings['ec_store_sm_youtube'] = ! empty( $_POST['ec_store_sm_youtube'] ) ? wc_clean( $_POST['ec_store_sm_youtube'] ) : '';
    	$ext_settings['ec_store_sm_instagram'] = ! empty( $_POST['ec_store_sm_instagram'] ) ? wc_clean( $_POST['ec_store_sm_instagram'] ) : '';
    	$ext_settings['ec_store_sm_flickr'] = ! empty( $_POST['ec_store_sm_flickr'] ) ? wc_clean( $_POST['ec_store_sm_flickr'] ) : '';

		update_user_meta( $store_id, 'ktt_extended_settings', $ext_settings );

	}


	function save_user_details( $user_ID ){

        if( isset($_POST['dokan_gravatar']) ){
    		$user_avatar = get_user_meta( $user_ID, 'dokan_profile_settings', true );
    	   
     	    $user_avatar['gravatar'] = $_POST['dokan_gravatar'];
    	    update_user_meta( $user_ID, 'dokan_profile_settings', $user_avatar );
        }
	 	$ext_profile = get_user_meta( $user_ID, 'ktt_extended_profile', true );

    	$ext_profile['mobile'] = ! empty( $_POST['account_mobile'] ) ? wc_clean( $_POST['account_mobile'] ) : '';
    	$ext_profile['skype'] = ! empty( $_POST['account_skype'] ) ? wc_clean( $_POST['account_skype'] ) : '';
    	$ext_profile['gender'] = ! empty( $_POST['account_gender'] ) ? wc_clean( $_POST['account_gender'] ) : '';
    	$ext_profile['dob'] = ! empty( $_POST['account_dob'] ) ? wc_clean( $_POST['account_dob'] ) : '';
    	$ext_profile['workyears'] = ! empty( $_POST['account_workyears'] ) ? wc_clean( $_POST['account_workyears'] ) : '';
    	$ext_profile['video'] = ! empty( $_POST['account_video'] ) ? wc_clean( $_POST['account_video'] ) : '';
    	$ext_profile['articles_links'] = ! empty( $_POST['account_articles_links'] ) ? wc_clean( $_POST['account_articles_links'] ) : '';
    	$ext_profile['description'] = ! empty( $_POST['account_description'] ) ? wc_clean( $_POST['account_description'] ) : '';
    	$ext_profile['education'] = ! empty( $_POST['account_education'] ) ? wc_clean( $_POST['account_education'] ) : '';
    	$ext_profile['education_school'] = ! empty( $_POST['account_education_school'] ) ? wc_clean( $_POST['account_education_school'] ) : '';
    	$ext_profile['education_start'] = ! empty( $_POST['account_education_start'] ) ? wc_clean( $_POST['account_education_start'] ) : '';
    	$ext_profile['education_end'] = ! empty( $_POST['account_education_end'] ) ? wc_clean( $_POST['account_education_end'] ) : '';
    	$ext_profile['country'] = ! empty( $_POST['account_location_country'] ) ? wc_clean( $_POST['account_location_country'] ) : '';
    	$ext_profile['state'] = ! empty( $_POST['account_location_state'] ) ? wc_clean( $_POST['account_location_state'] ) : '';
    	$ext_profile['city'] = ! empty( $_POST['account_location_city'] ) ? wc_clean( $_POST['account_location_city'] ) : '';
    	$ext_profile['address'] = ! empty( $_POST['account_location_address'] ) ? wc_clean( $_POST['account_location_address'] ) : '';

    	if( ! empty( $_POST['account_org_name'] ) ){

    		$orgs = [];

    		foreach ($_POST['account_org_name'] as $index => $org) {
    			
    			$org = ['name' => $org, 'link' => $_POST['account_org_link'][$index], 'start' => $_POST['account_org_start'][$index], 'end' => $_POST['account_org_end'][$index]];

    			$data_entered = array_diff($org, array('', ' '));
    			if( count($data_entered) ) { 
    				$orgs[] = $org; 
    			}

    		}

    		if(!count($orgs)){ 
    			$orgs = [['name' => '', 'link' => '', 'start' => '', 'end' => '']]; 
    		}

    		$ext_profile['org'] = wc_clean( array_values($orgs) );

    	}

    	if( ! empty( $_POST['account_work_exp_name'] ) ){

    		$exp_array = [];

    		foreach ($_POST['account_work_exp_name'] as $index => $exp) {
    			
    			$exp = ['name' => $exp, 'field' => $_POST['account_work_exp_field'][$index], 'start' => $_POST['account_work_exp_start'][$index], 'end' => $_POST['account_work_exp_end'][$index]];

    			$data_entered = array_diff($exp, array('', ' '));
    			if( count($data_entered) ) { 
    				$exp_array[] = $exp; 
    			}

    		}

    		if(!count($exp_array)){ 
    			$exp_array = [['name' => '', 'field' => '', 'start' => '', 'end' => '']]; 
    		}

    		$ext_profile['work_exp'] = wc_clean( array_values($exp_array) );

    	}

    	if( ! empty( $_POST['account_cert_name'] ) ){

    		$cert_array = [];

    		foreach ($_POST['account_cert_name'] as $index => $cert) {
    			
    			$link = $_POST['account_cert_link'][$index];

    			if( $link != '' ){
    				$link = ( strpos($link, 'http://') !== 0 && strpos($link, 'https://') !== 0 )? 'http://'.$link : $link;
				}

    			$cert = ['name' => $cert, 'auth' => $_POST['account_cert_auth'][$index], 'start' => $_POST['account_cert_start'][$index], 'end' => $_POST['account_cert_end'][$index], 'link' => $link, 'file' => $_POST['account_cert_file'][$index]];

    			$data_entered = array_diff($cert, array('', ' '));
    			if( count($data_entered) ) { 
    				$cert_array[] = $cert; 
    			}

    		}

    		if(!count($cert_array)){ 
    			$cert_array = [['name' => '', 'auth' => '', 'start' => '', 'end' => '', 'link' => '', 'file' => '']]; 
    		}

    		$ext_profile['certificates'] = wc_clean( array_values($cert_array) );

    	}

	    update_user_meta( $user_ID, 'ktt_extended_profile', $ext_profile );

	}

    function display_user_profile_completeness($user_id){

		$required_fields = [
            'mobile', 
            'skype', 
            'description', 
            'gender', 
            'dob', 
            'workyears', 
            'video', 
            'education', 
            'education_school', 
            'education_start', 
            'education_end', 
            'work_exp' => [
                'name',
                'filed',
                'start',
                'end'
            ],
            'org' => [
                'name', 
                'link',
                'start', 
                'end'
            ],
            'certificates' => [
                'name', 
                'auth', 
                'start', 
                'end', 
                'link', 
                'file'
            ],
            'country', 
            'state', 
            'city', 
            'address'
        ];

        $certificates  = isset( $ext_profile['certificates'] ) ? $ext_profile['certificates'] : [['name' => '', 'auth' => '', 'start' => '', 'end' => '', 'link' => '', 'file' => '']];

		$ext_profile = get_user_meta( $user_id, 'ktt_extended_profile', true );
		$completeness = 0;
        $object_count = count($required_fields, 1)-2;

		foreach($required_fields as $key => $req_field){

            if( is_array($req_field) ){

                if( isset($ext_profile[$key]) && is_array($ext_profile[$key]) ){

                    $first_array = array_shift($ext_profile[$key]);
            
                    foreach ($first_array as $k => $value) {
                    
                        if($value != ''){
                            $completeness += 100/$object_count;
                        }

                    }

                }

            } else if( isset($ext_profile[$req_field]) ){

                if ( $ext_profile[$req_field] != '' && $ext_profile[$req_field] != 'none'){
                    $completeness += 100/$object_count;
                }
            }

		}

        $user_avatar = get_user_meta( $user_id, 'dokan_profile_settings', true );
        if( isset( $user_avatar['gravatar'] ) && $user_avatar['gravatar'] != 0){
            $completeness += 100/$object_count;
        }

        $this->display_completeness_bar( ceil($completeness), __( 'Profile complete', 'ktt' ) );

	}



	function display_shop_profile_completeness($user_id){

		$required_fields = ['company_name', 'company_nr', 'company_type', 'description', 'media', 'address' => ['country', 'state', 'city', 'address', 'email', 'phone']];

		$ext_profile = get_user_meta( $user_id, 'ktt_extended_settings', true );

		$completeness = 0;
        $object_count = count($required_fields, 1);

		foreach($required_fields as $key => $req_field){

            if( is_array($req_field) ){

                if( isset($ext_profile[$key]) && is_array($ext_profile[$key]) ){

                    $first_array = array_shift($ext_profile[$key]);
            
                    foreach ($first_array as $k => $value) {
                    
                        if($value != ''){
                            $completeness += 100/$object_count;
                        }

                    }

                }

            } else if( isset($ext_profile[$req_field]) ){

                if ($req_field == 'media'){
    				$media = array_diff($ext_profile[$req_field], array('http://', 'https://', ''));

    				if( count($media) ){
    					$completeness += 100/$object_count;
    				}

				} else if( $ext_profile[$req_field] != '' && $ext_profile[$req_field] != 'none'){
					$completeness += 100/$object_count;
				}
			}

		}

        $profile_info = get_user_meta( $user_id, 'dokan_profile_settings', true );
        $banner = isset( $profile_info['banner'] ) ? absint( $profile_info['banner'] ) : 0;

        if($banner){
            $completeness += 100/$object_count;
        }

        $this->display_completeness_bar( ceil($completeness), __( 'Profile complete', 'ktt' ) );

	}


    function display_completeness_bar($percentage, $message){

        ?>
        <div class="dokan-panel dokan-panel-default">
            <div class="dokan-panel-body">
            <div class="dokan-progress lb-progress">
                <div class="dokan-progress-bar dokan-progress-bar-info dokan-progress-bar-striped" role="progressbar" aria-valuenow="<?= $percentage ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?= $percentage ?>%">
                    <?= $percentage ?>% <?= $message ?></div>
            </div>
           </div>
        </div>
        <?php

    }

    function woocommerce_user_form(){

        $user = wp_get_current_user();
        $user_id = $user->ID;

        $roles = $user->roles;
        if( in_array('seller', $roles) ){
            return;
        }

        $phone = get_user_meta( $user_id, 'billing_phone', true );
        $day = get_user_meta( $user_id, 'lb_dokan_dob_day', true );
        $month = get_user_meta( $user_id, 'lb_dokan_dob_month', true );
        $year = get_user_meta( $user_id, 'lb_dokan_dob_year', true );

        $ext_profile = get_user_meta( $user_id, 'ktt_extended_profile', true );
        $gender = ( isset($ext_profile['gender']) )? $ext_profile['gender'] : '';

        ?>

        <p class="woocommerce-FormRow woocommerce-FormRow--first form-row form-row-first">
            <label for="billing_phone"><?= __('Phone', 'ktt') ?></label>
            <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="billing_phone" id="billing_phone" value="<?= $phone ?>">
        </p>
        <p class="woocommerce-FormRow woocommerce-FormRow--last form-row form-row-last">
            <label for="lb_dokan_gender"><?= __('Gender', 'ktt') ?></label>
            <select name="account_gender">
                <option value=""> - <?= __('select gender', 'ktt') ?> - </option>
                <option value="male" <?= (($gender == 'male')? 'selected': '')?>><?= __('Male', 'ktt') ?></option>
                <option value="female" <?= (($gender == 'female')? 'selected': '')?>><?= __('Female', 'ktt') ?></option>
            </select>
        </p>
        <div class="break"></div>
        <p class="woocommerce-FormRow woocommerce-FormRow--wide form-row form-row-wide">
            <label for="lb_dokan_dob"><?= __('Date of birth', 'ktt') ?></label>
            <?php
                if(trim($day) !== "" && trim($month) !=="" && trim($year) !=="" )
                    $val = $day.'.'.$month.'.'.$year;
                else
                    $val="";

            ?>
            <input type="text" pattern="(0[1-9]|1[0-9]|2[0-9]|3[01]).(0[1-9]|1[012]).[0-9]{4}" value="<?= $val ?>" name="dummy" id="dummy_box" list="dates_pattern0_datalist" placeholder="dd.mm.yyyy" onchange="updateValues()" >
            <div class="dokan-hide">
                 <input type="number" class="lb-small-nr inp" name="lb_dokan_dob_day" id="lb_dokan_day" value="<?= $day ?>" placeholder="dd">
                 <input type="number" class="lb-small-nr inp" name="lb_dokan_dob_month" id="lb_dokan_month" value="<?= $month ?>" placeholder="mm">
                 <input type="number" class="lb-med-nr inp" name="lb_dokan_dob_year" id="lb_dokan_year" value="<?= $year ?>" placeholder="yyyy" >
             </div>
        </p>
        <script type="text/javascript">
            updateValues = ()=>{
                let values = jQuery.trim(jQuery("#dummy_box").val()).split(".");
                console.log(values);
                jQuery('#lb_dokan_day').val(values[0]);
                jQuery('#lb_dokan_month').val(values[1]);
                jQuery('#lb_dokan_year').val(values[2]);
            }
        </script>
<!--         <p class="woocommerce-FormRow woocommerce-FormRow--wide form-row form-row-wide">
            <label><?= __('Mailing list opt-in', 'ktt') ?></label>
            <input type="checkbox" name="lb_dokan_mailinglist" value="1">
        </p> -->
        <?php
        // TODO: integrate mailing list checkbox with the plugin in use
    }
 
    function woocommerce_user_save( $user_id ) {

        $user = wp_get_current_user();

        $roles = $user->roles;
        if( in_array('seller', $roles) ){
            return;
        }

        $ext_profile = get_user_meta( $user_id, 'ktt_extended_profile', true );
        $ext_profile['gender'] = ! empty( $_POST['account_gender'] ) ? wc_clean( $_POST['account_gender'] ) : '';
        update_user_meta( $user_id, 'ktt_extended_profile', $ext_profile );

        update_user_meta( $user_id, 'billing_phone', sanitize_text_field( $_POST[ 'billing_phone' ] ) );
        update_user_meta( $user_id, 'lb_dokan_dob_day', sanitize_text_field( $_POST[ 'lb_dokan_dob_day' ] ) );
        update_user_meta( $user_id, 'lb_dokan_dob_month', sanitize_text_field( $_POST[ 'lb_dokan_dob_month' ] ) );
        update_user_meta( $user_id, 'lb_dokan_dob_year', sanitize_text_field( $_POST[ 'lb_dokan_dob_year' ] ) );

    }


    /**
     * Display extended options on admin edit user page
     */
    function admin_user_profile_fields($user){

        $extended_settings = get_user_meta( $user->ID, 'ktt_extended_settings', true );
        $company_name  = isset( $extended_settings['company_name'] ) ? esc_attr( $extended_settings['company_name'] ) : '';
        $company_nr  = isset( $extended_settings['company_nr'] ) ? esc_attr( $extended_settings['company_nr'] ) : '';
        $company_type  = isset( $extended_settings['company_type'] ) ? esc_attr( $extended_settings['company_type'] ) : '';
        $description  = isset( $extended_settings['description'] ) ? esc_attr( $extended_settings['description'] ) : '';

        $media_links  = isset( $extended_settings['media'] ) ? $extended_settings['media'] : [''];
        
        $addresses  = isset( $extended_settings['address'] ) ? $extended_settings['address'] : [['country' => false, 'state' => '', 'city' => '', 'address' => '', 'email' => '', 'phone' => '']];

        // Profile specifics
        $ext_profile = get_user_meta( $user->ID, 'ktt_extended_profile', true );
        $organizations  = isset( $ext_profile['org'] ) ? $ext_profile['org'] : [['name' => '', 'link' => '', 'start' => '', 'end' => '']];
        $experience  = isset( $ext_profile['work_exp'] ) ? $ext_profile['work_exp'] : [['name' => '', 'field' => '', 'start' => '', 'end' => '']];
        $certificates  = isset( $ext_profile['certificates'] ) ? $ext_profile['certificates'] : [['name' => '', 'auth' => '', 'start' => '', 'end' => '', 'link' => '', 'file' => '']];

        ?>
        <h2>Dokan extended - store fields</h2>

        <table class="form-table">
            <tbody>
                <tr>
                    <th><label><?php _e( 'Company Name', 'ktt' ); ?></label></th>
                    <td>
                        <input id="dokan_store_name" required value="<?php echo $company_name; ?>" name="dokan_company_name" placeholder="<?php _e( 'company name', 'ktt'); ?>" class="regular-text" type="text">
                    </td>
                </tr>
                <tr>
                    <th><label><?php _e( 'Company reg nr', 'ktt' ); ?></label></th>
                    <td>
                        <input id="dokan_company_nr" required value="<?php echo $company_nr; ?>" name="dokan_company_nr" placeholder="<?php _e( 'company registration number', 'ktt'); ?>" class="regular-text" type="text">
                    </td>
                </tr>
                <tr>
                    <th><label><?php _e( 'Company type', 'ktt' ); ?></label></th>
                    <td>
                        <select name="dokan_company_type" required  id="dokan_company_type" style="max-width: 300px">
                            <option value=""> - <?php _e( 'company type', 'ktt'); ?> - </option>
                            <option value="1" <?= (($company_type == '1')? 'selected': '') ?>><?php _e( 'FIE', 'ktt'); ?></option>
                            <option value="2" <?= (($company_type == '2')? 'selected': '') ?>><?php _e( 'OÜ', 'ktt'); ?></option>
                            <option value="3" <?= (($company_type == '3')? 'selected': '') ?>><?php _e( 'AS', 'ktt'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label><?php _e( 'Short description', 'ktt' ); ?></label></th>
                    <td>
                        <textarea id="dokan_description" name="dokan_description"><?php echo $description; ?></textarea>
                    </td>
                </tr>
                <tr>
                    <th><label><?php _e( 'Media links', 'ktt' ); ?></label></th>
                    <td>
                        <div class="lb-elastic-container">
                            <div class="lb-elastic-elements">

                                <?php 

                                    foreach($media_links as $link){ ?>

                                        <div class="lb-elastic-element">
                                            <input value="<?php echo $link; ?>" name="dokan_media[]" placeholder="<?php _e( 'http://', 'ktt'); ?>" class="dokan-form-control" type="text">
                                        </div>

                                <?php } ?>
                                
                            </div>
                            <a href="#lb-add-more" class="lb-elastic-add"> + add more</a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><label><?php _e( 'Address', 'ktt' ); ?></label></th>
                    <td>
                        
                        <div class="lb-elastic-container">
                            <div class="lb-elastic-elements">

                                <?php
                                    $i = 0;
                                    foreach( $addresses as $address){
                                ?>
                                
                                    <div class="lb-elastic-element lb-input-margins">
                                        <?php lb_display_country_select($address['country'], 'dokan_address['.$i.'][country]') ?>
                                       
                                        <input value="<?= $address['state'] ?>" name="dokan_address[<?= $i ?>][state]" placeholder="<?php _e( 'State', 'ktt'); ?>" class="dokan-form-control" type="text">
                                        <input value="<?= $address['city'] ?>" name="dokan_address[<?= $i ?>][city]" placeholder="<?php _e( 'City', 'ktt'); ?>" class="dokan-form-control" type="text">
                                        <input value="<?= $address['address'] ?>" name="dokan_address[<?= $i ?>][address]" placeholder="<?php _e( 'Address', 'ktt'); ?>" class="dokan-form-control" type="text">
                                        <input value="<?= $address['email'] ?>" name="dokan_address[<?= $i ?>][email]" placeholder="<?php _e( 'Shop e-mail', 'ktt'); ?>" class="dokan-form-control" type="email">
                                        <input value="<?= $address['phone'] ?>" name="dokan_address[<?= $i ?>][phone]" placeholder="<?php _e( 'Shop phone', 'ktt'); ?>" class="dokan-form-control" type="text">
                                    </div>
                                <?php $i++; } ?>
                        
                                
                            </div>
                            <a href="#lb-add-more" class="lb-elastic-add"> + add more shops</a>
                        </div>

                    </td>
                </tr>
            </tbody>
        </table>

        <h2>Dokan extended - user fields</h2>

        <table class="form-table">
            <tbody>
                <tr>
                    <th><label><?php _e( 'Phone', 'ktt' ); ?></label></th>
                    <td>
                        <input type="text" class="input-text" name="account_mobile" id="account_mobile" value="<?php echo esc_attr( $ext_profile['mobile'] ); ?>" />
                    </td>
                </tr>
                <tr>
                    <th><label><?php _e( 'Skype', 'ktt' ); ?></label></th>
                    <td>
                        <input type="text" class="input-text" name="account_skype" id="account_skype" value="<?php echo esc_attr( $ext_profile['skype'] ); ?>" />
                    </td>
                </tr>
                <tr>
                    <th><label><?php _e( 'Description', 'ktt' ); ?></label></th>
                    <td>
                        <textarea rows="4" class="input-text" name="account_description" id="account_description"><?php echo esc_attr( $ext_profile['description'] ); ?></textarea>
                    </td>
                </tr>
                <tr>
                    <th><label><?php _e( 'Gender', 'ktt' ); ?></label></th>
                    <td>
                        <select name="account_gender" id="account_gender" style="max-width: 300px">
                            <option value="none"><?php _e( ' - Select gender - ', 'ktt' ); ?></option>
                            <option value="male" <?= ($ext_profile['gender'] == 'male')? 'selected' : '' ?>><?php _e( 'Male', 'ktt' ); ?></option>
                            <option value="female" <?= ($ext_profile['gender'] == 'female')? 'selected' : '' ?>><?php _e( 'Female', 'ktt' ); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label><?php _e( 'Date of birth', 'ktt' ); ?></label></th>
                    <td>
                        <input type="text" pattern="(0[1-9]|1[0-9]|2[0-9]|3[01]).(0[1-9]|1[012]).[0-9]{4}" value="<?php echo esc_attr( $ext_profile['dob'] ); ?>" name="account_dob" id="account_dob" list="dates_pattern0_datalist" placeholder="dd.mm.yyyy">
                    </td>
                </tr>
                <tr>
                    <th><label><?php _e( 'Working experience (years)', 'ktt' ); ?></label></th>
                    <td>
                        <input type="number" class="input-text" name="account_workyears" id="account_workyears" value="<?php echo esc_attr( $ext_profile['workyears'] ); ?>" />
                    </td>
                </tr>
                <tr>
                    <th><label><?php _e( 'YouTube video link', 'ktt' ); ?></label></th>
                    <td>
                        <input type="text" class="input-text" name="account_video" id="account_video" value="<?php echo esc_attr( $ext_profile['video'] ); ?>" />
                    </td>
                </tr>
                <tr>
                    <th><label><?php _e( 'Education', 'ktt' ); ?></label></th>
                    <td>
                        <select name="account_education" id="account_education" style="max-width: 300px">
                            <option value="none"><?php _e( ' - Select your education - ', 'ktt' ); ?></option>
                            <option value="1" <?= ($ext_profile['education'] == '1')? 'selected' : '' ?>><?php _e( 'Basic education', 'ktt' ); ?></option>
                            <option value="2" <?= ($ext_profile['education'] == '2')? 'selected' : '' ?>><?php _e( 'Secondary education', 'ktt' ); ?></option>
                            <option value="3" <?= ($ext_profile['education'] == '3')? 'selected' : '' ?>><?php _e( 'Vocational education', 'ktt' ); ?></option>
                            <option value="4" <?= ($ext_profile['education'] == '4')? 'selected' : '' ?>><?php _e( 'Higher education', 'ktt' ); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label><?php _e( 'School name', 'ktt' ); ?></label></th>
                    <td>
                        <input type="text" class="input-text" name="account_education_school" id="account_education_school" value="<?php echo esc_attr( $ext_profile['education_school'] ); ?>" />
                    </td>
                </tr>
                <tr>
                    <th><label><?php _e( 'Start date', 'ktt' ); ?></label></th>
                    <td>
                        <input type="text" class="input-text" name="account_education_start" id="account_education_start" value="<?php echo esc_attr( $ext_profile['education_start'] ); ?>" placeholder="mm.yyyy" />
                    </td>
                </tr>
                <tr>
                    <th><label><?php _e( 'End date', 'ktt' ); ?></label></th>
                    <td>
                        <input type="text" class="input-text" name="account_education_end" id="account_education_end" value="<?php echo esc_attr( $ext_profile['education_end'] ); ?>" placeholder="mm.yyyy" />
                    </td>
                </tr>
                <tr>
                    <th><label><?php _e( 'Work experience', 'ktt' ); ?></label></th>
                    <td>
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
                    </td>
                </tr>
                <tr>
                    <th><label><?php _e( 'Organization', 'ktt' ); ?></label></th>
                    <td>
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
                    </td>
                </tr>
                <tr>
                    <th><label><?php _e( 'Certificates', 'ktt' ); ?></label></th>
                    <td>
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
                    </td>
                </tr>
                <tr>
                    <th><label><?php _e( 'Country', 'ktt' ); ?></label></th>
                    <td>
                        <?= lb_display_country_select($ext_profile['country']) ?>
                    </td>
                </tr>
                <tr>
                    <th><label><?php _e( 'State', 'ktt' ); ?></label></th>
                    <td>
                        <input type="text" class="input-text" name="account_location_state" id="account_location_state" value="<?php echo esc_attr( $ext_profile['state'] ); ?>" />
                    </td>
                </tr>
                <tr>
                    <th><label><?php _e( 'City', 'ktt' ); ?></label></th>
                    <td>
                        <input type="text" class="input-text" name="account_location_city" id="account_location_city" value="<?php echo isset($ext_profile['city']) ? esc_attr( $ext_profile['city'] ) : ''; ?>" />
                    </td>
                </tr>
                <tr>
                    <th><label><?php _e( 'Address', 'ktt' ); ?></label></th>
                    <td>
                        <input type="text" class="input-text" name="account_location_address" id="account_location_address" value="<?php echo isset($ext_profile['address']) ? esc_attr( $ext_profile['address'] ) : ''; ?>" />
                    </td>
                </tr>
                
            </tbody>
        </table>

        <?php

    }

    function admin_user_save( $user_id ) {

        if ( !current_user_can( 'edit_user', $user_id ) )
            return false;

        $this->save_shop( $user_id );
        $this->save_user_details( $user_id );
        
    }

}
