<?php

/**
*  Product Functionality for Product Handler
*
*  @since 2.4
*
*  @package dokan
*/
class Dokan_Template_Products {

    public static $errors;
    public static $draft_errors;
    public static $product_cat;
    public static $post_content;

    /**
     *  Load autometially when class initiate
     *
     *  @since 2.4
     *
     *  @uses actions
     *  @uses filters
     */
    function __construct() {
        add_action( 'dokan_render_product_listing_template', array( $this, 'render_product_listing_template' ), 11 );
        add_action( 'template_redirect', array( $this, 'handle_all_submit' ), 11 );
        add_action( 'template_redirect', array( $this, 'handle_delete_product' ) );
        add_action( 'dokan_render_new_product_template', array( $this, 'render_new_product_template' ), 10 );
        add_action( 'dokan_render_product_edit_template', array( $this, 'load_product_edit_template' ), 11 );
        
      
    }

    /**
     * Singleton method
     *
     * @return self
     */
    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new Dokan_Template_Products();
        }

        return $instance;
    }

    /**
     * Render New Product Template for only free version
     *
     * @since 2.4
     *
     * @param  array $query_vars
     *
     * @return void
     */
    public function render_new_product_template( $query_vars ) {

        if ( isset( $query_vars['new-product'] ) && !WeDevs_Dokan::init()->is_pro() ) {
            dokan_get_template_part( 'products/new-product-single' );
        }
    }

    /**
     * Load Product Edit Template
     *
     * @since 2.4
     *
     * @return void
     */
    public function load_product_edit_template() {
        if ( !WeDevs_Dokan::init()->is_pro() ) {
            dokan_get_template_part( 'products/new-product-single' );
        }
    }

    /**
     * Render Product Listing Template
     *
     * @since 2.4
     *
     * @param  string $action
     *
     * @return void
     */
    public function render_product_listing_template( $action ) {
        dokan_get_template_part( 'products/products-listing');
    }

    /**
     * Handle all the form POST submit
     *
     * @return void
     */
    function handle_all_submit() {

        if ( ! is_user_logged_in() ) {
            return;
        }

        if ( ! dokan_is_user_seller( get_current_user_id() ) ) {
            return;
        }

        $errors = array();
        $draft_errors = array();
        self::$product_cat  = -1;
        self::$post_content = __( 'Details of your product ...', 'dokan' );

        if ( ! $_POST ) {
            return;
        }


        global $wpdb;
       
        if ( ( isset( $_POST['dokan_add_product'] ) ||  ( isset( $_POST["dokan_save_draft_product"] ) && $_POST["dokan_save_draft_product"]  == "true"  ) ) && wp_verify_nonce( $_POST['dokan_add_new_product_nonce'], 'dokan_add_new_product' ) ) {
             
            $post_title              = trim( $_POST['post_title'] );
            $post_content            = trim( $_POST['post_content'] );
            $post_excerpt            = isset( $_POST['post_excerpt'] ) ? trim( $_POST['post_excerpt'] ) : '';
            $_POST['_regular_price'] = isset(  $_POST['_regular_price'] ) ?  $_POST['_regular_price'] : '';
            $price                   = floatval( $_POST['_regular_price'] );
            $featured_image          = absint( $_POST['feat_image_id'] );
            $sku                     = isset( $_POST['_sku'] ) ? trim( $_POST['_sku'] ) : '';
            $is_lot_discount         = isset( $_POST['_is_lot_discount'] ) ? $_POST['_is_lot_discount'] : 'no';


            if ( empty( $post_title ) ) {
                $errors[] = __( 'Please enter product title', 'dokan' );
                $draft_errors[] = __( 'Please enter product title', 'dokan' );
            }
            
            // product dimensions
            $unit = isset($_POST['lb-dimension-unit']) ? $_POST['lb-dimension-unit'] : null;
            $length = isset($_POST['_length']) ? (double)$_POST['_length'] : null;
            $width = isset($_POST['_width']) ? (double)$_POST['_width'] : null;
            $height = isset($_POST['_height']) ? (double)$_POST['_height'] : null;

            $unitCorrection = 1.0;
            if ('cm' === $unit) {
                $unitCorrection = 0.1;
            } else if ('m' === $unit) {
                $unitCorrection = 0.001;
            }
            // default values in mm
            $maxLength = ((double)get_option('_product_max_length')) * $unitCorrection;
            $maxWidth = ((double)get_option('_product_max_width')) * $unitCorrection;
            $maxHeight = ((double)get_option('_product_max_height')) * $unitCorrection;

            // check clamp
            if ($length && $maxLength && ($length < 0 || $length > $maxLength)) {
                $errors[] = __('Invalid length', 'ktt');
            }
            if ($width && $maxWidth && ($width < 0 || $width > $maxWidth)) {
                $errors[] = __('Invalid width', 'ktt');   
            }
            if ($height && $maxHeight && ($height < 0 || $height > $maxHeight)) {
                $errors[] = __('Invalid height', 'ktt');
            }            
            
            // check description limit
    //ERIK: REMOVAL OF DESCRIPTION LIMIT AS TRELLO TASK		

/*            $descriptionLimit = (int)get_option('_product_description_limit');
            if($descriptionLimit && strlen($post_content) > $descriptionLimit)
            {
                $errors[] = __('Description is too long', 'ktt');
            }
*/
            $shortDescriptionLimit = (int)get_option('_product_short_description_limit');
            if($shortDescriptionLimit && strlen($post_excerpt) > $shortDescriptionLimit)
            {
                $errors[] = __('Short description is too long', 'ktt');
            }
           
      

            if( dokan_get_option( 'product_category_style', 'dokan_selling', 'single' ) == 'single' ) {
                $product_cat    = intval( $_POST['product_cat'] );
                if ( $product_cat < 0 ) {
                    $errors[] = __( 'Please select a category', 'dokan' );
                }
            } else {
                if( !isset( $_POST['product_cat'] ) && empty( $_POST['product_cat'] ) ) {
                    $errors[] = __( 'Please select AT LEAST ONE category', 'dokan' );
                }
            }
            
            $_sku_post_id = $wpdb->get_var( $wpdb->prepare("
                    SELECT $wpdb->posts.ID
                    FROM $wpdb->posts
                    LEFT JOIN $wpdb->postmeta ON ($wpdb->posts.ID = $wpdb->postmeta.post_id)
                    WHERE $wpdb->posts.post_type = 'product'
                    AND $wpdb->posts.post_status = 'publish'
                    AND $wpdb->postmeta.meta_key = '_sku' AND $wpdb->postmeta.meta_value = '%s'
                 ", $sku ) );
   
            if($_POST["_create_variation"] == "yes"){
                 if(isset($_POST['variable_regular_price'])){
                    for($a = 0 ; $a < sizeof($_POST['variable_regular_price'] ) ; $a++ ){
                        if($_POST['variable_regular_price'][$a] == 0 )
                            $errors[] = __( 'Please add a price', 'dokan' );

                    }
                 }
                 if( ( isset($_POST['_variation_product_update']) && $_POST['_variation_product_update'] == "yes"  && !isset($_POST['variable_regular_price'])  ) || ( !isset($_POST["attribute_names"]) && !isset($_POST["variable_regular_price"])  ) ){
                        $errors[] = __( 'Please  add a variation', 'dokan' );
                 }

                 if(isset($_POST["attribute_names"]) && !isset($_POST["variable_regular_price"]) &&   ( empty($_POST["attribute_values"][0]) ) ){
                    $errors[] = __( 'Please  add an attribute', 'dokan' );

                 }
            }
            if($price <= 0 ){
                if(   $_POST["_create_variation"] == "no"  )
                 $errors[] = __( 'Please add a price', 'dokan' );
            }

            if ( isset( $_POST['dokan_product_id'] ) && empty( $_POST['dokan_product_id'] ) ) {
               
                if ( ! empty( $sku ) && $_sku_post_id ) {
                    $errors[] = __( 'Product SKU must be unique.', 'dokan' );
                }
                
                self::$errors = apply_filters( 'dokan_can_add_product', $errors );
                
            } else {
                if ($_sku_post_id && ! empty( $sku ) && $_sku_post_id != (int) $_POST['dokan_product_id']  ) {
                    $errors[] = __( 'Product SKU must be unique.', 'dokan' );
                }
                
                self::$errors = apply_filters( 'dokan_can_edit_product', $errors );
            }

       

            if (  !self::$errors  ||  ( !self::$draft_errors  && ( isset( $_POST["dokan_save_draft_product"] ) && $_POST["dokan_save_draft_product"]  == "true"  ) )  ) {
                
                $_POST['dokan_product_id'] = isset( $_POST['dokan_product_id'] ) ? $_POST['dokan_product_id'] : '';
                
                if( isset( $_POST['dokan_product_id'] ) && empty( $_POST['dokan_product_id'] ) ) {
                    $product_status = dokan_get_new_post_status();
                    if( $_POST["_create_variation"]  === "yes"  && !isset($_POST['variable_regular_price'] ) )
                        $product_status= "draft";
                    $post_data = apply_filters( 'dokan_insert_product_post_data', array(
                        'post_type'    => 'product',
                        'post_status'  => ( isset( $_POST["dokan_save_draft_product"] ) && $_POST["dokan_save_draft_product"]  == "true"  ) ? "draft" : $product_status,
                        'post_title'   => $post_title,
                        'post_content' => $post_content,
                        'post_excerpt' => $post_excerpt,
                    ) );

                    $product_id = wp_insert_post( $post_data );
                    
                } else {
                    $post_id = (int)$_POST['dokan_product_id'];
                    $product_info = apply_filters( 'dokan_update_product_post_data', array(
                        'ID'             => $post_id,
                        'post_title'     => sanitize_text_field( $_POST['post_title'] ),
                        'post_content'   => $_POST['post_content'],
                        'post_excerpt'   => $_POST['post_excerpt'],
                        'post_status'    => isset( $_POST['post_status'] ) ? $_POST['post_status'] : 'pending',
                        'comment_status' => isset( $_POST['_enable_reviews'] ) ? 'open' : 'closed'
                    ) );

                    $product_id = wp_update_post( $product_info );
                }

                if ( $product_id ) {
                    
                    
                    
                    
                    
                    /****
                     * Added by urmas
                     * Manufacturing and Used materials diled
                     */
                    
                    
                    
                    update_post_meta( $product_id, '_backorder_time', wc_clean($_POST['_backorder_time']));
        update_post_meta( $product_id, '_fragile_cargo', wc_clean($_POST['_fragile_cargo']));
        update_post_meta( $product_id, '_manufacturing_method', wc_clean($_POST['_manufacturing_method']));
        update_post_meta( $product_id, '_manufacturing_desc', wc_clean($_POST['_manufacturing_desc']));
        update_post_meta( $product_id, '_manufacturing_time', wc_clean($_POST['_manufacturing_time']));
        update_post_meta( $product_id, '_manufacturing_time_unit', wc_clean($_POST['_manufacturing_time_unit']));
        update_post_meta( $product_id, '_manufacturing_qty', wc_clean($_POST['_manufacturing_qty']));
        update_post_meta( $product_id, '_manufacturing_qty_unit', wc_clean($_POST['_manufacturing_qty_unit']));

        update_post_meta( $product_id, '_maintenance_info', $_POST['_maintenance_info']);
    	
    	do_action('ec_extra_product_meta', $product_id,$_POST);    
        
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
        
        if( ! empty( $_POST['_product_videos'] ) ){

            $product_videos= $_POST['_product_videos'];

            // Remove all empty strings first
            $product_videos = array_diff($product_videos, array('http://', 'https://', ''));

            // Make sure all media links have http:// or https:// in front of them
            $product_videos= array_map(function($element) {
                    return (strpos($element, 'http://') !== 0 && strpos($element, 'https://') !== 0)? 'http://'.$element : $element;
                },
                $product_videos
            );

            if(!count($product_videos)){ $product_videos = ['']; }
            
            update_post_meta( $product_id, '_product_videos', wc_clean($product_videos));

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
                    
                    /****
                     * Added by urmas
                     * Finish Manufacturing and Used materials diled
                     */     
                    
                    
                    
                    /**/

                    /** set images **/
                    if ( $featured_image ) {
                        set_post_thumbnail( $product_id, $featured_image );
                    }else{
                        
                        delete_post_thumbnail( $product_id);
                    }

                    if( isset( $_POST['product_tag'] ) && !empty( $_POST['product_tag'] ) ) {
                        $tags_ids = array_map( 'intval', (array)$_POST['product_tag'] );
                        wp_set_object_terms( $product_id, $tags_ids, 'product_tag' );
                    }

                    /** set product category * */
                    if( dokan_get_option( 'product_category_style', 'dokan_selling', 'single' ) == 'single' ) {
                        wp_set_object_terms( $product_id, (int) $_POST['product_cat'], 'product_cat' );
                    } else {
                        if( isset( $_POST['product_cat'] ) && !empty( $_POST['product_cat'] ) ) {
                            $cat_ids = array_map( 'intval', (array)$_POST['product_cat'] );
                            wp_set_object_terms( $product_id, $cat_ids, 'product_cat' );
                        }
                    }
                    
                    if ( isset( $_POST['product-type'] ) ) {
                        wp_set_object_terms( $product_id, $_POST['product-type'], 'product_type' );
                    } else {

                        /** Set Product type by default simple */
                        if ( isset( $_POST['_create_variation'] ) && $_POST['_create_variation'] == 'yes' ) {
                            wp_set_object_terms( $product_id, 'variable', 'product_type' );
                        } else {
                            wp_set_object_terms( $product_id, 'simple', 'product_type' );
                        }
                    }

                    update_post_meta( $product_id, '_regular_price', $price );
                    update_post_meta( $product_id, '_sale_price', '' );
                    update_post_meta( $product_id, '_price', $price );
                    update_post_meta( $product_id, '_visibility', 'visible' );

                    dokan_new_process_product_meta( $product_id );
                
                    if( isset( $_POST['dokan_product_id'] ) && !empty( $_POST['dokan_product_id'] ) ) {                        
                        do_action( 'dokan_product_updated', $product_id );
                    }  else {
                        do_action( 'dokan_new_product_added', $product_id );
                    }

                    if( isset( $_POST['dokan_product_id'] ) && empty( $_POST['dokan_product_id'] ) ) {
                        if ( dokan_get_option( 'product_add_mail', 'dokan_general', 'on' ) == 'on' ) {
                            Dokan_Email::init()->new_product_added( $product_id, $product_status );
                        }
                    }

                    if ( $is_lot_discount == 'yes' ) {
                        $lot_discount_quantity = isset($_POST['_lot_discount_quantity']) ? $_POST['_lot_discount_quantity'] : 0;
                        $lot_discount_amount   = isset($_POST['_lot_discount_amount']) ? $_POST['_lot_discount_amount'] : 0;
                        if ( $lot_discount_quantity == '0' || $lot_discount_amount == '0' ) {
                            update_post_meta( $product_id, '_lot_discount_quantity', $lot_discount_quantity);
                            update_post_meta( $product_id, '_lot_discount_amount', $lot_discount_amount);
                            update_post_meta( $product_id, '_is_lot_discount', 'no');
                        } else {
                            update_post_meta( $product_id, '_lot_discount_quantity', $lot_discount_quantity);
                            update_post_meta( $product_id, '_lot_discount_amount', $lot_discount_amount);
                            update_post_meta( $product_id, '_is_lot_discount', $is_lot_discount);
                        }
                    } else if ( $is_lot_discount == 'no' ) {
                        update_post_meta( $product_id, '_lot_discount_quantity', 0);
                        update_post_meta( $product_id, '_lot_discount_amount', 0);
                        update_post_meta( $product_id, '_is_lot_discount', 'no');
                    }
                    
                    if ( isset( $_POST['product-type'] ) ) {
                        wp_set_object_terms( $product_id, $_POST['product-type'], 'product_type' );
                    }
                    $redirect_url = apply_filters( 'dokan_add_new_product_redirect', dokan_edit_product_url( $product_id ), $product_id );
                    wp_redirect( add_query_arg( array( 'message' => 'success' ), $redirect_url ) );
                    exit;
                }
            }

        }

        if ( isset( $_POST['add_product'] ) && wp_verify_nonce( $_POST['dokan_add_new_product_nonce'], 'dokan_add_new_product' ) ) {
            $post_title     = trim( $_POST['post_title'] );
            $post_content   = trim( $_POST['post_content'] );
            $post_excerpt   = trim( $_POST['post_excerpt'] );
            $price          = floatval( $_POST['_regular_price'] );
            $featured_image = absint( $_POST['feat_image_id'] );

            if ( empty( $post_title ) ) {

                $errors[] = __( 'Please enter product title', 'dokan' );
            }

            if( dokan_get_option( 'product_category_style', 'dokan_selling', 'single' ) == 'single' ) {
                $product_cat    = intval( $_POST['product_cat'] );
                if ( $product_cat < 0 ) {
                    $errors[] = __( 'Please select a category', 'dokan' );
                }
            } else {
                if( !isset( $_POST['product_cat'] ) && empty( $_POST['product_cat'] ) ) {
                    $errors[] = __( 'Please select AT LEAST ONE category', 'dokan' );
                }
            }
            if($price <= 0){
                $errors[] = __( 'Please add a price', 'dokan' );
            }

            self::$errors = apply_filters( 'dokan_can_add_product', $errors );

            if ( !self::$errors ) {

                $product_status = dokan_get_new_post_status();
                $post_data = apply_filters( 'dokan_insert_product_post_data', array(
                        'post_type'    => 'product',
                        'post_status'  => $product_status,
                        'post_title'   => $post_title,
                        'post_content' => $post_content,
                        'post_excerpt' => $post_excerpt,
                    ) );

                $product_id = wp_insert_post( $post_data );

                if ( $product_id ) {

                    /** set images **/
                    if ( $featured_image ) {
                        set_post_thumbnail( $product_id, $featured_image );
                    }

                    if( isset( $_POST['product_tag'] ) && !empty( $_POST['product_tag'] ) ) {
                        $tags_ids = array_map( 'intval', (array)$_POST['product_tag'] );
                        wp_set_object_terms( $product_id, $tags_ids, 'product_tag' );
                    }

                    /** set product category * */
                    if( dokan_get_option( 'product_category_style', 'dokan_selling', 'single' ) == 'single' ) {
                        wp_set_object_terms( $product_id, (int) $_POST['product_cat'], 'product_cat' );
                    } else {
                        if( isset( $_POST['product_cat'] ) && !empty( $_POST['product_cat'] ) ) {
                            $cat_ids = array_map( 'intval', (array)$_POST['product_cat'] );
                            wp_set_object_terms( $product_id, $cat_ids, 'product_cat' );
                        }
                    }
                    if ( isset( $_POST['product-type'] ) ) {
                        wp_set_object_terms( $product_id, $_POST['product-type'], 'product_type' );
                    } else {
                        /** Set Product type by default simple */
                        wp_set_object_terms( $product_id, 'simple', 'product_type' );
                    }
                    update_post_meta( $product_id, '_regular_price', $price );
                    update_post_meta( $product_id, '_sale_price', '' );
                    update_post_meta( $product_id, '_price', $price );
                    update_post_meta( $product_id, '_visibility', 'visible' );

                    do_action( 'dokan_new_product_added', $product_id, $post_data );

                    if ( dokan_get_option( 'product_add_mail', 'dokan_general', 'on' ) == 'on' ) {
                        Dokan_Email::init()->new_product_added( $product_id, $product_status );
                    }

                    wp_redirect( dokan_edit_product_url( $product_id ) );
                    exit;
                }
            }
        }


        if ( isset( $_GET['product_id'] ) ) {
            $post_id = intval( $_GET['product_id'] );
        } else {
            global $post, $product;

            if ( !empty( $post ) ) {
                $post_id = $post->ID;
            }
        }


        if ( isset( $_POST['update_product'] ) && wp_verify_nonce( $_POST['dokan_edit_product_nonce'], 'dokan_edit_product' ) ) {
            $post_title     = trim( $_POST['post_title'] );
            if ( empty( $post_title ) ) {

                $errors[] = __( 'Please enter product title', 'dokan' );
            }

            if( dokan_get_option( 'product_category_style', 'dokan_selling', 'single' ) == 'single' ) {
                $product_cat    = intval( $_POST['product_cat'] );
                if ( $product_cat < 0 ) {
                    $errors[] = __( 'Please select a category', 'dokan' );
                }
            } else {
                if( !isset( $_POST['product_cat'] ) && empty( $_POST['product_cat'] ) ) {
                    $errors[] = __( 'Please select AT LEAST ONE category', 'dokan' );
                }
            }

            self::$errors = apply_filters( 'dokan_can_edit_product', $errors );

            if ( !self::$errors ) {

                $product_info = array(
                    'ID'             => $post_id,
                    'post_title'     => sanitize_text_field( $_POST['post_title'] ),
                    'post_content'   => $_POST['post_content'],
                    'post_excerpt'   => $_POST['post_excerpt'],
                    'post_status'    => isset( $_POST['post_status'] ) ? $_POST['post_status'] : 'pending',
                    'comment_status' => isset( $_POST['_enable_reviews'] ) ? 'open' : 'closed'
                );

                $is_lot_discount     = isset( $_POST['_is_lot_discount'] ) ? $_POST['_is_lot_discount'] : 'no';
                if ( $is_lot_discount == 'yes' ) {
                    $lot_discount_quantity = isset($_POST['_lot_discount_quantity']) ? $_POST['_lot_discount_quantity'] : 0;
                    $lot_discount_amount   = isset($_POST['_lot_discount_amount']) ? $_POST['_lot_discount_amount'] : 0;
                    if ( $lot_discount_quantity == '0' || $lot_discount_amount == '0' ) {
                        update_post_meta( $post_id, '_lot_discount_quantity', $lot_discount_quantity);
                        update_post_meta( $post_id, '_lot_discount_amount', $lot_discount_amount);
                        update_post_meta( $post_id, '_is_lot_discount', 'no');
                    } else {
                        update_post_meta( $post_id, '_lot_discount_quantity', $lot_discount_quantity);
                        update_post_meta( $post_id, '_lot_discount_amount', $lot_discount_amount);
                        update_post_meta( $post_id, '_is_lot_discount', $is_lot_discount);
                    }
                } else if ( $is_lot_discount == 'no' ) {
                    update_post_meta( $post_id, '_lot_discount_quantity', 0);
                    update_post_meta( $post_id, '_lot_discount_amount', 0);
                    update_post_meta( $post_id, '_is_lot_discount', 'no');
                }

                wp_update_post( $product_info );

                /** Set Product tags */
                if( isset( $_POST['product_tag'] ) ) {
                    $tags_ids = array_map( 'intval', (array)$_POST['product_tag'] );
                } else {
                    $tags_ids = array();
                }
                wp_set_object_terms( $post_id, $tags_ids, 'product_tag' );


                /** set product category * */

                if( dokan_get_option( 'product_category_style', 'dokan_selling', 'single' ) == 'single' ) {
                    wp_set_object_terms( $post_id, (int) $_POST['product_cat'], 'product_cat' );
                } else {
                    if( isset( $_POST['product_cat'] ) && !empty( $_POST['product_cat'] ) ) {
                        $cat_ids = array_map( 'intval', (array)$_POST['product_cat'] );
                        wp_set_object_terms( $post_id, $cat_ids, 'product_cat' );
                    }
                }
                if ( isset( $_POST['product-type'] ) ) {
                    wp_set_object_terms( $product_id, $_POST['product-type'], 'product_type' );
                } else {
                    wp_set_object_terms( $post_id, 'simple', 'product_type' );
                }
                /**  Process all variation products meta */
                dokan_process_product_meta( $post_id );

                /** set images **/
                $featured_image = absint( $_POST['feat_image_id'] );
                if ( $featured_image ) {
                    set_post_thumbnail( $post_id, $featured_image );
                }
                
                $edit_url = dokan_edit_product_url( $post_id );
                wp_redirect( add_query_arg( array( 'message' => 'success' ), $edit_url ) );
                exit;
            }
        }


    }

    /**
     * Handle delete product link
     *
     * @return void
     */
    function handle_delete_product() {

        if ( ! is_user_logged_in() ) {
            return;
        }

        if ( ! dokan_is_user_seller( get_current_user_id() ) ) {
            return;
        }

        dokan_delete_product_handler();
    }
    
    
    
    
 
    
    
    

}
