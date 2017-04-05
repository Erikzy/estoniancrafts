<?php
/**
 * Plugin Name: Dokan extension
 * Description: Käsitööturg custom extension for Dokan plugin
 * Version: 1.0
 */


class lbDokan{

	function __construct(){

		add_action( 'dokan_store_profile_saved', [$this, 'save_shop'] );
		add_action( 'wp_enqueue_scripts', [$this, 'register_scripts'], 15 );
        add_action( 'woocommerce_save_account_details', [$this, 'save_user_details'] );
		add_action( 'dokan_product_edit_after_options', [$this, 'add_product_options_form'] );
		
	}

	function register_scripts(){
		global $wp_scripts;

		if (!is_admin()) {
			wp_enqueue_style( 'lb-dokan', plugin_dir_url( __FILE__ ) . 'dokan.css', false,'1.0','all');
			wp_enqueue_script( 'lb-dokan', plugin_dir_url( __FILE__ ) . 'dokan.js', false,'1.0','all');
		}

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


	    update_user_meta( $store_id, 'ktt_extended_settings', $ext_settings );

	}


	function save_user_details( $user_ID ){

		$user_avatar = get_user_meta( $user_ID, 'dokan_profile_settings', true );
	    $user_avatar['gravatar'] = $_POST['dokan_gravatar'];
	    update_user_meta( $user_ID, 'dokan_profile_settings', $user_avatar );

		$ext_profile = get_user_meta( $user_ID, 'ktt_extended_profile', true );

    	$ext_profile['mobile'] = ! empty( $_POST['account_mobile'] ) ? wc_clean( $_POST['account_mobile'] ) : '';
    	$ext_profile['skype'] = ! empty( $_POST['account_skype'] ) ? wc_clean( $_POST['account_skype'] ) : '';
    	$ext_profile['gender'] = ! empty( $_POST['account_gender'] ) ? wc_clean( $_POST['account_gender'] ) : '';
    	$ext_profile['dob'] = ! empty( $_POST['account_dob'] ) ? wc_clean( $_POST['account_dob'] ) : '';
    	$ext_profile['workyears'] = ! empty( $_POST['account_workyears'] ) ? wc_clean( $_POST['account_workyears'] ) : '';
    	$ext_profile['video'] = ! empty( $_POST['account_video'] ) ? wc_clean( $_POST['account_video'] ) : '';
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

	static function user_profile_completeness($user_id){

		// TODO: make sure everything is being counted

		$required_fields = ['mobile', 'skype', 'gender', 'dob', 'workyears', 'video', 'description', 'education', 'country', 'state', 'city', 'address'];

		$ext_profile = get_user_meta( $user_id, 'ktt_extended_profile', true );
		$completeness = 0;

		foreach($required_fields as $req_field){

			if( isset($ext_profile[$req_field]) && !empty($ext_profile[$req_field]) ){
				$completeness += 100/count($required_fields);
			}

		}

		return floor($completeness);

	}

	static function shop_profile_completeness($user_id){
		// TODO: make sure everything is being counted

		$required_fields = ['company_name', 'company_nr', 'company_type', 'description', 'media', 'address'];

		$ext_profile = get_user_meta( $user_id, 'ktt_extended_settings', true );
		$completeness = 0;

		foreach($required_fields as $req_field){

			if( isset($ext_profile[$req_field]) && !empty($ext_profile[$req_field]) ){

				if($req_field == 'media' || $req_field == 'address'){
    				$media = array_diff($ext_profile[$req_field], array('http://', 'https://', ''));

    				if( count($media) ){
    					$completeness += 100/count($required_fields);
    				}
				}else{
					$completeness += 100/count($required_fields);
				}
			}

		}

		return floor($completeness);

	}

    /**
     * Displays extended from fields whed adding/editing new products
     */
    function add_product_options_form(){
        global $post;

        $post_id = $post->ID;
        ?>

        <div class="lb-dokan-options dokan-edit-row dokan-clearfix">
            <div class="dokan-side-left">
                <h2><?php _e( 'Käsitööturg options', 'ktt' ); ?></h2>
            </div>

            <div class="dokan-side-right">
                <div class="dokan-form-group">

                    <?php $is_fragile = ( $post->comment_status == 'open' ) ? 'yes' : 'no'; ?>

                    <?php dokan_post_input_box( $post_id, '_enable_reviews', array('value' => $is_fragile, 'label' => __( 'Fragile cargo', 'ktt' ) ), 'checkbox' ); ?>
                </div>

            </div>
        </div><!-- .lb-dokan-options -->


        <div class="lb-dokan-options dokan-edit-row dokan-clearfix">
            <div class="dokan-side-left">
                <h2><?php _e( 'Used materials', 'ktt' ); ?></h2>
            </div>

            <div class="dokan-side-right">
                <div class="lb-elastic-container">
                    <div class="lb-elastic-elements">

                        <div class="lb-elastic-element lb-input-margins">

                            <div class="dokan-form-group">
                                <label class="form-label"><?php _e( 'Material country', 'ktt' ); ?></label>
                                <?php lb_display_country_select(false, '_material_country[]') ?>
                            </div>

                            <div class="dokan-form-group">
                                <label class="form-label"><?php _e( 'Material name', 'ktt' ); ?></label>
                                <?php dokan_post_input_box( $post_id, '_material_name[]', array( 'placeholder' => __( 'Material name', 'ktt' ) ), 'text' ); ?>
                            </div>

                            <div class="dokan-form-group">
                                <label class="form-label"><?php _e( 'Material contents', 'ktt' ); ?></label>
                                <?php dokan_post_input_box( $post_id, '_material_contents[]', array( 'placeholder' => __( 'Material contents', 'ktt' ) ), 'text' ); ?>
                            </div>

                            <div class="dokan-form-group">
                                <label class="form-label"><?php _e( 'Description', 'ktt' ); ?></label>
                                <?php dokan_post_input_box( $post_id, '_material_desc[]', array( 'placeholder' => __( 'Description', 'ktt' ) ), 'text' ); ?>
                            </div>
                            <hr>
                        </div>
                    </div>
                    <a href="#lb-add-more" class="lb-elastic-add"> + add more...</a>
                </div>
            </div>

        </div><!-- .lb-dokan-options -->

        <div class="lb-dokan-options dokan-edit-row dokan-clearfix">
            <div class="dokan-side-left">
                <h2><?php _e( 'Manufacturing info', 'ktt' ); ?></h2>
            </div>

            <div class="dokan-side-right">
               
                <div class="dokan-form-group">
                    <label class="form-label"><?php _e( 'Manufacturing method', 'ktt' ); ?></label>
                 
                    <?php dokan_post_input_box( $post_id, '_manufacturing_method', array( 'options' => array(
                                            'hand' => __( 'Hand crafted', 'ktt' ),
                                            'machine' => __( 'Machined', 'ktt' )
                                        ) ), 'select' ); ?>
                
                </div>

                <div class="dokan-form-group">
                    <label class="form-label"><?php _e( 'Manufacturing description', 'ktt' ); ?></label>
                    <?php dokan_post_input_box( $post_id, '_manufacturing_desc', array( 'placeholder' => __( 'Manufacturing description', 'ktt' ) ), 'text' ); ?>
                </div>

                <div class="dokan-form-group">
                    <label class="form-label"><?php _e( 'Manufacturing time', 'ktt' ); ?></label>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <?php dokan_post_input_box( $post_id, '_manufacturing_time', array( 'placeholder' => __( 'Manufacturing time', 'ktt' ) ), 'number' ); ?>
                        </div>
                        <div class="col-md-6">
                            <?php dokan_post_input_box( $post_id, '_manufacturing_time_unit', array( 'options' => array(
                                                'hour' => __( 'Hours', 'ktt' ),
                                                'day' => __( 'Days', 'ktt' ),
                                                'week' => __( 'Weeks', 'ktt' ),
                                                'month' => __( 'Months', 'ktt' )
                                            ) ), 'select' ); ?>
                        </div>
                    </div>
                    
                </div>

                <div class="dokan-form-group">
                    <label class="form-label"><?php _e( 'Manufacturing quantity', 'ktt' ); ?></label>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <?php dokan_post_input_box( $post_id, '_manufacturing_qty', array( 'placeholder' => __( 'Manufacturing quantity', 'ktt' ) ), 'number' ); ?>
                        </div>
                        <div class="col-md-6">
                            <?php dokan_post_input_box( $post_id, '_manufacturing_time_unit', array( 'options' => array(
                                                'hour' => __( 'in an hour', 'ktt' ),
                                                'day' => __( 'in a day', 'ktt' ),
                                                'week' => __( 'in a week', 'ktt' ),
                                                'month' => __( 'in a month', 'ktt' ),
                                                'year' => __( 'in a year', 'ktt' )
                                            ) ), 'select' ); ?>
                        </div>
                    </div>
                    
                </div>

            </div>

        </div><!-- .lb-dokan-options -->

        <div class="lb-dokan-options dokan-edit-row dokan-clearfix">
            <div class="dokan-side-left">
                <h2><?php _e( 'Maintenance', 'ktt' ); ?></h2>
            </div>

            <div class="dokan-side-right">
               
                <div class="dokan-form-group">
                    <label class="form-label"><?php _e( 'Maintenance info', 'ktt' ); ?></label>
                 
                    <?php wp_editor( 'ccxxx' , '_maintenance_info', array('editor_height' => 50, 'quicktags' => false, 'media_buttons' => false, 'teeny' => true, 'editor_class' => 'post_excerpt') ); ?>
                
                </div>


            </div>

        </div><!-- .lb-dokan-options -->

        <div class="lb-dokan-options dokan-edit-row dokan-clearfix">
            <div class="dokan-side-left">
                <h2><?php _e( 'External media links', 'ktt' ); ?></h2>
            </div>

            <div class="dokan-side-right">
               
                <div class="lb-elastic-container">
                    <div class="lb-elastic-elements">

                        <div class="lb-elastic-element lb-input-margins">
                            <div class="dokan-form-group">
                                <?php dokan_post_input_box( $post_id, '_media_link[]', array( 'placeholder' => 'http://' ), 'text' ); ?>
                            </div>
                        </div>
                    </div>
                    <a href="#lb-add-more" class="lb-elastic-add"> + add more...</a>
                    
                </div>

            </div>

        </div><!-- .lb-dokan-options -->

        <div class="lb-dokan-options dokan-edit-row dokan-clearfix">
            <div class="dokan-side-left">
                <h2><?php _e( 'Patent / Certificate', 'ktt' ); ?></h2>
            </div>

            <div class="dokan-side-right">
               
                <div class="lb-elastic-container">
                    <div class="lb-elastic-elements">

                        <div class="lb-elastic-element lb-input-margins">
                            <div class="dokan-form-group">
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <?php dokan_post_input_box( $post_id, '_file_type', array( 'options' => array(
                                                            'patent' => __( 'Patent', 'ktt' ),
                                                            'trademark' => __( 'Trademark', 'ktt' ),
                                                            'certificate' => __( 'Certificate', 'ktt' )
                                                        ) ), 'select' ); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="hidden" class="input-text" name="account_cert_file[]" id="account_cert_file" value="<?= (int)$cert['file']; ?>" />

                                        <a href="#remove" class="lb-file-placeholder <?php if( (int)$cert['file'] != 0){ echo 'active'; } ?>"></a>
                                        <a href="#add-file" class="lb-add-doc <?php if( (int)$cert['file'] == 0){ echo 'active'; } ?>"> + <?php _e( 'Add document', 'ktt' ); ?></a>
                                        <a href="#add-file" class="lb-remove-doc <?php if( (int)$cert['file'] != 0){ echo 'active'; } ?>"> + <?php _e( 'Remove document', 'ktt' ); ?></a>
                                        
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <a href="#lb-add-more" class="lb-elastic-add"> + add more...</a>
                    
                </div>

            </div>

        </div><!-- .lb-dokan-options -->

        <?php

    }

}

new lbDokan();