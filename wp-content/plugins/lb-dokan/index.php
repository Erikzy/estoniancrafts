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
		add_action( 'admin_enqueue_scripts', [$this, 'admin_register_scripts'], 15 );
        add_action( 'woocommerce_save_account_details', [$this, 'save_user_details'] );
        add_action( 'dokan_product_edit_after_options', [$this, 'add_product_options_form'] );
		add_action( 'dokan_product_updated', [$this, 'product_updated'] );
		
        add_action( 'add_meta_boxes', [$this, 'add_box'] );
        add_action( 'save_post', [$this, 'save_post'] );

    }

	function register_scripts(){
        global $wp_scripts;

        if (!is_admin()) {
            wp_enqueue_style( 'lb-dokan', plugin_dir_url( __FILE__ ) . 'dokan.css', false,'1.0','all');
            wp_enqueue_script( 'lb-dokan', plugin_dir_url( __FILE__ ) . 'dokan.js', false,'1.0','all');
        }

    }

    function admin_register_scripts(){
        global $wp_scripts;

        wp_enqueue_style( 'lb-dokan-admin', plugin_dir_url( __FILE__ ) . 'dokan-admin.css', false,'1.0','all');
        wp_enqueue_script( 'lb-dokan-admin', plugin_dir_url( __FILE__ ) . 'dokan-admin.js', false,'1.0','all');
        
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

    static function product_completeness($product_id){

        $completeness = 0;
        $meta_data = get_post_meta($product_id, '', true);

        $required_fields = ['_manufacturing_method', '_manufacturing_desc', '_manufacturing_time', '_manufacturing_qty', '_maintenance_info'];

        foreach($meta_data as $key => $meta){

            $meta = $meta[0];

            if( in_array($key, $required_fields) && !empty($meta) ){

                $completeness += 100/count($required_fields);
            
            }

        }

        return floor($completeness);

    }

    /**
     * Displays extended form fields whed adding/editing new products
     */
    function add_product_options_form(){
        global $post;
        
        $this->product_extended_form($post->ID);

    }

    function product_extended_form($post_id){

        ?>

        <div class="lb-dokan-options dokan-edit-row dokan-clearfix">
            <div class="dokan-side-left">
                <h2><?php _e( 'Shipping options', 'ktt' ); ?></h2>
            </div>

            <div class="dokan-side-right">
                <div class="dokan-form-group">

                    <?php $is_fragile = ( get_post_meta($post_id, '_fragile_cargo', true) == 'yes' ) ? 'yes' : 'no'; ?>

                    <?php dokan_post_input_box( $post_id, '_fragile_cargo', array('value' => $is_fragile, 'label' => __( 'Fragile cargo', 'ktt' ) ), 'checkbox' ); ?>
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
                        <?php

                        $materials = get_post_meta($post_id, '_materials', true);

                        // var_dump($materials);die();

                        if( !is_array($materials) || !count($materials) ){
                            $materials = [['country' => '', 'name' => '', 'contents' => '', 'desc' => '']];
                        }

                        // var_dump($materials);die();


                        foreach($materials as $material){

                        ?>
                            <div class="lb-elastic-element lb-input-margins">

                                <div class="dokan-form-group">
                                    <label class="form-label"><?php _e( 'Material country', 'ktt' ); ?></label>
                                    <?php lb_display_country_select($material['country'], '_material_country[]') ?>
                                </div>

                                <div class="dokan-form-group">
                                    <label class="form-label"><?php _e( 'Material name', 'ktt' ); ?></label>
                                    <?php dokan_post_input_box( $post_id, '_material_name[]', array( 'placeholder' => __( 'Material name', 'ktt' ), 'value' => $material['name'] ), 'text' ); ?>
                                </div>

                                <div class="dokan-form-group">
                                    <label class="form-label"><?php _e( 'Material contents', 'ktt' ); ?></label>
                                    <?php dokan_post_input_box( $post_id, '_material_contents[]', array( 'placeholder' => __( 'Material contents', 'ktt' ), 'value' => $material['contents'] ), 'text' ); ?>
                                </div>

                                <div class="dokan-form-group">
                                    <label class="form-label"><?php _e( 'Description', 'ktt' ); ?></label>
                                    <?php dokan_post_input_box( $post_id, '_material_desc[]', array( 'placeholder' => __( 'Description', 'ktt' ), 'value' => $material['desc'] ), 'text' ); ?>
                                </div>
                                <hr>
                            </div>

                        <?php 
                        }
                        ?>

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
                                            ''  => __(' - select method - ', 'ktt'),
                                            'hand' => __( 'Hand crafted', 'ktt' ),
                                            'machine' => __( 'Machined', 'ktt' )
                                        ), 'value' => get_post_meta($post_id, '_manufacturing_method', true) ), 'select' ); ?>
                
                </div>

                <div class="dokan-form-group">
                    <label class="form-label"><?php _e( 'Manufacturing description', 'ktt' ); ?></label>
                    <?php dokan_post_input_box( $post_id, '_manufacturing_desc', array( 'placeholder' => __( 'Manufacturing description', 'ktt' ), 'value' => get_post_meta($post_id, '_manufacturing_desc', true) ), 'text' ); ?>
                </div>

                <div class="dokan-form-group">
                    <label class="form-label"><?php _e( 'Manufacturing time', 'ktt' ); ?></label>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <?php dokan_post_input_box( $post_id, '_manufacturing_time', array( 'placeholder' => __( 'Manufacturing time', 'ktt' ), 'value' => get_post_meta($post_id, '_manufacturing_time', true) ), 'number' ); ?>
                        </div>
                        <div class="col-md-6">
                            <?php dokan_post_input_box( $post_id, '_manufacturing_time_unit', array( 'options' => array(
                                                'hour' => __( 'Hours', 'ktt' ),
                                                'day' => __( 'Days', 'ktt' ),
                                                'week' => __( 'Weeks', 'ktt' ),
                                                'month' => __( 'Months', 'ktt' )
                                            ), 'value' => get_post_meta($post_id, '_manufacturing_time_unit', true) ), 'select' ); ?>
                        </div>
                    </div>
                    
                </div>

                <div class="dokan-form-group">
                    <label class="form-label"><?php _e( 'Manufacturing quantity', 'ktt' ); ?></label>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <?php dokan_post_input_box( $post_id, '_manufacturing_qty', array( 'placeholder' => __( 'Manufacturing quantity', 'ktt' ), 'value' => get_post_meta($post_id, '_manufacturing_qty', true) ), 'number' ); ?>
                        </div>
                        <div class="col-md-6">
                            <?php dokan_post_input_box( $post_id, '_manufacturing_qty_unit', array( 'options' => array(
                                                'hour' => __( 'in an hour', 'ktt' ),
                                                'day' => __( 'in a day', 'ktt' ),
                                                'week' => __( 'in a week', 'ktt' ),
                                                'month' => __( 'in a month', 'ktt' ),
                                                'year' => __( 'in a year', 'ktt' )
                                            ), 'value' => get_post_meta($post_id, '_manufacturing_qty_unit', true) ), 'select' ); ?>
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
                 
                    <?php wp_editor( get_post_meta($post_id, '_maintenance_info', true), '_maintenance_info', array('editor_height' => 50, 'quicktags' => false, 'media_buttons' => false, 'teeny' => true, 'editor_class' => 'post_excerpt') ); ?>
                
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
                    <?php

                    $media_links = get_post_meta($post_id, '_media_links', true);

                    if(!is_array($media_links)){
                        $media_links = [''];
                    }

                    foreach($media_links as $link){

                        ?>

                        <div class="lb-elastic-element lb-input-margins">
                            <div class="dokan-form-group">
                                <?php dokan_post_input_box( $post_id, '_media_link[]', array( 'placeholder' => 'http://', 'value' => $link ), 'text' ); ?>
                            </div>
                        </div>
                        
                        <?php

                    }

                    ?>

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

                        <?php

                        $certificates = get_post_meta($post_id, '_certificates', true);

                        if(!is_array($certificates) ){
                            $certificates = [['type' => '', 'file' => '']];
                        }

                        foreach($certificates as $cert){

                            $file = (int)$cert['file'];

                        ?>

                            <div class="lb-elastic-element lb-input-margins">
                                <div class="dokan-form-group">
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <?php dokan_post_input_box( $post_id, '_cert_type[]', array( 'options' => array(
                                                                '' => __( ' - select type - ', 'ktt' ),
                                                                'patent' => __( 'Patent', 'ktt' ),
                                                                'trademark' => __( 'Trademark', 'ktt' ),
                                                                'certificate' => __( 'Certificate', 'ktt' )
                                                            ), 'value' => $cert['type'] ), 'select' ); ?>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="hidden" class="input-text" name="_cert_file[]" value="<?= $file; ?>" />

                                            <a href="#remove" class="lb-file-placeholder <?php if( $file ){ echo 'active'; } ?>"></a>
                                            <a href="#add-file" class="lb-add-doc <?php if( !$file ){ echo 'active'; } ?>"> + <?php _e( 'Add document', 'ktt' ); ?></a>
                                            <a href="#add-file" class="lb-remove-doc <?php if( $file ){ echo 'active'; } ?>"> + <?php _e( 'Remove document', 'ktt' ); ?></a>
                                            
                                        </div>
                                    </div>

                                </div>
                            </div>
                        
                        <?php } ?>

                    </div>
                    <a href="#lb-add-more" class="lb-elastic-add"> + add more...</a>
                    
                </div>

            </div>

        </div><!-- .lb-dokan-options -->

        <?php

    }

    /**
     * Save additional Dokan fields to product meta
     */
    function product_updated($product_id){

        update_post_meta( $product_id, '_fragile_cargo', wc_clean($_POST['_fragile_cargo']));
        update_post_meta( $product_id, '_manufacturing_method', wc_clean($_POST['_manufacturing_method']));
        update_post_meta( $product_id, '_manufacturing_desc', wc_clean($_POST['_manufacturing_desc']));
        update_post_meta( $product_id, '_manufacturing_time', wc_clean($_POST['_manufacturing_time']));
        update_post_meta( $product_id, '_manufacturing_time_unit', wc_clean($_POST['_manufacturing_time_unit']));
        update_post_meta( $product_id, '_manufacturing_qty', wc_clean($_POST['_manufacturing_qty']));
        update_post_meta( $product_id, '_manufacturing_qty_unit', wc_clean($_POST['_manufacturing_qty_unit']));

        update_post_meta( $product_id, '_maintenance_info', $_POST['_maintenance_info']);
        
        if( ! empty( $_POST['_media_link'] ) ){

            $media = $_POST['_media_link'];

            // Remove all empty strings first
            $media = array_diff($media, array('http://', 'https://', ''));

            // Make sure all media links have http:// or https:// in front of them
            $media = array_map(function($element) {
                    return (strpos($element, 'http://') !== 0 && strpos($element, 'https://') !== 0)? 'http://'.$element : $element;
                },
                $media
            );

            if(!count($media)){ $media = ['']; }
            
            update_post_meta( $product_id, '_media_links', wc_clean($media));

        }



        if( ! empty( $_POST['_material_country'] ) ){

            $material_array = [];

            foreach ($_POST['_material_country'] as $index => $country) {

                $material = [ 'country' => $country, 'name' => $_POST['_material_name'][$index], 'contents' => $_POST['_material_contents'][$index], 'desc' => $_POST['_material_desc'][$index] ];

                $data_entered = array_diff($material, array('', ' '));
                if( count($data_entered) ) { 
                    $material_array[] = $material; 
                }

            }

            if(!count($material_array)){ 
                $material_array = [['country' => '', 'name' => '', 'contents' => '', 'desc' => '']];
            }

            update_post_meta( $product_id, '_materials', wc_clean($material_array));

        }


        if( ! empty( $_POST['_cert_file'] ) ){

            $certificates = [];

            foreach ($_POST['_cert_file'] as $index => $file) {

                if( $file == 0 || $file == '0' || empty($_POST['_cert_type'][$index]) ){
                    continue;
                }

                $cert = [ 'type' => $_POST['_cert_type'][$index], 'file' => $file ];

                $data_entered = array_diff($cert, array('', ' '));
                if( count($data_entered) ) { 
                    $certificates[] = $cert; 
                }

            }

            if(!count($certificates)){ 
                $certificates = [['type' => '', 'file' => '']];
            }

            update_post_meta( $product_id, '_certificates', wc_clean($certificates));

        }

    }


    function save_post( $post_id ){
        
        $post_type = get_post_type($post_id);

        // If this isn't a product, don't try to save fields
        if ( "product" != $post_type ) return;

        $this->product_updated( $post_id );

    }

    public function add_box(){

        add_meta_box(
            'lb-dokan-extra',                    // Unique ID
            'Extra fields',                   // Box title
            [$this, 'product_box_extra_fields'],       // Content callback
            ['product']             // post type
        );

    }

    function product_box_extra_fields( $post ) {

        $this->product_extended_form($post->ID);
        
    }

}

new lbDokan();